<?php

namespace ByJG\SmsClient;

class ReturnObject
{
    protected $sent = false;
    protected $rawMessage = null;

    public function __construct($sent, $rawMessage)
    {
        $this->sent = $sent;
        $this->rawMessage = $rawMessage;
    }

    public function isSent()
    {
        return $this->sent;
    }

    public function getRawMessage()
    {
        return $this->rawMessage;
    }
}
