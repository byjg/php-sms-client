<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\HydratePhone;
use ByJG\WebRequest\Exception\MessageException;
use ByJG\WebRequest\Exception\NetworkException;
use ByJG\WebRequest\Exception\RequestException;
use ByJG\WebRequest\Helper\RequestFormUrlEncoded;
use ByJG\WebRequest\HttpClient;
use ByJG\Util\Uri;
use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;
use Exception;

class TwilioMessagingProvider implements ProviderInterface
{
    protected Uri $uri;

    public static function schema(): array
    {
        return ['twilio'];
    }

    public function setUp(Uri $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @throws NetworkException
     * @throws RequestException
     * @throws MessageException
     * @throws Exception
     */
    public function send(string $to, Message $envelope): ReturnObject
    {
        if (empty($envelope->getSender())) {
            throw new Exception("The 'sender' is required");
        }

        $to = HydratePhone::phone($to)->withPlusPrefix()->hydrate();
        $from = HydratePhone::phone($envelope->getSender())->withPlusPrefix()->hydrate();

        $request = RequestFormUrlEncoded::build(
            new Uri("https://api.twilio.com/2010-04-01/Accounts/" . $this->uri->getUsername() . "/Messages.json"),
            [
                'To' => $to,
                'From' => $from,
                "Body" => $envelope->getBody()
            ]
        );
        $response = HttpClient::getInstance()
            ->withCurlOption(CURLOPT_USERPWD, $this->uri->getUsername() . ":" . $this->uri->getPassword())
            ->sendRequest($request);

        return new ReturnObject($response->getStatusCode() == 201 || $response->getStatusCode() == 200, $response->getBody()->getContents());
    }
}