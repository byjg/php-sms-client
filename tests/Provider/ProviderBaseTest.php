<?php

namespace Tests\Provider;

use ByJG\SmsClient\Message;
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;
use ByJG\WebRequest\Exception\MessageException;
use ByJG\SmsClient\Provider\ProviderBase;
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\ReturnObject;
use ByJG\Util\Uri;
use ByJG\WebRequest\HttpPromise;
use ByJG\WebRequest\HttpPromiseState;
use ByJG\WebRequest\MockClient;
use ByJG\WebRequest\Psr7\MemoryStream;
use ByJG\WebRequest\Psr7\Request;
use ByJG\WebRequest\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class ProviderBaseTest extends TestCase
{
    /**
     * A provider that sends with the client passed
     */
    protected function provider(ClientInterface $client): ProviderBase
    {
        return new class($client) extends ProviderBase {
            public function __construct(private ClientInterface $client)
            {
            }

            public static function schema(): array
            {
                return ['test'];
            }

            public function setUp(Uri $uri): void
            {
            }

            public function send(string|Phone $to, Message $envelope): ReturnObject
            {
                return $this->sendAsync($to, $envelope)->await();
            }

            public function sendAsync(string|Phone $to, Message $envelope): HttpPromise
            {
                return $this->sendHttpRequestAsync($this->client, Request::getInstance(new Uri('http://localhost/sms')))
                    ->then(fn(ResponseInterface $response) => new ReturnObject(
                        $response->getStatusCode() == 200,
                        (string)$response->getBody()
                    ));
            }
        };
    }

    /**
     * A provider written before sendAsync(): it only implements send()
     */
    protected function syncProvider(?\Throwable $error = null): ProviderBase
    {
        return new class($error) extends ProviderBase {
            public array $sent = [];

            public function __construct(private ?\Throwable $error)
            {
            }

            public static function schema(): array
            {
                return ['sync'];
            }

            public function setUp(Uri $uri): void
            {
            }

            public function send(string|Phone $to, Message $envelope): ReturnObject
            {
                if ($this->error !== null) {
                    throw $this->error;
                }
                $this->sent[] = $to;
                return new ReturnObject(true, 'sent to ' . $to);
            }
        };
    }

    public function testDefaultSendAsyncSendsAtOnce(): void
    {
        $provider = $this->syncProvider();

        $promise = $provider->sendAsync('+5521912345678', new Message('test'));
        $this->assertEquals(['+5521912345678'], $provider->sent);
        $this->assertEquals(HttpPromiseState::Fulfilled, $promise->getState());
        $this->assertEquals('sent to +5521912345678', $promise->await()->getRawMessage());
    }

    public function testDefaultSendAsyncRejects(): void
    {
        $promise = $this->syncProvider(new RuntimeException('provider down'))
            ->sendAsync('+5521912345678', new Message('test'));

        // sendAsync() does not throw: the error rejects the promise
        $this->assertEquals(HttpPromiseState::Rejected, $promise->getState());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('provider down');
        $promise->await();
    }

    public function testInvalidPhoneRejects(): void
    {
        $promise = (new ByJGSmsProviderMock())->sendAsync(Phone::phone("+12129121234", new USPhoneFormat()), new Message('test'));
        $this->assertEquals(HttpPromiseState::Rejected, $promise->getState());
        $this->expectException(MessageException::class);
        $promise->await();
    }

    public function testSendAsync(): void
    {
        ProviderFactory::registerProvider(ByJGSmsProviderMock::class);
        ProviderFactory::registerServices("byjg://user:password@default", "+55");

        $promise = ProviderFactory::createAndSendAsync("+5521912345678", new Message("This is a test message"));
        $this->assertInstanceOf(HttpPromise::class, $promise);

        $result = $promise->await();
        $this->assertTrue($result->isSent());
        $this->assertEquals('OK|0,Delivery', $result->getRawMessage());
    }

    public function testHttpClientIsSentAsync(): void
    {
        // MockClient is an HttpClient: its sendAsync() is used
        $client = new MockClient((new Response(200))->withBody(new MemoryStream('sent')));

        $result = $this->provider($client)->send('+5521912345678', new Message('test'));
        $this->assertTrue($result->isSent());
        $this->assertEquals('sent', $result->getRawMessage());
        $this->assertEquals('http://localhost/sms', (string)$client->getRequestedObject()->getUri());
    }

    public function testOtherPsr18Client(): void
    {
        $client = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return (new Response(500))->withBody(new MemoryStream('failed'));
            }
        };

        $promise = $this->provider($client)->sendAsync('+5521912345678', new Message('test'));
        // Without the ByJG HttpClient the request is sent at once
        $this->assertEquals(HttpPromiseState::Fulfilled, $promise->getState());
        $this->assertFalse($promise->await()->isSent());
        $this->assertEquals('failed', $promise->await()->getRawMessage());
    }

    public function testOtherPsr18ClientErrorRejects(): void
    {
        $client = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new class('Connection refused') extends RuntimeException implements ClientExceptionInterface {
                };
            }
        };

        $promise = $this->provider($client)->sendAsync('+5521912345678', new Message('test'));
        $this->assertEquals(HttpPromiseState::Rejected, $promise->getState());

        $this->expectException(ClientExceptionInterface::class);
        $this->expectExceptionMessage('Connection refused');
        $this->provider($client)->send('+5521912345678', new Message('test'));
    }
}
