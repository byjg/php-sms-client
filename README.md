# SMS Client

[![Build Status](https://github.com/byjg/php-sms-client/actions/workflows/phpunit.yml/badge.svg?branch=main)](https://github.com/byjg/php-sms-client/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](http://opensource.byjg.com)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-sms-client/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-sms-client.svg)](https://opensource.byjg.com/opensource/licensing.html)
[![GitHub release](https://img.shields.io/github/release/byjg/php-sms-client.svg)](https://github.com/byjg/php-sms-client/releases/)

This is a simple client to send SMS using different providers.

## Features

* Low code to send SMS
* Classes totally decoupled
* Easy to implement new providers

## Usage

### Using ProviderFactory

```php
// Register the provider and associate with a scheme
ProviderFactory::registerProvider(TwilioMessagingProvider::class);

// Create a provider
$provider = ProviderFactory::create(new Uri("twilio://$accountSid:$authToken@default"));

// Send a message
$response = $byjg->send("12221234567", (new \ByJG\SmsClient\Message("This is a test message")->withSender("+12223217654"));

// Check if the message was sent
if ($response->isSent()) {
    echo "Message sent";
} else {
    echo "Message not sent";
}
```

### Using ProviderFactory to send a message to multiple providers depending on the country


```php
// Register the provider and associate with a scheme
ProviderFactory::registerProvider(TwilioMessagingProvider::class);
ProviderFactory::registerProvider(ByJGSmsProvider::class);

// Define the provider according to the country prefix
ProviderFactory::registerServices("twilio://accoundId:authToken@default", ["+1"]);
ProviderFactory::registerServices("byjg://username:password@default", ["+55"]);

// Send a message and select the provider according to the country prefix
$response = ProviderFactory::createAndSend("+5521900001234", (new \ByJG\SmsClient\Message("This is a test message")));
var_dump($response);

$response = ProviderFactory::createAndSend("+12221234567", (new \ByJG\SmsClient\Message("This is a test message"))->withSender("+12223217654"));
var_dump($response);
```

## Providers

The providers are the classes responsible to send the text message.

All providers have the following interface:

```php
<?php
interface ProviderInterface
{
    public static function schema();

    public function setUp(Uri $uri);

    public function send($to, Message $envelope): ReturnObject;
}
```

There is no necessary call the method `getConnection()` because the method publish() and consume() will call it automatically.
Use the method `getConnection()` only if you need to access the connection directly.

## Implemented providers

| provider                                       | URL / Documentation                                                                                               | Specifics                                                                                                                     |
|------------------------------------------------|-------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------|
| twilio://accoundId:authToken@default           | Send a message using the [Twilio messaging](https://www.twilio.com/en-us/messaging/channels/sms) provider.        | Message object requires `withSender` to set.                                                                                  | 
| twilio_verify://accoundId:authToken@serviceSid | Send a message using the [Twilio verify](https://www.twilio.com/en-us/trusted-activation/verify) verify provider. | Message with empty body send the SMS with the OTP code. To validate the received OTP, needs to pass it to the `Message::body` |
| byjg://username:password@default               | Send a message using the [ByJG](https://www.byjg.com.br/) provider.                                               | Only Brazil.                                                                                                                  |
| fakesender://                                  | Fake sender to be used on tests.                                                                                  | Only for tests. Do not send messages.                                                                                         |

## Install

```shell
composer require "byjg/sms-client"
```

## Dependencies

```mermaid  
flowchart TD  
    byjg/sms-client --> byjg/webrequest
    byjg/sms-client --> ext-curl
```

----  
[Open source ByJG](http://opensource.byjg.com)

----
[Open source ByJG](http://opensource.byjg.com)
