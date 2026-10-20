<?php

namespace Tests\Provider;

use ByJG\SmsClient\Provider\ByJGSmsProvider;
use ByJG\WebRequest\HttpPromise;
use ByJG\WebRequest\Psr7\MemoryStream;
use ByJG\WebRequest\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

final class ByJGSmsProviderMock extends ByJGSmsProvider
{
    #[\Override]
    protected function sendHttpRequestAsync(ClientInterface $client, RequestInterface $request): HttpPromise
    {
        return HttpPromise::resolve((new Response(200))
            ->withBody(new MemoryStream('OK|0,Delivery')));
    }

}