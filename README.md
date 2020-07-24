# kevin. PHP Client

> PHP client implementing kevin. API.

## Usage Examples

> Parameter names and response data match those defined in API documentation.

> Detailed API documentation can be found <a href="https://docs.getkevin.eu/public/platform" target="_blank">here</a>.

### 1. Authentication

> Initialize authentication client.

```
use Kevin\Auth;

$clientId = 'my-client-id';
$clientSecret = 'my-client-secret';

$kevinAuth = new Auth($clientId, $clientSecret);
```

### 1.1 Get supported countries

```
$response = $kevinAuth->getCountries();
```

### 1.2 Get supported banks

```
$attr = ['countryCode' => 'LT'];
$response = $kevinAuth->getBanks($attr);
```

### 1.3 Get supported bank

```
$bankId = 'SEB_LT_SAND';
$response = $kevinAuth->getBank($bankId);
```

### 1.4 Start authentication

```
$attr = [
    'redirectPreferred' => 'false',
    'scopes' => 'payments',
    'Request-Id' => 'your-guid',
    'Redirect-URL' => 'https://redirect.getkevin.eu/authorization.html'
];
$response = $kevinAuth->authenticate($attr);
```

### 1.5 Receive token

```
$attr = ['code' => 'your-auth-code'];
// ...or $attr = 'your-auth-code';
$response = $kevinAuth->receiveToken($attr);
```

### 1.6 Refresh token

```
$attr = ['refreshToken' => 'your-refresh-token'];
// ...or $attr = 'your-refresh-token';
$response = $kevinAuth->refreshToken($attr);
```

### 1.7 Receive token content

```
$attr = ['Authorization' => 'your-bearer-token'];
// ...or $attr = 'your-bearer-token';
// ...or $attr = 'Bearer your-bearer-token';
$response = $kevinAuth->receiveTokenContent($attr);
```

### 2. Payment

> Initialize payment client.

```
use Kevin\Payment;

$clientId = 'my-client-id';
$clientSecret = 'my-client-secret';

$kevinPayment = new Payment($clientId, $clientSecret);
```

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
$response = $kevinPayment->initPayment($attr);
```

### 2.2 Get payment

```
$paymentId = 'your-payment-id';
$attr = ['PSU-IP-Address' => 'user-ip-address'];
$response = $kevinPayment->getPayment($paymentId, $attr);
```

### 2.3 Get payment status

```
$paymentId = 'your-payment-id';
$attr = ['PSU-IP-Address' => 'user-ip-address'];
$response = $kevinPayment->getPaymentStatus($paymentId, $attr);
```

## Support

Email: support@getkevin.eu
