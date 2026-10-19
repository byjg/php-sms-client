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
use ByJG\Util\Uri;

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

## Sending in Parallel

`sendAsync(string|Phone $to, Message $envelope)` returns a `ByJG\WebRequest\HttpPromise` (see
[byjg/webrequest](https://github.com/byjg/php-webrequest/blob/master/docs/http-client-parallel.md)) fulfilled with
the `ReturnObject`, or rejected with the error: it does not throw. The HTTP providers (`byjg`, `twilio`,
`twilio_verify`) send in parallel while the promises are awaited:

```php
use ByJG\WebRequest\HttpPromise;

$message = (new Message("Your order has shipped"))->withSender("+12223217654");

$results = HttpPromise::all(
    ...array_map(fn($phone) => ProviderFactory::createAndSendAsync($phone, $message), $phones)
)->await();   // the ReturnObject of each phone, in the same order
```

For a long list, `HttpPromise::all()` would open every connection at once and could exceed the provider's rate limit
(HTTP 429). `HttpPromise::map()` keeps at most a number of messages in progress:

```php
$results = HttpPromise::map(
    $phones,
    fn($phone) => ProviderFactory::createAndSendAsync($phone, $message),
    10   // at most 10 messages in progress
)->await();   // the ReturnObject of each phone, in the same order
```

`ProviderFactory::createAndSendAsync()` selects the provider as `createAndSend()` does. A provider that only
implements `send()` (e.g. `fakesender`, or a custom one) sends at once and returns a promise that is already settled.

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
