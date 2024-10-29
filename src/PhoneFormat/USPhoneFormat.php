<?php

namespace ByJG\SmsClient\PhoneFormat;

class USPhoneFormat extends PhoneFormat
{
    public function __construct()
    {
        $this->countryCode = '1';
        $this->validateRegex = '/^1[2-9]\d{2}[2-9](?!11)\d{6}$/';
        $this->formatRegex = '/^(\+?1)?(\d{3})(\d{3})(\d{4})$/';
    }

}