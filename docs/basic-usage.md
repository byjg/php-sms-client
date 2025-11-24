---
sidebar_position: 1
---

# Basic Usage

This guide shows you how to send SMS messages using the SMS Client library.

## Quick Start

The simplest way to send an SMS is using the `ProviderFactory`:

```php
use ByJG\SmsClient\Message;
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\Provider\TwilioMessagingProvider;
use ByJG\Uri\Uri;

// Register the provider
ProviderFactory::registerProvider(TwilioMessagingProvider::class);

// Create a provider instance
$provider = ProviderFactory::create(new Uri("twilio://$accountSid:$authToken@default"));

// Send a message
$response = $provider->send(
    "+12221234567",
    (new Message("This is a test message"))->withSender("+12223217654")
);

// Check if sent
if ($response->isSent()) {
    echo "Message sent successfully!";
} else {
    echo "Failed to send message.";
}
```

## Using ProviderFactory::createAndSend()

For even simpler usage, you can use the `createAndSend()` method:

```php
use ByJG\SmsClient\Message;
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\Provider\TwilioMessagingProvider;

// Register the provider
ProviderFactory::registerProvider(TwilioMessagingProvider::class);

// Register service with country prefix
ProviderFactory::registerServices("twilio://accountId:authToken@default", "+1");

// Send message - provider is selected automatically based on phone number
$response = ProviderFactory::createAndSend(
    "+12221234567",
    (new Message("This is a test message"))->withSender("+12223217654")
);
```

## Multi-Provider Setup

You can register multiple providers for different countries:

```php
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\Provider\TwilioMessagingProvider;
use ByJG\SmsClient\Provider\ByJGSmsProvider;

// Register providers
ProviderFactory::registerProvider(TwilioMessagingProvider::class);
ProviderFactory::registerProvider(ByJGSmsProvider::class);

// Associate providers with country codes
ProviderFactory::registerServices("twilio://accountId:authToken@default", "+1");
ProviderFactory::registerServices("byjg://username:password@default", "+55");

// US number uses Twilio
$response = ProviderFactory::createAndSend(
    "+12221234567",
    new Message("Hello from USA!")
);

// Brazilian number uses ByJG
$response = ProviderFactory::createAndSend(
    "+5521900001234",
    new Message("Olá do Brasil!")
);
```

## Message Options

The `Message` class supports various options:

```php
use ByJG\SmsClient\Message;

$message = new Message("Your message text here");

// Set sender phone number
$message->withSender("+12223217654");

// Add custom properties
$message->withProperty("custom_key", "custom_value");

// Set multiple properties at once
$message->withProperties([
    'key1' => 'value1',
    'key2' => 'value2'
]);

// Get properties
$body = $message->getBody();
$sender = $message->getSender();
$properties = $message->getProperties();
$customValue = $message->getProperty('custom_key', 'default_value');
```

## Message Creation Methods

You can create messages in multiple ways:

```php
use ByJG\SmsClient\Message;

// Using constructor
$message = new Message("Message text");

// Using static create method
$message = Message::create("Message text");

// Chaining methods
$message = Message::create("Message text")
    ->withSender("+12223217654")
    ->withProperty("priority", "high");
```
