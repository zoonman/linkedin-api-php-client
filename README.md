### Attention!

This a fork from the original code from [Zoonman](https://github.com/zoonman/linkedin-api-php-client), so almost all the credit for this library goes for him.

I just made a single change in order to adapt the code for avoid an error pusblishing a new post, because Linkedin has migrated the api to the V2 Version.

I still have to addapt some other changes (like adding new scopes for the V2 version), but I thought it would be better to publish a new package, so it can be used with composer without facing the commented error.

Most of the examples from the original Readme are deprecated, and they should be fixed too.


LinkedIn API Client with OAuth 2 authorization written on PHP
============================================================
[![Build Status](https://travis-ci.org/zoonman/linkedin-api-php-client.svg?branch=master)](https://travis-ci.org/zoonman/linkedin-api-php-client) [![Code Climate](https://codeclimate.com/github/zoonman/linkedin-api-php-client/badges/gpa.svg)](https://codeclimate.com/github/zoonman/linkedin-api-php-client) [![Packagist](https://img.shields.io/packagist/dt/zoonman/linkedin-api-php-client.svg)](https://packagist.org/packages/zoonman/linkedin-api-php-client) [![GitHub license](https://img.shields.io/github/license/zoonman/linkedin-api-php-client.svg)](https://github.com/zoonman/linkedin-api-php-client/blob/master/LICENSE.md)



See [complete example](examples/) inside [index.php](examples/index.php) to get started.


## Installation

You will need at least PHP 5.6. PHP 5.5 was deprecated more than a year ago! This is the time for upgrade.

Use [composer](https://getcomposer.org/) package manager to install the lastest version of the package:

```bash
composer require zoonman/linkedin-api-php-client
```

Or add this package as dependency to `composer.json`.

If you have never used Composer, you should start [here](http://www.phptherightway.com/#composer_and_packagist)
and install composer.


## Usage

To start working with LinkedIn API, you will need to 
get application client id and secret. 

Go to [LinkedIn Developers portal](https://developer.linkedin.com/) 
and create new application in section My Apps.

All the documentation about using the api now resides in this [documentation](https://docs.microsoft.com/en-us/linkedin/marketing/index)

#### Bootstrapping autoloader and instantiating a client


```php
// ... please, add composer autoloader first
include_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// import client class
use LinkedIn\Client;

// instantiate the Linkedin client
$client = new Client(
    'YOUR_LINKEDIN_APP_CLIENT_ID',
    'YOUR_LINKEDIN_APP_CLIENT_SECRET'
);
```

#### Getting local redirect URL

To start linking process you have to setup redirect url. 
You can set your own or use current one.
SDK provides you a `getRedirectUrl()` helper for your convenience:

```php
$redirectUrl = $client->getRedirectUrl();
```

We recommend you to have it stored during the linking session 
because you will need to use it when you will be getting access token.

#### Setting local redirect URL 

Set a custom redirect url use:

```php
$client->setRedirectUrl('http://your.domain.tld/path/to/script/');
```

#### Getting LinkedIn redirect URL 

In order of performing OAUTH 2.0 flow, you should get LinkedIn login URL.
During this procedure you have to define scope of requested permissions.
Use `Scope` enum class to get scope names.
To get redirect url to LinkedIn, use the following approach:

```php
use LinkedIn\Scope;

// define scope
$scopes = [
  Scope::READ_BASIC_PROFILE, 
  Scope::READ_EMAIL_ADDRESS,
  Scope::MANAGE_COMPANY,
  Scope::SHARING,
];
$loginUrl = $client->getLoginUrl($scopes); // get url on LinkedIn to start linking
```

Now you can take user to LinkedIn. You can use link or rely on Location HTTP header.

#### Getting Access Token 

To get access token use (don't forget to set redirect url)

```php
$accessToken = $client->getAccessToken($_GET['code']);
```
This method returns object of `LinkedIn\AccessToken` class. 
You can store this token in the file like this:
```php
file_put_contents('token.json', json_encode($accessToken));
```
This way of storing tokens is not recommended due to security concerns and used for demonstration purpose. 
Please, ensure that tokens are stored securely. 

#### Setting Access Token

You can use method `setAccessToken()` for the `LinkedIn\Client` class to set token stored as string. You have to pass
instance of `LinkedIn\AccessToken` to this method.

```php
use LinkedIn\AccessToken;
use LinkedIn\Client;

// instantiate the Linkedin client
$client = new Client(
    'LINKEDIN_APP_CLIENT_ID',  
    'LINKEDIN_APP_CLIENT_SECRET'
);

// load token from the file
$tokenString = file_get_contents('token.json');
$tokenData = json_decode($tokenString, true);
// instantiate access token object from stored data
$accessToken = new AccessToken($tokenData['token'], $tokenData['expiresAt']);

// set token for client
$client->setAccessToken($accessToken);
```

#### Performing API calls 

All API calls can be called through simple method:

```php
$profile = $client->api(
    'ENDPOINT',
    ['parameter name' => 'its value here'],
    'HTTP method like GET for example'
);
```

There are 3 helper methods:

```php
// get method
$client->get('ENDPOINT', ['param' => 'value']);

//post
$client->post('ENDPOINT', ['param' => 'value']);

// delete
$client->delete('ENDPOINT');
```

#### Examples

##### Perform api call to get profile information

```php
$profile = $client->get(
    'people/~:(id,email-address,first-name,last-name)'
);
print_r($profile);
```

##### List companies where you are an admin

```php
$profile = $client->get(
    'companies',
    ['is-company-admin' => true]
);
print_r($profile);
```

##### Share content on a personal profile

Make sure that image URL is available from the Internet (don't use localhost in the image url).

```php
$share = $client->post(
    'people/~/shares',
    [
        'comment' => 'Checkout this amazing PHP SDK for LinkedIn!',
        'content' => [
            'title' => 'PHP Client for LinkedIn API',
            'description' => 'OAuth 2 flow, composer Package',
            'submitted-url' => 'https://github.com/zoonman/linkedin-api-php-client',
            'submitted-image-url' => 'https://github.com/fluidicon.png',
        ],
        'visibility' => [
            'code' => 'anyone'
        ]
    ]
);
print_r($share);
```

##### Get Company page profile

```php
$companyId = '123'; // use id of the company where you are an admin
$companyInfo = $client->get('companies/' . $companyId . ':(id,name,num-followers,description)');
print_r($companyInfo);
```

##### Share content on a LinkedIn business page

```php
// set sandboxed company page to work with
// you can check updates at
// https://www.linkedin.com/company/devtestco
$companyId = '2414183';

$share = $client->post(
    'companies/' . $companyId . '/shares',
    [
        'comment' => 'Checkout this amazing PHP SDK for LinkedIn!',
        'content' => [
            'title' => 'PHP Client for LinkedIn API',
            'description' => 'OAuth 2 flow, composer Package',
            'submitted-url' => 'https://github.com/zoonman/linkedin-api-php-client',
            'submitted-image-url' => 'https://github.com/fluidicon.png',
        ],
        'visibility' => [
            'code' => 'anyone'
        ]
    ]
);
print_r($share);
```

##### Setup custom API request headers

Change different headers sent to LinkedIn API.

```php
$client->setApiHeaders([
  'Content-Type' => 'application/json',
  'x-li-format' => 'json',
  'X-Restli-Protocol-Version' => '2.0.0', // use protocol v2
  'x-li-src' => 'msdk' // set a src header to "msdk" to mimic a mobile SDK
]);
```

##### Change default API root

Some private API access there.

```php
$client->setApiRoot('https://api.linkedin.com/v2/');
```

##### Image Upload

I assume you have to be LinkedIn partner or something like that.

Try to upload image to LinkedIn. See [Rich Media Shares](https://developer.linkedin.com/docs/guide/v2/shares/rich-media-shares#upload)
(returns "Not enough permissions to access media resource" for me). 

```php
$filename = '/path/to/image.jpg';
$client->setApiRoot('https://api.linkedin.com/');
$mp = $client->upload($filename);
```

## Contributing

Please, open PR with your changes linked to an GitHub issue.
You code must follow [PSR](http://www.php-fig.org/psr/) standards and have PHPUnit tests. 

## License

[MIT](LICENSE.md)
