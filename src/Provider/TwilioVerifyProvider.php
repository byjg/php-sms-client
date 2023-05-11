<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\HydratePhone;
use ByJG\Util\Uri;
use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;

class TwilioVerifyProvider implements ProviderInterface
{
    protected Uri $uri;

    public static function schema() {
        return 'twilio_verify';
    }

    public function setUp(Uri $uri) {
        $this->uri = $uri;
    }

    public function send($to, Message $envelope): ReturnObject {
        if (empty($envelope->getBody())) {
            return $this->sendVerify($to, $envelope);
        } else {
            return $this->sendVerifyCheck($to, $envelope);
        }

    }

    protected function sendVerify($to, Message $envelope): ReturnObject
    {
        $request = \ByJG\Util\Helper\RequestFormUrlEncoded::build(
            new Uri("https://verify.twilio.com/v2/Services/" . $this->uri->getHost() . "/Verifications"),
            [
                'To' => HydratePhone::phone($to)->withPlusPrefix()->hydrate(),
                "Channel" => "sms"
            ]
        );
        $response = \ByJG\Util\HttpClient::getInstance()
            ->withCurlOption(CURLOPT_USERPWD, $this->uri->getUsername() . ":" . $this->uri->getPassword())
            ->sendRequest($request);

        return new ReturnObject($response->getStatusCode() == 201, $response->getBody()->getContents());
    }

    protected function sendVerifyCheck($to, Message $envelope): ReturnObject
    {
        $request = \ByJG\Util\Helper\RequestFormUrlEncoded::build(
            new Uri("https://verify.twilio.com/v2/Services/" . $this->uri->getHost() . "/VerificationCheck"),
            [
                'To' => HydratePhone::phone($to)->withPlusPrefix()->hydrate(),
                "Code" => $envelope->getBody()
            ]
        );
        $response = \ByJG\Util\HttpClient::getInstance()
            ->withCurlOption(CURLOPT_USERPWD, $this->uri->getUsername() . ":" . $this->uri->getPassword())
            ->sendRequest($request);

        return new ReturnObject($response->getStatusCode() == 200, $response->getBody()->getContents());
   }
}