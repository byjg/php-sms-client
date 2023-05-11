<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;
use ByJG\Util\Uri;

interface ProviderInterface
{
    public static function schema();

    public function setUp(Uri $uri);

    public function send($to, Message $envelope): ReturnObject;
}
