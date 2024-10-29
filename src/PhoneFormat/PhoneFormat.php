<?php

namespace ByJG\SmsClient\PhoneFormat;

abstract class PhoneFormat
{
    protected string $countryCode;

    protected string $validateRegex;

    protected string $formatRegex;

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getValidateRegex(): string
    {
        return $this->validateRegex;
    }

    public function getFormatRegex(): string
    {
        return $this->formatRegex;
    }

}