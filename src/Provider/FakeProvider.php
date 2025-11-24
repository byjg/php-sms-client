<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Phone;
use ByJG\Util\Uri;
use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;

final class FakeProvider implements ProviderInterface
{

    #[\Override]
    public static function schema(): array
    {
        return ["fakesender"];
    }

    #[\Override]
    public function setUp(Uri $uri): void
    { }

    #[\Override]
    public function send(string|Phone $to, Message $envelope): ReturnObject
    {
        return new ReturnObject(true, "OK");
    }
}