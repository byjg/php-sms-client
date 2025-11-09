---
sidebar_position: 4
---

# Creating Custom Providers

The SMS Client library is designed to be extensible. You can easily create custom providers to integrate with any SMS service.

## Provider Interface

To create a custom provider, implement the `ProviderInterface`:

```php
namespace ByJG\SmsClient\Provider;

use ByJG\SmsClient\Message;
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\ReturnObject;
use ByJG\Uri\Uri;

interface ProviderInterface
{
    /**
     * Returns the URI schema for this provider
     * @return array
     */
    public static function schema(): array;

    /**
     * Setup the provider with connection details
     * @param Uri $uri Connection URI
     */
    public function setUp(Uri $uri): void;

    /**
     * Send an SMS message
     * @param string|Phone $to Recipient phone number
     * @param Message $envelope Message to send
     * @return ReturnObject Result of the send operation
     */
    public function send(string|Phone $to, Message $envelope): ReturnObject;
}
```

## Using ProviderBase

The easiest way to create a custom provider is to extend the `ProviderBase` class:

```php
namespace MyApp\SmsProviders;

use ByJG\SmsClient\Message;
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\Provider\ProviderBase;
use ByJG\SmsClient\ReturnObject;
use ByJG\Uri\Uri;

class CustomSmsProvider extends ProviderBase
{
    protected string $apiKey;
    protected string $apiSecret;

    /**
     * Define the URI schema for your provider
     */
    public static function schema(): array
    {
        return [
            'customsms' // Your custom URI scheme
        ];
    }

    /**
     * Setup connection from URI
     * URI format: customsms://apiKey:apiSecret@default
     */
    public function setUp(Uri $uri): void
    {
        $this->apiKey = $uri->getUsername();
        $this->apiSecret = $uri->getPassword();
    }

    /**
     * Send the SMS message
     */
    #[\Override]
    public function send(string|Phone $to, Message $envelope): ReturnObject
    {
        // Normalize phone number
        $phoneNumber = $this->getPhoneNumber($to);

        // Get message body
        $messageBody = $envelope->getBody();

        // Get sender (if set)
        $sender = $envelope->getSender();
        if ($sender instanceof Phone) {
            $sender = $this->getPhoneNumber($sender);
        }

        // Make API call to your SMS service
        try {
            $result = $this->makeApiCall($phoneNumber, $messageBody, $sender);

            return new ReturnObject(
                true, // Message sent successfully
                [
                    'message_id' => $result['id'],
                    'status' => $result['status']
                ]
            );
        } catch (\Exception $e) {
            return new ReturnObject(
                false, // Message failed
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Your custom API call logic
     */
    protected function makeApiCall(string $to, string $message, ?string $from): array
    {
        // Implement your API call here
        // This is just an example

        $data = [
            'to' => $to,
            'message' => $message,
            'from' => $from,
            'api_key' => $this->apiKey
        ];

        // Make HTTP request to your SMS API
        $response = $this->sendHttpRequest(/* ... */);

        return json_decode($response, true);
    }
}
```

## Complete Example

Here's a complete example of a custom provider for a fictional SMS service:

