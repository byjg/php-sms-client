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
    public function send(string|Phone $to, Message $envelope): ReturnObject
    {
        if (empty($envelope->getSender())) {
            throw new Exception("The 'sender' is required");
        }

        if (is_string($to)) {
            $to = Phone::phone($to, new USPhoneFormat());
        }

        $toStr = $to->withPlusPrefix()->withCountryCode()->hydrate();
        $from = Phone::phone($envelope->getSender(), $to->getPhoneFormat())->withPlusPrefix()->withCountryCode()->hydrate();

        $request = RequestFormUrlEncoded::build(
            new Uri("https://api.twilio.com/2010-04-01/Accounts/" . $this->uri->getUsername() . "/Messages.json"),
            [
                'To' => $toStr,
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