<?php

namespace Tests;

use ByJG\SmsClient\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testGetBody(): void
    {
        $message = new Message("body");
        $this->assertEquals("body", $message->getBody());
        $this->assertEquals([], $message->getProperties());
    }

    public function testGetBodyWithProperties(): void
    {
        $message = new Message("body");
        $message->withProperties(["key" => "value"]);
        $this->assertEquals("body", $message->getBody());
        $this->assertEquals(["key" => "value"], $message->getProperties());
    }

    public function testGetBodyWithProperty(): void
    {
        $message = new Message("body");
        $message->withProperty("key", "value");
        $this->assertEquals("body", $message->getBody());
        $this->assertEquals(["key" => "value"], $message->getProperties());
    }

    public function testGetSender(): void
    {
        $message = new Message("body");
        $message->withSender("sender");
        $this->assertEquals("body", $message->getBody());
        $this->assertEquals("sender", $message->getSender());
    }
}
