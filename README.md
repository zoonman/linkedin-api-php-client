PHP LinkedIn Client with OAuth 2 authorization
==============================================
[![Build Status](https://travis-ci.org/zoonman/linkedin-client.svg)](https://travis-ci.org/zoonman/linkedin-client) [![Code Climate](https://codeclimate.com/github/zoonman/linkedin-client/badges/gpa.svg)](https://codeclimate.com/github/zoonman/linkedin-client) [![Packagist](https://img.shields.io/packagist/dt/zoonman/linkedin-client.svg)]() [![GitHub license](https://img.shields.io/github/license/zoonman/linkedin-client.svg)]()



See [complete example](examples/index.php) to get started.


## Installation

Use composer package manager

```bash
composer require zoonman/linkedin-client
```

Or add this package as dependency to `composer.json`.


## Usage

To start working with LinkedIn API, you will need to 
get client and secret go to 
[LinkedIn Developers portal](https://developer.linkedin.com/) 
and create new app there.


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

To perform api call to get profile information

```php
$profile = $client->api(
    'people/~:(id,email-address,first-name,last-name)'
);
```
