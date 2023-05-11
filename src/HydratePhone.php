<?php

namespace ByJG\SmsClient;

use InvalidArgumentException;

class HydratePhone
{
    protected $phone;

    protected function __construct($phone)
    {
        $this->phone = $phone;
    }

    public static function phone($phone): HydratePhone
    {
        $phone = preg_replace('/[^+0-9]/', '', $phone);

        return new HydratePhone($phone);
    }

    public static function hydrateUsNumber($phone)
    {
        return HydratePhone::phone($phone)
            ->withPlusPrefix()
            ->withUSCountryCode()
            ->hydrate();
    }

    public static function hydrateBrazilNumber($phone)
    {
        return HydratePhone::phone($phone)
            ->withPlusPrefix()
            ->withBrazilCountryCode()
            ->hydrate();
    }

    public function withPlusPrefix()
    {
        // add + to the beginning of the phone number if not exists
        if (substr($this->phone, 0, 1) !== '+') {
            $this->phone = '+' . $this->phone;
        }
        return $this;
    }

    public function withNoPlusPrefix()
    {
        // remove + from the beginning of the phone number if exists
        if (substr($this->phone, 0, 1) === '+') {
            $this->phone = substr($this->phone, 1);
        }
        return $this;
    }

    public function withUSCountryCode()
    {
        // add '1' to the beginning of the phone number if not exists. Check if there us a plus sign in the beginning
        if ($this->phone[0] === '+') {
            if ($this->phone[1] !== '1') {
                $this->phone = '+1' . substr($this->phone, 1);
            }
        } else if ($this->phone[0] !== '1') {
            $this->phone = '1' . $this->phone;
        }

        return $this;
    }

    public function withBrazilCountryCode()
    {
        // add '55' to the beginning of the phone number if not exists. Check if there us a plus sign in the beginning
        if ($this->phone[0] === '+') {
            if (substr($this->phone, 1, 2) !== '55') {
                $this->phone = '+55' . substr($this->phone, 1);
            }
        } else if (substr($this->phone, 0, 2) !== '55') {
            $this->phone = '55' . $this->phone;
        }

        return $this;
    }

    public function validateUSNumber()
    {
        // validate US phone number
        if (!preg_match('/^(\+?1)?[2-9]\d{2}[2-9](?!11)\d{6}$/', $this->phone)) {
            throw new InvalidArgumentException('Invalid US phone number');
        }
        return $this;
    }

    public function validateBrazilNumber()
    {
        // validate Brazil phone number
        if (!preg_match('/^(\+?55)?[1-9][1-9][1-9]\d{8}$/', $this->phone)) {
            throw new InvalidArgumentException('Invalid Brazil phone number');
        }
        return $this;
    }

    public function validateUKNumber()
    {
        // validate UK phone number
        if (!preg_match('/^(\+?44)?[1-9]\d{9}$/', $this->phone)) {
            throw new InvalidArgumentException('Invalid UK phone number');
        }
        return $this;
    }

    public function hydrate()
    {
        return $this->phone;
    }

}