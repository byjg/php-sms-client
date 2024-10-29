<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Phone;
use ByJG\Util\Uri;
use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;

class FakeProvider implements ProviderInterface
{

    public static function schema(): array
    {
        return ["fakesender"];
    }

    public function setUp(Uri $uri): void
    { }

    public function send(string|Phone $to, Message $envelope): ReturnObject
    {
        return new ReturnObject(true, "OK");
    }
}