```php
namespace MyApp\SmsProviders;

use ByJG\SmsClient\Message;
use ByJG\SmsClient\Phone;
use ByJG\SmsClient\Provider\ProviderBase;
use ByJG\SmsClient\ReturnObject;
use ByJG\Uri\Uri;
use ByJG\WebRequest\HttpClient;
use ByJG\WebRequest\Psr7\Request;

class AcmeProvider extends ProviderBase
{
    protected string $apiToken;
    protected string $apiEndpoint = 'https://api.acmesms.com/v1';

    public static function schema(): array
    {
        return ['acmesms'];
    }

    public function setUp(Uri $uri): void
    {
        // URI format: acmesms://token@endpoint
        $this->apiToken = $uri->getUsername();

        if ($uri->getHost() !== 'default') {
            $this->apiEndpoint = 'https://' . $uri->getHost();
        }
    }

    #[\Override]
    public function send(string|Phone $to, Message $envelope): ReturnObject
    {
        $to = $this->getPhoneNumber($to);
        $from = $envelope->getSender();

        if ($from instanceof Phone) {
            $from = $this->getPhoneNumber($from);
        }

        // Prepare request
        $request = new Request("{$this->apiEndpoint}/send");
        $request = $request->withMethod('POST')
            ->withHeader('Authorization', "Bearer {$this->apiToken}")
            ->withHeader('Content-Type', 'application/json')
            ->withBody(json_encode([
                'to' => $to,
                'from' => $from,
                'message' => $envelope->getBody(),
                'properties' => $envelope->getProperties()
            ]));

        try {
            $client = HttpClient::getInstance();
            $response = $this->sendHttpRequest($client, $request);

            $data = json_decode($response->getBody()->getContents(), true);

            $success = $response->getStatusCode() === 200
                && isset($data['status'])
                && $data['status'] === 'sent';

            return new ReturnObject($success, $data);

        } catch (\Exception $e) {
            return new ReturnObject(false, [
                'error' => $e->getMessage()
            ]);
        }
    }
}
```

## Registering Your Custom Provider

Once you've created your provider, register it with the `ProviderFactory`:

```php
use ByJG\SmsClient\Provider\ProviderFactory;
use MyApp\SmsProviders\AcmeProvider;

// Register the provider
ProviderFactory::registerProvider(AcmeProvider::class);

// Use it
$provider = ProviderFactory::create(new Uri("acmesms://your-api-token@default"));

$response = $provider->send(
    "+12221234567",
    (new Message("Hello from Acme SMS!"))->withSender("+12223217654")
);
```

## Using with Service Registration

You can also register your custom provider for automatic selection:

```php
use ByJG\SmsClient\Provider\ProviderFactory;
use MyApp\SmsProviders\AcmeProvider;

ProviderFactory::registerProvider(AcmeProvider::class);

// Register for specific country codes
ProviderFactory::registerServices(
    "acmesms://your-api-token@default",
    ["+44", "+49"] // UK and Germany
);

// Automatically uses your provider for UK/German numbers
$response = ProviderFactory::createAndSend(
    "+447911123456",
    new Message("Hello UK!")
);
```

## Helper Methods

The `ProviderBase` class provides useful helper methods:

### getPhoneNumber()

Converts a `Phone` object to a string:

```php
$phoneString = $this->getPhoneNumber($phoneObject);
```

### sendHttpRequest()

Makes HTTP requests (you can override for custom behavior):

```php
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

protected function sendHttpRequest(
    ClientInterface $client,
    RequestInterface $request
): ResponseInterface {
    return $client->sendRequest($request);
}
```

## Best Practices

1. **Error Handling**: Always wrap API calls in try-catch blocks and return appropriate `ReturnObject` instances

2. **Phone Number Validation**: Use the `getPhoneNumber()` helper to ensure proper phone number format

3. **Configuration**: Store API credentials and endpoints in the `setUp()` method

4. **Response Data**: Include useful information in the `ReturnObject` extra info array

5. **Testing**: Create a mock version of your provider for testing (extend your provider and override `sendHttpRequest()`)

## Testing Your Provider

Create a mock version for testing:

```php
namespace Tests\Providers;

use MyApp\SmsProviders\AcmeProvider;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ByJG\WebRequest\Psr7\Response;
use ByJG\WebRequest\Psr7\MemoryStream;

class AcmeProviderMock extends AcmeProvider
{
    #[\Override]
    protected function sendHttpRequest(
        ClientInterface $client,
        RequestInterface $request
    ): ResponseInterface {
        // Return mock response
        return new Response(200, new MemoryStream(json_encode([
            'status' => 'sent',
            'message_id' => 'mock-12345'
        ])));
    }
}
```

Then use it in your tests:

```php
use Tests\Providers\AcmeProviderMock;
use ByJG\SmsClient\Provider\ProviderFactory;

ProviderFactory::registerProvider(AcmeProviderMock::class);

$provider = ProviderFactory::create(new Uri("acmesms://test-token@default"));
$response = $provider->send("+12221234567", new Message("Test"));

$this->assertTrue($response->isSent());
```
