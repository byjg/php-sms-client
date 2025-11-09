---
sidebar_position: 2
---

# Phone Formatting

The SMS Client library provides powerful phone number formatting and validation capabilities through the `Phone` class and country-specific `PhoneFormat` classes.

## Overview

The `Phone` class allows you to:
- Validate phone numbers
- Format phone numbers according to country standards
- Add or remove country codes
- Add or remove the + prefix

## Basic Phone Formatting

```php
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;

// Create a phone number
$phone = Phone::phone("+12345678900", new USPhoneFormat());

// Hydrate (normalize) the number
$normalized = $phone->hydrate(); // Returns: +12345678900

// Format the number
$formatted = $phone->format(); // Returns: +1(234)567-8900
```

## Phone Format Options

### With Country Code and Plus Prefix (Default)

```php
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;

$phone = Phone::phone("2345678900", new USPhoneFormat())
    ->withPlusPrefix()
    ->withCountryCode();

$result = $phone->hydrate(); // Returns: +12345678900
$formatted = $phone->format(); // Returns: +1(234)567-8900
```

### Without Plus Prefix

```php
$phone = Phone::phone("+12345678900", new USPhoneFormat())
    ->withNoPlusPrefix()
    ->withCountryCode();

$result = $phone->hydrate(); // Returns: 12345678900
$formatted = $phone->format(); // Returns: 1(234)567-8900
```

### Without Country Code

```php
$phone = Phone::phone("+12345678900", new USPhoneFormat())
    ->withNoPlusPrefix()
    ->withNoCountryCode();

$result = $phone->hydrate(); // Returns: 2345678900
$formatted = $phone->format(); // Returns: (234)567-8900
```

## Phone Validation

The `Phone` class can validate phone numbers according to country-specific rules:

```php
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;

$phone = Phone::phone("2345678900", new USPhoneFormat());

// Validate with exception on failure
try {
    $phone->validate(); // Throws InvalidArgumentException if invalid
    echo "Phone number is valid";
} catch (\InvalidArgumentException $e) {
    echo "Invalid phone number: " . $e->getMessage();
}

// Validate without exception
$isValid = $phone->validate(throwException: false); // Returns true or false
if ($isValid) {
    echo "Phone number is valid";
}
```

## Available Phone Formats

### US Phone Format

For United States phone numbers (country code: +1):

```php
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;

$format = new USPhoneFormat();
// Country code: 1
// Format: +1(234)567-8900
// Validates: 10-digit numbers
```

### Brazilian Phone Format

For Brazilian phone numbers (country code: +55):

```php
use ByJG\SmsClient\PhoneFormat\BrazilianPhoneFormat;

$format = new BrazilianPhoneFormat();
// Country code: 55
// Format: +55(21)91234-5678
// Validates: 11-digit numbers (2-digit area code + 9-digit number)
```

## Creating Custom Phone Formats

To create a custom phone format, implement the `PhoneFormat` interface:

```php
use ByJG\SmsClient\PhoneFormat\PhoneFormat;

class CustomPhoneFormat implements PhoneFormat
{
    public function getCountryCode(): string
    {
        // Return the country code (e.g., "44" for UK)
        return "44";
    }

    public function getValidateRegex(): string
    {
        // Return regex to validate the full number including country code
        return '/^44[0-9]{10}$/';
    }

    public function getFormatRegex(): string
    {
        // Return regex to format the number
        // Groups: $1=country code, $2=area, $3=first part, $4=second part
        return '/^(\+?44)([0-9]{4})([0-9]{3})([0-9]{3})$/';
    }
}
```

## Practical Examples

### Accepting Various Input Formats

The `Phone` class can normalize different input formats:

```php
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;

$format = new USPhoneFormat();

// All of these produce the same result
$inputs = [
    '+1(234)567-8900',
    '(234)567-8900',
    '+12345678900',
    '12345678900',
    '2345678900'
];

foreach ($inputs as $input) {
    $phone = Phone::phone($input, $format);
    echo $phone->hydrate(); // All output: +12345678900
}
```

### Using Phone Objects with Providers

You can pass `Phone` objects directly to providers:

```php
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\Message;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;
use ByJG\SmsClient\Provider\ProviderFactory;

// Create formatted phone number
$recipient = Phone::phone("2345678900", new USPhoneFormat())
    ->withPlusPrefix()
    ->withCountryCode();

$sender = Phone::phone("3217654321", new USPhoneFormat())
    ->withPlusPrefix()
    ->withCountryCode();

// Send using Phone objects
$response = ProviderFactory::createAndSend(
    $recipient,
    (new Message("Hello!"))->withSender($sender)
);
```
