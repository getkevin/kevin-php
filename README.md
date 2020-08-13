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

> Detailed API documentation can be found <a href="https://docs.getkevin.eu/public/platform" target="_blank">here</a>.

### Initialization

```
use Kevin\Client;

$clientId = 'my-client-id';
$clientSecret = 'my-client-secret';
$options = ['error' => 'array'];

$kevinClient = new Client($clientId, $clientSecret, $options);
```

> `$clientId` - Your client id. Your can get it in kevin. platform console.

> `$clientSecret` - Your client secret. Your can get it in kevin. platform console.

> `$options` - Optional options array.
>
> - `error` - Defines return type of error data. Possible values are: `array` - returns an array on error, `exception` - throws an exception on error, default value is `exception`.

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

### 1.4 Start authentication

```
$attr = [
    'redirectPreferred' => 'false',
    'scopes' => 'payments',
    'Request-Id' => 'your-guid',
    'Redirect-URL' => 'https://redirect.getkevin.eu/authorization.html'
];
$response = $kevinClient->auth()->authenticate($attr);
```

### 1.5 Receive token

```
$attr = ['code' => 'your-auth-code'];
// ...or $attr = 'your-auth-code';
$response = $kevinClient->auth()->receiveToken($attr);
```

### 1.6 Refresh token

```
$attr = ['refreshToken' => 'your-refresh-token'];
// ...or $attr = 'your-refresh-token';
$response = $kevinClient->auth()->refreshToken($attr);
```

### 1.7 Receive token content

```
$attr = ['Authorization' => 'your-bearer-token'];
// ...or $attr = 'your-bearer-token';
// ...or $attr = 'Bearer your-bearer-token';
$response = $kevinClient->auth()->receiveTokenContent($attr);
```

### 2. Payment

### 2.1 Initiate payment

```
$attr = [
    'Redirect-URL' => 'https://redirect.getkevin.eu/payment.html',
    'endToEndId' => '1',
    'informationUnstructured' => 'Test',
    'currencyCode' => 'EUR',
    'amount' => '0.01',
    'creditorName' => 'John Smith',
    'creditorAccount' => [
        'iban' => 'LT144010051005081586'
    ]
];
$response = $kevinClient->payment()->initPayment($attr);
```

### 2.2 Get payment

```
$paymentId = 'your-payment-id';
$attr = ['PSU-IP-Address' => 'user-ip-address'];
$response = $kevinClient->payment()->getPayment($paymentId, $attr);
```

### 2.3 Get payment status

```
$paymentId = 'your-payment-id';
$attr = ['PSU-IP-Address' => 'user-ip-address'];
$response = $kevinClient->payment()->getPaymentStatus($paymentId, $attr);
```

## Support

Email: support@getkevin.eu

## License

- **[MIT license](http://opensource.org/licenses/mit-license.php)**
- Copyright© 2020 <a href="https://www.getkevin.eu/" target="_blank">kevin.</a>
