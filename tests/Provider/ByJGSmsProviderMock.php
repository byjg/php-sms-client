<?php

namespace Tests\Provider;

use ByJG\SmsClient\Provider\ByJGSmsProvider;
use ByJG\WebRequest\Psr7\MemoryStream;
use ByJG\WebRequest\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ByJGSmsProviderMock extends ByJGSmsProvider
{
    #[\Override]
    protected function sendHttpRequest(ClientInterface $client, RequestInterface $request): ResponseInterface
    {
        return (new Response(200))
            ->withBody(new MemoryStream('OK|0,Delivery'));
    }

}