---
sidebar_key: sms-client
sidebar_category: messaging
tags: [php, sms]
---

# SMS Client

One interface to send SMS through any provider, and the right provider chosen by the phone number's country.

[![Sponsor](https://img.shields.io/badge/Sponsor-%23ea4aaa?logo=githubsponsors&logoColor=white&labelColor=0d1117)](https://github.com/sponsors/byjg)
[![Build Status](https://github.com/byjg/php-sms-client/actions/workflows/phpunit.yml/badge.svg?branch=main)](https://github.com/byjg/php-sms-client/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](https://opensource.byjg.com)
[![Install MCP Server](https://img.shields.io/badge/Install-MCP_Server-8A2BE2?logo=modelcontextprotocol&logoColor=white)](https://opensource.byjg.com/docs/ai/mcpserver-byjg-docs/)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-sms-client/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-sms-client.svg)](https://opensource.byjg.com/license/)
[![GitHub release](https://img.shields.io/github/release/byjg/php-sms-client.svg)](https://github.com/byjg/php-sms-client/releases/)

## Why

SMS providers differ by country: one is cheap or reliable in the US, another in Brazil. Each
has its own SDK, so supporting two means two code paths, and the choice is spread wherever a
message is sent.

SMS Client puts the providers behind one interface, chosen by the connection URI, and can
route each message by the destination country:

```php
ProviderFactory::registerServices("twilio://accountId:authToken@default", "+1");
ProviderFactory::registerServices("byjg://username:password@default", "+55");

ProviderFactory::createAndSend("+12221234567", new Message("Hello USA!"));      // Twilio
ProviderFactory::createAndSend("+5521987654321", new Message("Olá Brasil!"));   // ByJG
```

Phone numbers are validated and formatted per country, and a new provider is one class.

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
use ByJG\Util\Uri;

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

## Running Tests

```bash
vendor/bin/phpunit
```

----
[Open source ByJG](https://opensource.byjg.com)
