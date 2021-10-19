# kevin. PHP Client

> PHP client implementing kevin. API.

## Prerequisites

- PHP 5.6 or later

## Installation

### Composer

1. To install latest kevin. PHP Client repository using composer:

```
composer require getkevin/kevin-php
```

2. Using Composer autoloader, include kevin. PHP Client:

```
require('vendor/autoload.php');
```

> Detailed information about available versions can be found at packagist repository:
>
>https://packagist.org/packages/getkevin/kevin-php


## Usage Examples

> Parameter names and response data match those defined in API documentation.

> Detailed API documentation can be found <a href="https://docs.kevin.eu/public/platform" target="_blank">here</a>.

### Initialization

```
use Kevin\Client;

$clientId = 'my-client-id';
$clientSecret = 'my-client-secret';
$options = ['error' => 'array', 'version' => '0.3', 'lang' => 'en'];

$kevinClient = new Client($clientId, $clientSecret, $options);
```

> `$clientId` - Your client id. Your can get it in kevin. platform console.

> `$clientSecret` - Your client secret. Your can get it in kevin. platform console.

> `$options` - Optional options array.
>
> - `error` - Defines return type of error data. Possible values are: `array` - returns an array on error, `exception` - throws an exception on error, default value is `exception`.
>
> - `version` - Selects API versions to use. Default value is `0.3`. Possible values are `0.1`, `0.2` or `0.3`.

### 1. Authentication

### 1.1 Get supported countries

```
$response = $kevinClient->auth()->getCountries();
```

### 1.2 Get supported banks

```
$attr = ['countryCode' => 'LT'];
$response = $kevinClient->auth()->getBanks($attr);
```

### 1.3 Get supported bank

```
$bankId = 'SEB_LT_SAND';
$response = $kevinClient->auth()->getBank($bankId);
```

### 1.4 Get project settings

```
$response = $kevinClient->auth()->getProjectSettings();
```

### 1.5 Start authentication

```
$attr = [
    'redirectPreferred' => 'false',
    'scopes' => 'payments',
    'Request-Id' => 'your-guid',
    'Redirect-URL' => 'https://redirect.kevin.eu/authorization.html'
];
$response = $kevinClient->auth()->authenticate($attr);
```

### 1.6 Receive token

```
$attr = ['code' => 'your-auth-code'];
// ...or $attr = 'your-auth-code';
$response = $kevinClient->auth()->receiveToken($attr);
```

### 1.7 Refresh token

```
$attr = ['refreshToken' => 'your-refresh-token'];
// ...or $attr = 'your-refresh-token';
$response = $kevinClient->auth()->refreshToken($attr);
```

### 1.8 Receive token content

```
$attr = ['Authorization' => 'your-bearer-token'];
// ...or $attr = 'your-bearer-token';
// ...or $attr = 'Bearer your-bearer-token';
$response = $kevinClient->auth()->receiveTokenContent($attr);
```

### 2. Payment

### 2.1 Initiate bank payment

:exclamation: _Take a note that the example below is for the v0.3 only. The v0.1 and v0.2 requires a slightly different body._

```
$attr = [
    'Redirect-URL' => 'https://redirect.kevin.eu/payment.html',
    'description' => 'Test',
    'currencyCode' => 'EUR',
    'amount' => '0.01',
    'bankPaymentMethod' => [
        'endToEndId' => '1',
        'creditorName' => 'John Smith',
        'creditorAccount' => [
            'iban' => 'LT144010051005081586'
        ],
    ],
];
$response = $kevinClient->payment()->initPayment($attr);
```

### 2.2 Initiate card payment

```
$attr = [
    'Redirect-URL' => 'https://redirect.kevin.eu/payment.html',
    'description' => 'Test',
    'currencyCode' => 'EUR',
    'amount' => '0.01',
    'cardPaymentMethod' => [
        'cvc' => '123',
        'expMonth' => '01',
        'expYear' => '2036',
        'number' => '5555555555555555',
        'holderName' => 'John Titor',
    ],
];
$response = $kevinClient->payment()->initPayment($attr);
```

### 2.3 Initiate hybrid payment

```
$attr = [
    'Redirect-URL' => 'https://redirect.kevin.eu/payment.html',
    'description' => 'Test',
    'currencyCode' => 'EUR',
    'amount' => '0.01',
    'bankPaymentMethod' => [
        'endToEndId' => '1',
        'creditorName' => 'John Smith',
        'creditorAccount' => [
            'iban' => 'LT144010051005081586'
        ],
    ],
    'cardPaymentMethod' => [],
];
$response = $kevinClient->payment()->initPayment($attr);
```

### 2.4 Get payment

```
$paymentId = 'your-payment-id';
$response = $kevinClient->payment()->getPayment($paymentId);
```

### 2.5 Get payment status

```
$paymentId = 'your-payment-id';
$response = $kevinClient->payment()->getPaymentStatus($paymentId);
```

### 2.6 Initiate payment refund

```
$paymentId = 'your-payment-id';
$attr = [
    'amount' => '1.00',
    'Webhook-URL' => 'https://yourapp.com/notify'
];
$response = $kevinClient->payment()->initiatePaymentRefund($paymentId, $attr);
```

### 2.7 Get payment refunds

```
$paymentId = 'your-payment-id';
$response = $kevinClient->payment()->getPaymentRefunds($paymentId);
```

### 3. Security

### 3.1 Verify signature

:exclamation: _We recommend ignoring the webhook if the signature is older than 5 minutes._

```
use Kevin\SecurityManager;

$endpointSecret = 'your-endpoint-secret';
$webhookUrl = 'your-webhook-url';

// Timestamp is provided in milliseconds
$timestampTimeout = 300000;

$requestBody = file_get_contents("php://input");
$headers = getallheaders();

$isValid = SecurityManager::verifySignature($endpointSecret, $requestBody, $headers, $webhookUrl, $timestampTimeout);
```

## Support

Email: help@kevin.eu

## License

- **[MIT license](http://opensource.org/licenses/mit-license.php)**
- Copyright© 2020 <a href="https://www.kevin.eu/" target="_blank">kevin.</a>
