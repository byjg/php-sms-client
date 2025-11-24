---
sidebar_position: 3
---

# Providers

Providers are the classes responsible for sending SMS messages through different services. The SMS Client library comes with several built-in providers and makes it easy to create custom ones.

## Provider Interface

All providers implement the `ProviderInterface`:

```php
interface ProviderInterface
{
    public static function schema(): array;
    public function setUp(Uri $uri): void;
    public function send(string|Phone $to, Message $envelope): ReturnObject;
}
```

## Built-in Providers

### Twilio Messaging Provider

Send SMS messages using [Twilio Messaging API](https://www.twilio.com/en-us/messaging/channels/sms).

**Connection URI:**
```
twilio://accountId:authToken@default
```

**Requirements:**
- The `Message` object must have a sender set using `withSender()`

**Example:**

```php
use ByJG\SmsClient\Message;
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\Provider\TwilioMessagingProvider;
use ByJG\Uri\Uri;

ProviderFactory::registerProvider(TwilioMessagingProvider::class);

$provider = ProviderFactory::create(
    new Uri("twilio://ACxxxx:your_auth_token@default")
);

$response = $provider->send(
    "+12221234567",
    (new Message("Hello from Twilio!"))->withSender("+12223217654")
);

if ($response->isSent()) {
    echo "Message sent! SID: " . $response->getExtraInfo()['sid'];
}
```

### Twilio Verify Provider

Send OTP (One-Time Password) codes using [Twilio Verify API](https://www.twilio.com/en-us/trusted-activation/verify).

**Connection URI:**
```
twilio_verify://accountId:authToken@serviceSid
```

**Features:**
- Send OTP: Use an empty message body to send an OTP code
- Verify OTP: Pass the received code in the message body to validate

**Example - Sending OTP:**

```php
use ByJG\SmsClient\Message;
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\Provider\TwilioVerifyProvider;
use ByJG\Uri\Uri;

ProviderFactory::registerProvider(TwilioVerifyProvider::class);

$provider = ProviderFactory::create(
    new Uri("twilio_verify://ACxxxx:auth_token@VAxxxServiceSid")
);

// Send OTP (empty body triggers OTP generation)
$response = $provider->send("+12221234567", new Message(""));

if ($response->isSent()) {
    echo "OTP sent successfully!";
}
```

**Example - Verifying OTP:**

```php
// Verify the OTP code received by user
$response = $provider->send(
    "+12221234567",
    new Message("123456") // The OTP code to verify
);

if ($response->isSent()) {
    echo "OTP verified successfully!";
} else {
    echo "Invalid OTP code.";
}
```

### ByJG SMS Provider

Send SMS messages using the [ByJG SMS Service](https://www.byjg.com.br/).

**Connection URI:**
```
byjg://username:password@default
```

**Availability:**
- Only available for Brazilian phone numbers (+55)

**Example:**

```php
use ByJG\SmsClient\Message;
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\Provider\ByJGSmsProvider;
use ByJG\Uri\Uri;

ProviderFactory::registerProvider(ByJGSmsProvider::class);

$provider = ProviderFactory::create(
    new Uri("byjg://username:password@default")
);

$response = $provider->send(
    "+5521987654321",
    new Message("Olá! Mensagem de teste.")
);
```

### Fake Provider

A fake provider for testing purposes that doesn't send actual messages.

**Connection URI:**
```
fakesender://
```

**Usage:**
- Only for testing and development
- Does not send real SMS messages
- Always returns success

**Example:**

```php
use ByJG\SmsClient\Message;
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\Provider\FakeProvider;
use ByJG\Uri\Uri;

ProviderFactory::registerProvider(FakeProvider::class);

$provider = ProviderFactory::create(new Uri("fakesender://"));

$response = $provider->send(
    "+12221234567",
    new Message("This message won't be sent")
);

// Always returns true
var_dump($response->isSent()); // true
```

## Provider Registration

Before using a provider, you must register it with the `ProviderFactory`:

```php
use ByJG\SmsClient\Provider\ProviderFactory;
use ByJG\SmsClient\Provider\TwilioMessagingProvider;

ProviderFactory::registerProvider(TwilioMessagingProvider::class);
```

## Service Registration

You can register services with country code prefixes for automatic provider selection:

```php
use ByJG\SmsClient\Provider\ProviderFactory;

// Register providers first
ProviderFactory::registerProvider(TwilioMessagingProvider::class);
ProviderFactory::registerProvider(ByJGSmsProvider::class);

// Register services with country prefixes
ProviderFactory::registerServices(
    "twilio://accountId:authToken@default",
    "+1"  // Use for US numbers
);

ProviderFactory::registerServices(
    "byjg://username:password@default",
    "+55" // Use for Brazilian numbers
);

// Now createAndSend() will automatically select the right provider
$response = ProviderFactory::createAndSend(
    "+12221234567",
    new Message("Sent via Twilio")
);

$response = ProviderFactory::createAndSend(
    "+5521987654321",
    new Message("Enviado via ByJG")
);
```

## Return Object

All providers return a `ReturnObject` after sending a message:

```php
$response = $provider->send($to, $message);

// Check if message was sent
if ($response->isSent()) {
    echo "Success!";
}

// Get extra information (provider-specific)
$extraInfo = $response->getExtraInfo();
print_r($extraInfo);
```

The `extraInfo` array contains provider-specific data, such as:
- Twilio: Message SID, status, error codes
- ByJG: Transaction ID, delivery status

## Provider Comparison

| Provider | URI Scheme | Region | OTP Support | Sender Required |
|----------|-----------|--------|-------------|-----------------|
| Twilio Messaging | `twilio://` | Global | No | Yes |
| Twilio Verify | `twilio_verify://` | Global | Yes | No |
| ByJG SMS | `byjg://` | Brazil only | No | No |
| Fake Sender | `fakesender://` | Testing only | No | No |
