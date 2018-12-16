# Omnipay

**An easy to use, consistent payment processing library for PHP**

[![Build Status](https://travis-ci.org/thephpleague/omnipay-common.svg?branch=master)](https://travis-ci.org/thephpleague/omnipay-common)
[![Latest Stable Version](https://poser.pugx.org/omnipay/common/version)](https://packagist.org/packages/omnipay/common)
[![Total Downloads](https://poser.pugx.org/omnipay/common/d/total)](https://packagist.org/packages/omnipay/common)

Omnipay is a payment processing library for PHP. It has been designed based on
ideas from [Active Merchant](http://activemerchant.org/), plus experience implementing
dozens of gateways for [CI Merchant]. It has a clear and consistent API,
is fully unit tested, and even comes with an example application to get you started.

**Why use Omnipay instead of a gateway's official PHP package/example code?**

* Because you can learn one API and use it in multiple projects using different payment gateways
* Because if you need to change payment gateways you won't need to rewrite your code
* Because most official PHP payment gateway libraries are a mess
* Because most payment gateways have exceptionally poor documentation
* Because you are writing a shopping cart and need to support multiple gateways

## TL;DR

Just want to see some code?

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Stripe');
$gateway->setApiKey('abc123');

$formData = array('number' => '4242424242424242', 'expiryMonth' => '6', 'expiryYear' => '2030', 'cvv' => '123');
$response = $gateway->purchase(array('amount' => '10.00', 'currency' => 'USD', 'card' => $formData))->send();

if ($response->isRedirect()) {
    // redirect to offsite payment gateway
    $response->redirect();
} elseif ($response->isSuccessful()) {
    // payment was successful: update database
    print_r($response);
} else {
    // payment failed: display message to customer
    echo $response->getMessage();
}
```

As you can see, Omnipay has a consistent, well thought out API. We try to abstract as much
as possible the differences between the various payments gateways.

## Package Layout

Omnipay is a collection of packages which all depend on the
[omnipay/common](https://github.com/thephpleague/omnipay-common) package to provide
a consistent interface. There are no dependencies on official payment gateway PHP packages -
we prefer to work with the HTTP API directly. Under the hood, we use the popular and powerful
[PHP-HTTP](http://docs.php-http.org/en/latest/index.html) library to make HTTP requests. 
A [Guzzle](http://guzzlephp.org/) adapter is required by default, when using `league/omnipay`.

New gateways can be created by cloning the layout of an existing package. When choosing a
name for your package, please don't use the `omnipay` vendor prefix, as this implies that
it is officially supported. You should use your own username as the vendor prefix, and prepend
`omnipay-` to the package name to make it clear that your package works with Omnipay.
For example, if your GitHub username was `santa`, and you were implementing the `giftpay`
payment library, a good name for your composer package would be `santa/omnipay-giftpay`.

## Installation

Omnipay is installed via [Composer](https://getcomposer.org/). 
For most uses, you will need to require `league/omnipay` and an individual gateway:

```
composer require league/omnipay:^3 omnipay/paypal
```

If you want to use your own HTTP Client instead of Guzzle (which is the default for `league/omnipay`),
you can require `league/common` and any `php-http/client-implementation` (see [PHP Http](http://docs.php-http.org/en/latest/clients.html))

```
composer require league/common:^3 omnipay/paypal php-http/buzz-adapter
```

## Upgrade from v2 to v3

If your gateway is supported for v3, you can require that version. Make sure you require `league/omnipay` or a separate Http Adapter.

If there is no version for v3 yet, please raise an issue or upgrade the gateways yourself and create a PR.
See the [Upgrade guide for omnipay/common](https://github.com/thephpleague/omnipay-common/blob/master/UPGRADE.md)

> Note: The package name has been changed from `omnipay/omnipay` to `league/omnipay` for v3

Gateways are created and initialized like so:

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('PayPal_Express');
$gateway->setUsername('adrian');
$gateway->setPassword('12345');
```

Most settings are gateway specific. If you need to query a gateway to get a list
of available settings, you can call `getDefaultParameters()`:

```php
$settings = $gateway->getDefaultParameters();
// default settings array format:
array(
    'username' => '', // string variable
    'testMode' => false, // boolean variable
    'landingPage' => array('billing', 'login'), // enum variable, first item should be treated as default
);
```

Generally most payment gateways can be classified as one of two types:

* Off-site gateways such as PayPal Express, where the customer is redirected to a third party site to enter payment details
* On-site (merchant-hosted) gateways such as PayPal Pro, where the customer enters their credit card details on your site

However, there are some gateways such as Sage Pay Direct, where you take credit card details on site, then optionally redirect
if the customer's card supports 3D Secure authentication. Therefore, there is no point differentiating between the two types of
gateway (other than by the methods they support).

## Credit Card / Payment Form Input

User form input is directed to an [CreditCard](https://github.com/thephpleague/omnipay-common/blob/master/src/Common/CreditCard.php)
object. This provides a safe way to accept user input.

The `CreditCard` object has the following fields:

* firstName
* lastName
* number
* expiryMonth
* expiryYear
* startMonth
* startYear
* cvv
* issueNumber
* type
* billingAddress1
* billingAddress2
* billingCity
* billingPostcode
* billingState
* billingCountry
* billingPhone
* shippingAddress1
* shippingAddress2
* shippingCity
* shippingPostcode
* shippingState
* shippingCountry
* shippingPhone
* company
* email

Even off-site gateways make use of the `CreditCard` object, because often you need to pass
customer billing or shipping details through to the gateway.

The `CreditCard` object can be initialized with untrusted user input via the constructor.
Any fields passed to the constructor which are not recognized will be ignored.

```php
$formInputData = array(
    'firstName' => 'Bobby',
    'lastName' => 'Tables',
    'number' => '4111111111111111',
);
$card = new CreditCard($formInputData);
```

You can also just pass the form data array directly to the gateway, and a `CreditCard` object
will be created for you.

CreditCard fields can be accessed using getters and setters:

```php
$number = $card->getNumber();
$card->setFirstName('Adrian');
```

If you submit credit card details which are obviously invalid (missing required fields, or a number
which fails the Luhn check), [InvalidCreditCardException](https://github.com/thephpleague/omnipay-common/blob/master/src/Omnipay/Common/Exception/InvalidCreditCardException.php)
will be thrown.  You should validate the card details using your framework's validation library
before submitting the details to your gateway, to avoid unnecessary API calls.

For on-site payment gateways, the following card fields are generally required:

* firstName
* lastName
* number
* expiryMonth
* expiryYear
* cvv

You can also verify the card number using the Luhn algorithm by calling `Helper::validateLuhn($number)`.

## Gateway Methods

The main methods implemented by gateways are:

* `authorize($options)` - authorize an amount on the customer's card
* `completeAuthorize($options)` - handle return from off-site gateways after authorization
* `capture($options)` - capture an amount you have previously authorized
* `purchase($options)` - authorize and immediately capture an amount on the customer's card
* `completePurchase($options)` - handle return from off-site gateways after purchase
* `refund($options)` - refund an already processed transaction
* `void($options)` - generally can only be called up to 24 hours after submitting a transaction
* `acceptNotification()` - convert an incoming request from an off-site gateway to a generic notification object
  for further processing

On-site gateways do not need to implement the `completeAuthorize` and `completePurchase` methods. Gateways that don't
receive payment notifications don't need to implement `acceptNotification`. If any gateway does not support certain
features (such as refunds), it will throw `BadMethodCallException`.

All gateway methods except `acceptNotification` take an `$options` array as an argument. The `acceptNotification` method
does not take any parameters and will access the HTTP URL variables or POST data implicitly. Each gateway differs in
which parameters are required, and the gateway will throw `InvalidRequestException` if you omit any required parameters.
All gateways will accept a subset of these options:

* card
* token
* amount
* currency
* description
* transactionId
* clientIp
* returnUrl
* cancelUrl

Pass the options through to the method like so:

```php
$card = new CreditCard($formData);
$request = $gateway->authorize(array(
    'amount' => '10.00', // this represents $10.00
    'card' => $card,
    'returnUrl' => 'https://www.example.com/return',
));
```

When calling the `completeAuthorize` or `completePurchase` methods, the exact same arguments should be provided as
when you made the initial `authorize` or `purchase` call (some gateways will need to verify for example the actual
amount paid equals the amount requested). The only parameter you can omit is `card`.

To summarize the various parameters you have available to you:

* Gateway settings (e.g. username and password) are set directly on the gateway. These settings apply to all payments, and generally you will store these in a configuration file or in the database.
* Method options are used for any payment-specific options, which are not set by the customer. For example, the payment `amount`, `currency`, `transactionId` and `returnUrl`.
* CreditCard parameters are data which the user supplies. For example, you want the user to specify their `firstName` and `billingCountry`, but you don't want a user to specify the payment `currency` or `returnUrl`.

## The Payment Response

The payment response must implement [ResponseInterface](https://github.com/thephpleague/omnipay-common/blob/master/src/Omnipay/Common/Message/ResponseInterface.php). There are two main types of response:

* Payment was successful (standard response)
* Website requires redirect to off-site payment form (redirect response)

### Successful Response

For a successful responses, a reference will normally be generated, which can be used to capture or refund the transaction
at a later date. The following methods are always available:

```php
$response = $gateway->purchase(array('amount' => '10.00', 'card' => $card))->send();

$response->isSuccessful(); // is the response successful?
$response->isRedirect(); // is the response a redirect?
$response->getTransactionReference(); // a reference generated by the payment gateway
$response->getTransactionId(); // the reference set by the originating website if available.
$response->getMessage(); // a message generated by the payment gateway
```

In addition, most gateways will override the response object, and provide access to any extra fields returned by the gateway.

### Redirect Response

The redirect response is further broken down by whether the customer's browser must redirect using GET (RedirectResponse object), or
POST (FormRedirectResponse). These could potentially be combined into a single response class, with a `getRedirectMethod()`.

After processing a payment, the cart should check whether the response requires a redirect, and if so, redirect accordingly:

```php
$response = $gateway->purchase(array('amount' => '10.00', 'card' => $card))->send();
if ($response->isSuccessful()) {
    // payment is complete
} elseif ($response->isRedirect()) {
    $response->redirect(); // this will automatically forward the customer
} else {
    // not successful
}
```

The customer isn't automatically forwarded on, because often the cart or developer will want to customize the redirect method
(or if payment processing is happening inside an AJAX call they will want to return JS to the browser instead).

To display your own redirect page, simply call `getRedirectUrl()` on the response, then display it accordingly:

```php
$url = $response->getRedirectUrl();
// for a form redirect, you can also call the following method:
$data = $response->getRedirectData(); // associative array of fields which must be posted to the redirectUrl
```

## Error Handling

You can test for a successful response by calling `isSuccessful()` on the response object. If there
was an error communicating with the gateway, or your request was obviously invalid, an exception
will be thrown. In general, if the gateway does not throw an exception, but returns an unsuccessful
response, it is a message you should display to the customer. If an exception is thrown, it is
either a bug in your code (missing required fields), or a communication error with the gateway.

You can handle both scenarios by wrapping the entire request in a try-catch block:

```php
try {
    $response = $gateway->purchase(array('amount' => '10.00', 'card' => $card))->send();
    if ($response->isSuccessful()) {
        // mark order as complete
    } elseif ($response->isRedirect()) {
        $response->redirect();
    } else {
        // display error to customer
        exit($response->getMessage());
    }
} catch (\Exception $e) {
    // internal error, log exception and display a generic message to the customer
    exit('Sorry, there was an error processing your payment. Please try again later.');
}
```

## Test mode and developer mode
  Most gateways allow you to set up a sandbox or developer account which uses a different url
  and credentials. Some also allow you to do test transactions against the live site, which does
  not result in a live transaction.
  
  Gateways that implement only the developer account (most of them) call it testMode. Authorize.net,
  however, implements both and refers to this mode as developerMode.  
  
  When implementing with multiple gateways you should use a construct along the lines of the following:
```php
if ($is_developer_mode) {
    if (method_exists($gateway, 'setDeveloperMode')) {
        $gateway->setDeveloperMode(TRUE);
    } else {
        $gateway->setTestMode(TRUE);
    }
}
```

## Token Billing

Token billing allows you to store a credit card with your gateway, and charge it at a later date.
Token billing is not supported by all gateways. For supported gateways, the following methods
are available:

* `createCard($options)` - returns a response object which includes a `cardReference`, which can be used for future transactions
* `updateCard($options)` - update a stored card, not all gateways support this method
* `deleteCard($options)` - remove a stored card, not all gateways support this method

Once you have a `cardReference`, you can use it instead of the `card` parameter when creating a charge:

    $gateway->purchase(array('amount' => '10.00', 'cardReference' => 'abc'));

## Recurring Billing

At this stage, automatic recurring payments functionality is out of scope for this library.
This is because there is likely far too many differences between how each gateway handles
recurring billing profiles. Also in most cases token billing will cover your needs, as you can
store a credit card then charge it on whatever schedule you like. Feel free to get in touch if
you really think this should be a core feature and worth the effort.

## Incoming Notifications

Some gateways (e.g. Cybersource, GoPay) offer HTTP notifications to inform the merchant about the completion (or, in
general, status) of the payment. To assist with handling such notifications, the `acceptNotification()` method will
extract the transaction reference and payment status from the HTTP request and return a generic `NotificationInterface`.

```php
$notification = $gateway->acceptNotification();

$notification->getTransactionReference(); // A reference provided by the gateway to represent this transaction
$notification->getTransactionStatus(); // Current status of the transaction, one of NotificationInterface::STATUS_*
$notification->getMessage(); // Additional message, if any, provided by the gateway

// update the status of the corresponding transaction in your database
```

**Note:** some earlier gateways used the `completeAuthorize` and `completePurchase` messages to handle the incoming
notifications. These are being converted and the `complete*` messages deprecated.
They won't be removed in OmniPay 2.x, but it is advisable to switch to the `acceptNotification` message when convenient.
An example is Sage Pay Server [completeAuthorize](https://github.com/thephpleague/omnipay-sagepay/blob/master/src/ServerGateway.php#L81)
which is now handled by [acceptNotification](https://github.com/thephpleague/omnipay-sagepay/blob/master/src/ServerGateway.php#L40).

## Example Application

An example application is provided in the [omnipay/example](https://github.com/thephpleague/omnipay-example) repo.
You can run it using PHP's built in web server (PHP 5.4+):

    $ php composer.phar update --dev
    $ php -S localhost:8000

For more information, see the [Omnipay example application](https://github.com/thephpleague/omnipay-example).

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the GitHub issue tracker
for the appropriate package, or better yet, fork the library and submit a pull request.

## Security
If you discover any security related issues, please email barryvdh@gmail.com instead of using the issue tracker.


## Feedback

**Please provide feedback!** We want to make this library useful in as many projects as possible.
Please head on over to the [mailing list](https://groups.google.com/forum/#!forum/omnipay)
and point out what you do and don't like, or fork the project and make suggestions. **No issue is too small.**
