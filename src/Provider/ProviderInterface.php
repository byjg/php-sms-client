<?php

namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Phone;
use ByJG\SmsClient\Message;
use ByJG\SmsClient\ReturnObject;
use ByJG\Util\Uri;
use ByJG\WebRequest\HttpPromise;

interface ProviderInterface
{
    public static function schema(): array;

    public function setUp(Uri $uri): void;

    public function send(string|Phone $to, Message $envelope): ReturnObject;

    /**
     * Send without waiting: the promise is fulfilled with the ReturnObject, or rejected with any error
     * (it does not throw).
     */
    public function sendAsync(string|Phone $to, Message $envelope): HttpPromise;
}
