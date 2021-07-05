LinkedIn API Client with OAuth 2 authorization written on PHP
============================================================
[![Build Status](https://travis-ci.org/zoonman/linkedin-api-php-client.svg?branch=master)](https://travis-ci.org/zoonman/linkedin-api-php-client) [![Code Climate](https://codeclimate.com/github/zoonman/linkedin-api-php-client/badges/gpa.svg)](https://codeclimate.com/github/zoonman/linkedin-api-php-client) [![Packagist](https://img.shields.io/packagist/dt/zoonman/linkedin-api-php-client.svg)](https://packagist.org/packages/zoonman/linkedin-api-php-client) [![GitHub license](https://img.shields.io/github/license/zoonman/linkedin-api-php-client.svg)](https://github.com/zoonman/linkedin-api-php-client/blob/master/LICENSE.md)



See [complete example](examples/) inside [index.php](examples/index.php) to get started.


## Installation

You will need at least PHP 7.3. We match [officially supported](https://www.php.net/supported-versions.php) versions of PHP.

Use [composer](https://getcomposer.org/) package manager to install the lastest version of the package:

```bash
composer require omitech/linkedin-api-php-client
```

Or add this package as dependency to `composer.json`.

If you have never used Composer, you should start [here](http://www.phptherightway.com/#composer_and_packagist)
and install composer.


## Get Started

Before you will get started, play visit to [LinkedIn API Documentation](https://docs.microsoft.com/en-us/linkedin/).
This will save you a lot of time and prevent some silly questions.

To start working with LinkedIn API, you will need to 
get application client id and secret. 

Go to [LinkedIn Developers portal](https://developer.linkedin.com/) 
and create new application in section My Apps. 
Save ClientId and ClientSecret, you will use them later.


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
  Scope::READ_LITE_PROFILE, 
  Scope::READ_EMAIL_ADDRESS,
  Scope::SHARE_AS_USER,
  Scope::SHARE_AS_ORGANIZATION,
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
$accessToken = new AccessToken($tokenData['token'], $tokenData['expiresAt'], $tokenData['refreshToken'], $tokenData['refreshTokenExpiresAt']);

// set token for client
if (!$accessToken->isExpired()) {
    $client->setAccessToken($accessToken);
} elseif (!$accessToken->isRefreshTokenExpired()) {
    $accessToken = $client->refreshAccessToken($accessToken);

    file_put_contents(__DIR__ . '/token.json', json_encode($accessToken));
} else {
    echo "try to login again\n";
    $client->setRedirectUrl('https://sciencex.com/Newsman3/extra/oauth/');

    $scopes = [
        Scope::READ_LITE_PROFILE,
        Scope::READ_EMAIL_ADDRESS,
        Scope::SHARE_AS_USER,
        Scope::SHARE_AS_ORGANIZATION,
    ];

    $loginUrl = $client->getLoginUrl($scopes);

    echo $loginUrl;
    die();
}
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
    'me',
    ['fields' => 'id,firstName,lastName']
);
print_r($profile);
```

##### List companies where you are an admin

```php
$profile = $client->get(
    'organizations',
    ['is-company-admin' => true]
);
print_r($profile);
```

##### Share content on a personal profile

Make sure that image URL is available from the Internet (don't use localhost in the image url).

```php
$share = $client->post(                 
                'ugcPosts',                         
                [                                   
                    'author' => 'urn:li:person:' . $profile['id'],
                    'lifecycleState' => 'PUBLISHED',
                    'specificContent' => [          
                        'com.linkedin.ugc.ShareContent' => [
                            'shareCommentary' => [
                                'text' => 'Checkout this amazing PHP SDK for LinkedIn!'
                            ],
                            'shareMediaCategory' => 'ARTICLE',
                            'media' => [
                                [
                                    'status' => 'READY',
                                    'description' => [
                                        'text' => 'OAuth 2 flow, composer Package.'
                                    ],
                                    'originalUrl' => 'https://github.com/zoonman/linkedin-api-php-client',
                                    'title' => [
                                        'text' => 'PHP Client for LinkedIn API'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'visibility' => [
                        'com.linkedin.ugc.MemberNetworkVisibility' => 'CONNECTIONS'
                    ]
                ]
            );
print_r($share);
```

##### Get Company page profile

```php
$companyId = '123'; // use id of the company where you are an admin
$companyInfo = $client->get('organizations/' . $companyId);
print_r($companyInfo);
```

##### Share content on a LinkedIn business page

```php
// set sandboxed company page to work with
// you can check updates at
// https://www.linkedin.com/company/devtestco
$companyId = '2414183';

$share = $client->post(                 
                'ugcPosts',                         
                [                                   
                    'author' => 'urn:li:organization:' . $companyId,
                    'lifecycleState' => 'PUBLISHED',
                    'specificContent' => [          
                        'com.linkedin.ugc.ShareContent' => [
                            'shareCommentary' => [
                                'text' => 'Checkout this amazing PHP SDK for LinkedIn!'
                            ],
                            'shareMediaCategory' => 'ARTICLE',
                            'media' => [
                                [
                                    'status' => 'READY',
                                    'description' => [
                                        'text' => 'OAuth 2 flow, composer Package.'
                                    ],
                                    'originalUrl' => 'https://github.com/zoonman/linkedin-api-php-client',
                                    'title' => [
                                        'text' => 'PHP Client for LinkedIn API'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'visibility' => [
                        'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
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

##### ~Image Upload~ 

I assume you have to be LinkedIn partner or something like that.

Try to upload image to LinkedIn. See [Rich Media Shares](https://docs.microsoft.com/en-us/linkedin/marketing/integrations/community-management/shares/rich-media-shares)
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
