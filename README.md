LinkedIn API Client with OAuth 2 authorization witten on PHP
============================================================
[![Build Status](https://travis-ci.org/zoonman/linkedin-client.svg)](https://travis-ci.org/zoonman/linkedin-client) [![Code Climate](https://codeclimate.com/github/zoonman/linkedin-client/badges/gpa.svg)](https://codeclimate.com/github/zoonman/linkedin-client) [![Packagist](https://img.shields.io/packagist/dt/zoonman/linkedin-client.svg)](https://packagist.org/packages/zoonman/linkedin-client) [![GitHub license](https://img.shields.io/github/license/zoonman/linkedin-client.svg)](https://github.com/zoonman/linkedin-client/LICENSE.md)



See [complete example](examples/) inside [index.php](examples/index.php) to get started.


## Installation

Use composer package manager

```bash
composer require zoonman/linkedin-client
```

Or add this package as dependency to `composer.json`.

If you have never used Composer, you should start [here](http://www.phptherightway.com/#composer_and_packagist)
and install composer.


## Usage

To start working with LinkedIn API, you will need to 
get application client id and secret. 

Go to [LinkedIn Developers portal](https://developer.linkedin.com/) 
and create new application in section My Apps.


#### Bootstrapping autoloader and instantiating a client


```php
// ... please, add composer autoloader first
include_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// import client class
use LinkedIn\Client;

// instantiate the Linkedin client
$client = new Client(
    'LINKEDIN_APP_CLIENT_ID',  
    'LINKEDIN_APP_CLIENT_SECRET'
);
```

#### Getting local redirect URL

Get current redirect url use

```php
$client->getRedirectUrl();
```


#### Setting local redirect URL 

To set redirect url use

```php
$client->setRedirectUrl('http://your.domain.tld/path/to/script/');
```

#### Getting LinkedIn redirect URL 

To get redirect url to LinkedIn, use the following approach:

```php
// define scope
$scopes = [
    'r_basicprofile',
    'r_emailaddress',
    'rw_company_admin',
    'w_share',
];
$loginUrl = $client->getLoginUrl(); // get url on LinkedIn to start linking
```

#### Getting Access Token 

To get access token use (don't forget to set redirect url)

```php
$accessToken = $client->getAccessToken($_GET['code']);
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

To perform api call to get profile information

```php
$profile = $client->get(
    'people/~:(id,email-address,first-name,last-name)'
);
print_r($profile);
```

To list companies where you an admin

```php
$profile = $client->get(
    'people/~:(id,email-address,first-name,last-name)'
);
print_r($profile);
```

To share content

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
```
