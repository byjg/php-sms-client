<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\BrazilianPhoneFormat;
use ByJG\WebRequest\Exception\MessageException;
use ByJG\WebRequest\Exception\NetworkException;
use ByJG\WebRequest\Exception\RequestException;
use ByJG\WebRequest\Helper\RequestFormUrlEncoded;
use ByJG\WebRequest\HttpClient;
use ByJG\Util\Uri;
use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;

class ByJGSmsProvider extends ProviderBase
{
    protected Uri $uri;

    public static function schema(): array
    {
        return ['byjg'];
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
    public function send(string|Phone $to, Message $envelope): ReturnObject
    {
        if (is_string($to)) {
            $to = Phone::phone($to, new BrazilianPhoneFormat());
        }

        if (!($to->getPhoneFormat() instanceof BrazilianPhoneFormat)) {
            throw new MessageException("The phone number must be in Brazilian format");
        }

        $to = $to->withNoCountryCode()->withNoPlusPrefix()->hydrate();
        $ddd = substr($to, 0, 2);
        $number = substr($to, 2, 9);

        $request = RequestFormUrlEncoded::build(
            new Uri("https://www.byjg.com.br/ws/sms?httpmethod=enviarsms"),
            [
                'ddd' => $ddd,
                'celular' => $number,
                "mensagem" => $envelope->getBody(),
                "usuario" => $this->uri->getUsername(),
                "senha" => $this->uri->getPassword(),
            ]
        );

        $response = $this->sendHttpRequest(HttpClient::getInstance(), $request);

        $result = $response->getBody()->getContents();

        $resultParts = explode("|", $result . "|");
        $resultStatusParts = explode(",", $resultParts[1]);
        return new ReturnObject($resultParts[0] == "OK" && $resultStatusParts[0] == '0', $result);
    }
}