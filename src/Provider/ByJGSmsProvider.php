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

class ByJGSmsProvider implements ProviderInterface
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
    public function send(string $to, Message $envelope): ReturnObject
    {
        $to = HydratePhone::phone($to)->withPlusPrefix()->withBrazilCountryCode()->validateBrazilNumber()->hydrate();
        $country = substr($to, 0, 3);
        $ddd = substr($to, 3, 2);
        $number = substr($to, 5, 9);

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

        $response = HttpClient::getInstance()
            ->sendRequest($request);

        $result = $response->getBody()->getContents();

        $resultParts = explode("|", $result . "|");
        $resultStatusParts = explode(",", $resultParts[1]);
        return new ReturnObject($resultParts[0] == "OK" && $resultStatusParts[0] == '0', $result);
    }
}