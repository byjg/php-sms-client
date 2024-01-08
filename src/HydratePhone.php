<?php

namespace ByJG\SmsClient;

use InvalidArgumentException;

class HydratePhone
{
    protected string $phone;

    protected function __construct(string $phone)
    {
        $this->phone = $phone;
    }

    public static function phone(string $phone): HydratePhone
    {
        $phone = preg_replace('/[^+0-9]/', '', $phone);

        return new HydratePhone($phone);
    }

    public static function hydrateUsNumber(string $phone): string
    {
        return HydratePhone::phone($phone)
            ->withPlusPrefix()
            ->withUSCountryCode()
            ->hydrate();
    }

    public static function hydrateBrazilNumber($phone): string
    {
        return HydratePhone::phone($phone)
            ->withPlusPrefix()
            ->withBrazilCountryCode()
            ->hydrate();
    }

    public function withPlusPrefix(): self
    {
        // add + to the beginning of the phone number if not exists
        if (!str_starts_with($this->phone, '+')) {
            $this->phone = '+' . $this->phone;
        }
        return $this;
    }

    public function withNoPlusPrefix(): self
    {
        // remove + from the beginning of the phone number if exists
        if (str_starts_with($this->phone, '+')) {
            $this->phone = substr($this->phone, 1);
        }
        return $this;
    }

    public function withUSCountryCode(): self
    {
        // add '1' to the beginning of the phone number if not exists. Check if there is a plus sign in the beginning
        if ($this->phone[0] === '+') {
            if ($this->phone[1] !== '1') {
                $this->phone = '+1' . substr($this->phone, 1);
            }
        } else if ($this->phone[0] !== '1') {
            $this->phone = '1' . $this->phone;
        }

        return $this;
    }

    public function withBrazilCountryCode(): self
    {
        // add '55' area code to the beginning of the phone number if not exists. Check if there is a plus sign in the beginning
        if ($this->phone[0] === '+') {
            if (substr($this->phone, 1, 2) !== '55') {
                $this->phone = '+55' . substr($this->phone, 1);
            }
        } else if (!str_starts_with($this->phone, '55')) {
            $this->phone = '55' . $this->phone;
        }

        return $this;
    }

    public function validateUSNumber(): self
    {
        // validate US phone number
        if (!preg_match('/^(\+?1)?[2-9]\d{2}[2-9](?!11)\d{6}$/', $this->phone)) {
            throw new InvalidArgumentException('Invalid US phone number');
        }
        return $this;
    }

    public function validateBrazilNumber(): self
    {
        // validate Brazil phone number
        if (!preg_match('/^(\+?55)?[1-9][1-9][1-9]\d{8}$/', $this->phone)) {
            throw new InvalidArgumentException('Invalid Brazil phone number');
        }
        return $this;
    }

    public function validateUKNumber(): self
    {
        // validate UK phone number
        if (!preg_match('/^(\+?44)?[1-9]\d{9}$/', $this->phone)) {
            throw new InvalidArgumentException('Invalid UK phone number');
        }
        return $this;
    }

    public function hydrate(): string
    {
        return $this->phone;
    }

}