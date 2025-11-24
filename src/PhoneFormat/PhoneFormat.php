<?php

namespace ByJG\SmsClient\PhoneFormat;

abstract class PhoneFormat
{
    protected string $countryCode;

    /**
     * @var non-empty-string
     */
    protected string $validateRegex;

    /**
     * @var non-empty-string
     */
    protected string $formatRegex;

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return non-empty-string
     */
    public function getValidateRegex(): string
    {
        return $this->validateRegex;
    }

    /**
     * @return non-empty-string
     */
    public function getFormatRegex(): string
    {
        return $this->formatRegex;
    }

}