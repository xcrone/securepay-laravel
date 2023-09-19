# SecurePay Payment Gateway for Laravel

## Setup

Add and fill in the following variables to your `.env`.
```bash
SECUREPAY_ENVIRONMENT=sandbox # production / sandbox
SECUREPAY_API_UID=
SECUREPAY_AUTH_TOKEN=
SECUREPAY_CHECKSUM_TOKEN=
```

Optionally, you can add the alias as below n `config/app.php`:-
```php
'SecurePay' => Xcrone\Facades\SecurePay::class
```

### Laravel without auto-discovery

If you don't use auto-discovery, add the ServiceProvider to the providers array in `config/app.php`.

```php
Xcrone\SecurePayServiceProvider::class
```

#### Copy the package config to your local config with the publish command

```bash
php artisan vendor:publish --provider="Xcrone\SecurePayServiceProvider"
```

## Usage Example

### Create a new payment

```php
use Xcrone\SecurePay;
use Illuminate\Http\Request;

/**
 * Demo payment.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \GuzzleHttp\Psr7\Stream
 */
public function pay(Request $request)
{
    $securepay = new SecurePay;

    $data = [
        'order_number' => rand(1111111111, 9999999999),
        'buyer_name' => 'John Doe',
        'buyer_email' => 'john@example.com',
        'buyer_phone' => '+60114444444',
        'transaction_amount' => 300.00,
        'product_description' => 'Payment for item: ' . $request->product_id,
        'callback_url' => 'http://callback_url',
        'redirect_url' => 'http://redirect_url',
        'cancel_url' => 'http://cancel_url',
        'params' => [
            'selected_item' => $request->selected_item,
            'selected_item2' => $request->selected_item2,
        ],
        'redirect_post' => 'true',
    ];

    return $securepay->createPayment($data);
}
```

### Get bank list

#### Retail bank list
All list
```php
$securepay = new SecurePay;
return $securepay->getRetailBankList();
```
Online or offline only
```php
$securepay = new SecurePay;
$online = true; // true = 'online', false = 'offline'
return $securepay->getRetailBankList($online);
```

#### Corporate bank list
All list
```php
$securepay = new SecurePay;
return $securepay->getCorporateBankList();
```
Online or offline only
```php
$securepay = new SecurePay;
$online = true; // true = 'online', false = 'offline'
return $securepay->getCorporateBankList($online);
```

More SecurePay payment parameters can be found [here](https://docs.securepay.my/api/merchant/payment#request-parameters).
