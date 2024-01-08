<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;
use ByJG\Util\Uri;

interface ProviderInterface
{
    public static function schema(): array;

    public function setUp(Uri $uri): void;

    public function send(string $to, Message $envelope): ReturnObject;
}
