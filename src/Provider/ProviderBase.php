<?php

namespace ByJG\SmsClient\Provider;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class ProviderBase implements ProviderInterface
{
    protected function sendHttpRequest(ClientInterface $client, RequestInterface $request): ResponseInterface
    {
        return $client->sendRequest($request);
    }

}