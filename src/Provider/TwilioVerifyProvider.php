<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;
use ByJG\WebRequest\Exception\MessageException;
use ByJG\WebRequest\Exception\NetworkException;
use ByJG\WebRequest\Exception\RequestException;
use ByJG\WebRequest\Helper\RequestFormUrlEncoded;
use ByJG\WebRequest\HttpClient;
use ByJG\Util\Uri;
use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;

class TwilioVerifyProvider extends ProviderBase
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
    public function send(string|Phone $to, Message $envelope): ReturnObject {
        if (is_string($to)) {
            $to = Phone::phone($to, new USPhoneFormat());
        }

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
    protected function sendVerify(Phone $to): ReturnObject
    {
        $request = RequestFormUrlEncoded::build(
            new Uri("https://verify.twilio.com/v2/Services/" . $this->uri->getHost() . "/Verifications"),
            [
                'To' => $to->withPlusPrefix()->withCountryCode()->hydrate(),
                "Channel" => "sms"
            ]
        );

        $response = $this->sendHttpRequest(HttpClient::getInstance()->withCurlOption(CURLOPT_USERPWD, $this->uri->getUsername() . ":" . $this->uri->getPassword()), $request);

        return new ReturnObject($response->getStatusCode() == 201, $response->getBody()->getContents());
    }

    /**
     * @throws NetworkException
     * @throws RequestException
     * @throws MessageException
     */
    protected function sendVerifyCheck(Phone $to, Message $envelope): ReturnObject
    {
        $request = RequestFormUrlEncoded::build(
            new Uri("https://verify.twilio.com/v2/Services/" . $this->uri->getHost() . "/VerificationCheck"),
            [
                'To' => $to->withPlusPrefix()->withCountryCode()->hydrate(),
                "Code" => $envelope->getBody()
            ]
        );

        $response = $this->sendHttpRequest(HttpClient::getInstance()->withCurlOption(CURLOPT_USERPWD, $this->uri->getUsername() . ":" . $this->uri->getPassword()), $request);

        return new ReturnObject($response->getStatusCode() == 200, $response->getBody()->getContents());
   }
}
