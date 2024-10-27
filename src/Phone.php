<?php

namespace ByJG\SmsClient;

use ByJG\SmsClient\PhoneFormat\PhoneFormat;
use InvalidArgumentException;

class Phone
{
    protected string $number;

    protected PhoneFormat $phoneFormat;

    protected bool $plusPrefix = true;

    protected bool $countryCode = true;

    protected function __construct(string $number, PhoneFormat $phoneFormat)
    {
        $this->number = preg_replace('/[^0-9]/', '', $number);;
        $this->phoneFormat = $phoneFormat;
    }

    public static function phone(string $number, PhoneFormat $phoneFormat): Phone
    {
        return new Phone($number, $phoneFormat);
    }

    public function withCountryCode(): self
    {
        $this->countryCode = true;
        return $this;
    }

    public function withNoCountryCode(): self
    {
        $this->countryCode = false;
        return $this;
    }

    public function withPlusPrefix(): self
    {
        $this->plusPrefix = true;
        return $this;
    }

    public function withNoPlusPrefix(): self
    {
        $this->plusPrefix = false;
        return $this;
    }

    public function validate(bool $throwException = true): bool
    {
        if (!preg_match($this->phoneFormat->getValidateRegex(), $this->number)) {
            if ($throwException) {
                throw new InvalidArgumentException('Invalid phone number');
            }
            return false;
        }
        return true;
    }

    public function hydrate(): string
    {
        $this->validate();
        $phone = $this->number;

        if ($this->countryCode) {
            if (!str_starts_with($phone, $this->phoneFormat->getCountryCode())) {
                $phone = $this->phoneFormat->getCountryCode() . $phone;
            }

            if ($this->plusPrefix) {
                $phone = '+' . $phone;
            }
        } elseif (str_starts_with($phone, $this->phoneFormat->getCountryCode())) {
            $phone = substr($phone, strlen($this->phoneFormat->getCountryCode()));
        }

        return $phone;
    }

    public function format(): string
    {
        $phone = $this->hydrate();
        return preg_replace($this->phoneFormat->getFormatRegex(), '$1($2)$3-$4', $phone);
    }

    public function getPhoneFormat(): PhoneFormat
    {
        return $this->phoneFormat;
    }
}