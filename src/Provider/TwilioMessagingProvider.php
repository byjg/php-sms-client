<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\HydratePhone;
use ByJG\Util\Uri;
use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;

class TwilioMessagingProvider implements ProviderInterface
{
    protected Uri $uri;

    public static function schema() {
        return 'twilio';
    }

    public function setUp(Uri $uri) {
        $this->uri = $uri;
    }

    public function send($to, Message $envelope): ReturnObject
    {
        if (empty($envelope->getSender())) {
            throw new \Exception("The 'sender' is required");
        }

        $to = HydratePhone::phone($to)->withPlusPrefix()->hydrate();
        $from = HydratePhone::phone($envelope->getSender())->withPlusPrefix()->hydrate();

        $request = \ByJG\Util\Helper\RequestFormUrlEncoded::build(
            new Uri("https://api.twilio.com/2010-04-01/Accounts/" . $this->uri->getUsername() . "/Messages.json"),
            [
                'To' => $to,
                'From' => $from,
                "Body" => $envelope->getBody()
            ]
        );
        $response = \ByJG\Util\HttpClient::getInstance()
            ->withCurlOption(CURLOPT_USERPWD, $this->uri->getUsername() . ":" . $this->uri->getPassword())
            ->sendRequest($request);

        return new ReturnObject($response->getStatusCode() == 201 || $response->getStatusCode() == 200, $response->getBody()->getContents());
    }
}