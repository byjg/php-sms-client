<?php

namespace ByJG\SmsClient;

class Message
{
    const MAX_LENGTH = 160;
    const ALLOW_UNICODE = false;
    const SMS = 'sms';
    const MMS = 'mms';

    protected $body;

    protected $sender = null;

    protected $properties = [];

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function withSender($sender)
    {
        $this->sender = $sender;
        return $this;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function getProperty($property, $default = null)
    {
        return $this->properties[$property] ?? $default;
    }

    public function withProperty($header, $value)
    {
        $this->properties[$header] = $value;
        return $this;
    }

    public function withProperties(array $properties)
    {
        $this->properties = $properties;
        return $this;
    }

    public static function create($body)
    {
        return new Message($body);
    }
}
