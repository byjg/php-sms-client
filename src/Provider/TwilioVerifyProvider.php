<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\HydratePhone;
use ByJG\Util\Exception\MessageException;
use ByJG\Util\Exception\NetworkException;
use ByJG\Util\Exception\RequestException;
use ByJG\Util\Helper\RequestFormUrlEncoded;
use ByJG\Util\HttpClient;
use ByJG\Util\Uri;
use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;

class TwilioVerifyProvider implements ProviderInterface
{
    protected Uri $uri;

    public static function schema(): array
    {
        return ['twilio_verify'];
    }

    public function setUp(Uri $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @throws NetworkException
     * @throws RequestException
     * @throws MessageException
     */
    public function send(string $to, Message $envelope): ReturnObject {
        if (empty($envelope->getBody())) {
            return $this->sendVerify($to);
        } else {
            return $this->sendVerifyCheck($to, $envelope);
        }

    }

    /**
     * @throws NetworkException
     * @throws RequestException
     * @throws MessageException
     */
    protected function sendVerify($to): ReturnObject
    {
        $request = RequestFormUrlEncoded::build(
            new Uri("https://verify.twilio.com/v2/Services/" . $this->uri->getHost() . "/Verifications"),
            [
                'To' => HydratePhone::phone($to)->withPlusPrefix()->hydrate(),
                "Channel" => "sms"
            ]
        );
        $response = HttpClient::getInstance()
            ->withCurlOption(CURLOPT_USERPWD, $this->uri->getUsername() . ":" . $this->uri->getPassword())
            ->sendRequest($request);

        return new ReturnObject($response->getStatusCode() == 201, $response->getBody()->getContents());
    }

    /**
     * @throws NetworkException
     * @throws RequestException
     * @throws MessageException
     */
    protected function sendVerifyCheck($to, Message $envelope): ReturnObject
    {
        $request = RequestFormUrlEncoded::build(
            new Uri("https://verify.twilio.com/v2/Services/" . $this->uri->getHost() . "/VerificationCheck"),
            [
                'To' => HydratePhone::phone($to)->withPlusPrefix()->hydrate(),
                "Code" => $envelope->getBody()
            ]
        );
        $response = HttpClient::getInstance()
            ->withCurlOption(CURLOPT_USERPWD, $this->uri->getUsername() . ":" . $this->uri->getPassword())
            ->sendRequest($request);

        return new ReturnObject($response->getStatusCode() == 200, $response->getBody()->getContents());
   }
}