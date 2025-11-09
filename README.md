# SMS Client

[![Build Status](https://github.com/byjg/php-sms-client/actions/workflows/phpunit.yml/badge.svg?branch=main)](https://github.com/byjg/php-sms-client/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](http://opensource.byjg.com)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-sms-client/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-sms-client.svg)](https://opensource.byjg.com/opensource/licensing.html)
[![GitHub release](https://img.shields.io/github/release/byjg/php-sms-client.svg)](https://github.com/byjg/php-sms-client/releases/)

A lightweight, extensible PHP library for sending SMS messages through multiple providers.

## Features

- **Low code** - Simple, intuitive API for sending SMS
- **Provider agnostic** - Support for multiple SMS providers
- **Extensible** - Easy to implement custom providers
- **Phone formatting** - Built-in phone number validation and formatting
- **Multi-provider support** - Route messages to different providers based on country codes

## Installation

```shell
composer require byjg/sms-client
```

## Quick Start

```php
use ByJG\SmsClient\Message;
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\Provider\TwilioMessagingProvider;
use ByJG\Uri\Uri;

// Register and create provider
ProviderFactory::registerProvider(TwilioMessagingProvider::class);
$provider = ProviderFactory::create(new Uri("twilio://$accountSid:$authToken@default"));

// Send message
$response = $provider->send(
    "+12221234567",
    (new Message("Hello World!"))->withSender("+12223217654")
);

// Check result
if ($response->isSent()) {
    echo "Message sent successfully!";
}
```

## Documentation

- [Basic Usage](docs/basic-usage.md) - Learn how to send SMS messages
- [Phone Formatting](docs/phone-formatting.md) - Phone number validation and formatting
- [Providers](docs/providers.md) - Available SMS providers and configuration
- [Custom Providers](docs/custom-providers.md) - Create your own SMS provider

## Available Providers

| Provider | URI Scheme | Documentation | Region |
|----------|-----------|---------------|--------|
| Twilio Messaging | `twilio://accountId:authToken@default` | [Twilio SMS](https://www.twilio.com/en-us/messaging/channels/sms) | Global |
| Twilio Verify | `twilio_verify://accountId:authToken@serviceSid` | [Twilio Verify](https://www.twilio.com/en-us/trusted-activation/verify) | Global |
| ByJG SMS | `byjg://username:password@default` | [ByJG](https://www.byjg.com.br/) | Brazil |
| Fake Sender | `fakesender://` | Testing only | Testing |

## Multi-Provider Setup

Route messages to different providers based on country codes:

```php
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\Provider\TwilioMessagingProvider;
use ByJG\SmsClient\Provider\ByJGSmsProvider;
use ByJG\SmsClient\Message;

// Register providers
ProviderFactory::registerProvider(TwilioMessagingProvider::class);
ProviderFactory::registerProvider(ByJGSmsProvider::class);

// Associate with country codes
ProviderFactory::registerServices("twilio://accountId:authToken@default", "+1");
ProviderFactory::registerServices("byjg://username:password@default", "+55");

// Automatically routes to the right provider
ProviderFactory::createAndSend("+12221234567", new Message("Hello USA!"));
ProviderFactory::createAndSend("+5521987654321", new Message("Olá Brasil!"));
```

## Phone Number Formatting

Format and validate phone numbers with country-specific rules:

```php
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\PhoneFormat\USPhoneFormat;

$phone = Phone::phone("2345678900", new USPhoneFormat())
    ->withPlusPrefix()
    ->withCountryCode();

echo $phone->hydrate();  // Output: +12345678900
echo $phone->format();   // Output: +1(234)567-8900

// Validate phone numbers
$isValid = $phone->validate(throwException: false);
```

## Dependencies

```mermaid
flowchart TD
    byjg/sms-client --> byjg/webrequest
```

----
[Open source ByJG](http://opensource.byjg.com)
