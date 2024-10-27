<?php

namespace ByJG\SmsClient\PhoneFormat;

class BrazilianPhoneFormat extends PhoneFormat
{
    public function __construct()
    {
        $this->countryCode = '55';
        $this->validateRegex = '/^(\+?55)?[1-9][1-9][1-9]\d{8}$/';
        $this->formatRegex = '/^(\+?55)?([1-9][1-9])([1-9]\d{4})(\d{4})$/';
    }
}