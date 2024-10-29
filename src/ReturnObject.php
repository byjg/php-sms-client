<?php

namespace ByJG\SmsClient;

class ReturnObject
{
    protected bool $sent = false;
    protected mixed $rawMessage = null;

    public function __construct(bool $sent, mixed $rawMessage)
    {
        $this->sent = $sent;
        $this->rawMessage = $rawMessage;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function getRawMessage(): mixed
    {
        return $this->rawMessage;
    }
}
