<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Message;
use ByJG\SmsClient\Phone;
use ByJG\WebRequest\HttpClient;
use ByJG\WebRequest\HttpPromise;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class ProviderBase implements ProviderInterface
{
    /**
     * Sends at once with send(): the promise is already settled.
     * The HTTP providers override it to send in parallel.
     */
    #[\Override]
    public function sendAsync(string|Phone $to, Message $envelope): HttpPromise
    {
        return HttpPromise::try(fn() => $this->send($to, $envelope));
    }

    protected function sendHttpRequest(ClientInterface $client, RequestInterface $request): ResponseInterface
    {
        return $client->sendRequest($request);
    }

    /**
     * Send the request without waiting for the response: in parallel with the other requests sent
     * this way when the client is the ByJG HttpClient, otherwise the promise is already settled.
     */
    protected function sendHttpRequestAsync(ClientInterface $client, RequestInterface $request): HttpPromise
    {
        if ($client instanceof HttpClient) {
            return $client->sendAsync($request);
        }

        return HttpPromise::try(fn() => $client->sendRequest($request));
    }
}
