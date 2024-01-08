<?php

namespace ByJG\SmsClient;

class Message
{
    const MAX_LENGTH = 160;
    const ALLOW_UNICODE = false;
    const SMS = 'sms';
    const MMS = 'mms';

    protected string $body;

    protected ?string $sender = null;

    protected array $properties = [];

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    public function withSender(?string $sender)
    {
        $this->sender = $sender;
        return $this;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $property, ?string $default = null)
    {
        return $this->properties[$property] ?? $default;
    }

    public function withProperty(string $header, string $value): self
    {
        $this->properties[$header] = $value;
        return $this;
    }

    public function withProperties(array $properties): self
    {
        $this->properties = $properties;
        return $this;
    }

    public static function create(string $body): self
    {
        return new Message($body);
    }
}
