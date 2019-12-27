<?php
/**
 * linkedin-client
 * index.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Philipp Tkachev <philipp@zoonman.com>
 * @date     8/17/17 22:47
 * @license  http://zoonman.com/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://zoonman.com/projects/linkedin-client
 */

// add Composer autoloader
include_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

// import client class
use LinkedIn\Client;
use LinkedIn\Scope;

// import environment variables from the environment file
// you need a .env file in the parent folder
// read this document to learn how to create that file
// https://github.com/zoonman/linkedin-api-php-client/blob/master/examples/README.md
//
$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

// we need a session to keep intermediate results
// you can use your own session persistence management
// client doesn't depend on it
session_start();

// instantiate the Linkedin client
// you can setup keys using
$client = new Client(
    getenv('LINKEDIN_CLIENT_ID'),
    getenv('LINKEDIN_CLIENT_SECRET')
);


if (isset($_GET['code'])) { // we are returning back from LinkedIn with the code
    if (isset($_GET['state']) &&  // and state parameter in place
        isset($_SESSION['state']) && // and we have have stored state
        $_GET['state'] === $_SESSION['state'] // and it is our request
    ) {
        try {
            // you have to set initially used redirect url to be able
            // to retrieve access token
            $client->setRedirectUrl($_SESSION['redirect_url']);
            // retrieve access token using code provided by LinkedIn
            $accessToken = $client->getAccessToken($_GET['code']);
            h1('Access token');
            pp($accessToken); // print the access token content
            h1('Profile');
            // perform api call to get profile information
            $profile = $client->get(        
                'me',                       
                ['fields' => 'id,firstName,lastName']
            ); 
            pp($profile); // print profile information
            
            $emailInfo = $email = $client->get('emailAddress', ['q' => 'members', 'projection' => '(elements*(handle~))']);
            pp($emailInfo);

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
            pp($share);

            // set sandboxed company page id to work with
            // https://www.linkedin.com/company/devtestco
            /* TODO!
            $companyId = '2414183';

            h1('Company information');
            $companyInfo = $client->get('companies/' . $companyId . ':(id,name,num-followers,description)');
            pp($companyInfo);

            h1('Sharing on company page');
            $companyShare = $client->post(
                'companies/' . $companyId . '/shares',
                [
                    'comment' =>
                        sprintf(
                            '%s %s just tried this amazing PHP SDK for LinkedIn!',
                            $profile['firstName'],
                            $profile['lastName']
                        ),
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
            pp($companyShare);
            */

            /*
            // Returns {"serviceErrorCode":100,"message":"Not enough permissions to access media resource","status":403}
            // You have to be whitelisted or so by LinkedIn
            $filename = './demo.jpg';
            $client->setApiRoot('https://api.linkedin.com/');
            $mp = $client->upload($filename);
            */
        } catch (\LinkedIn\Exception $exception) {
            // in case of failure, provide with details
            pp($exception);
            pp($_SESSION);
        }
        echo '<a href="/">Start over</a>';
    } else {
        // normally this shouldn't happen unless someone sits in the middle
        // and trying to override your state
        // or you are trying to change saved state during linking
        echo 'Invalid state!';
        pp($_GET);
        pp($_SESSION);
        echo '<a href="/">Start over</a>';
    }

} elseif (isset($_GET['error'])) {
    // if you cancel during linking
    // you will be redirected back with reason
    pp($_GET);
    echo '<a href="/">Start over</a>';
} else {
    // define desired list of scopes
    $scopes = [
        Scope::READ_LITE_PROFILE,
        Scope::READ_EMAIL_ADDRESS,
        Scope::SHARE_AS_USER,
    ];
    $loginUrl = $client->getLoginUrl($scopes); // get url on LinkedIn to start linking
    $_SESSION['state'] = $client->getState(); // save state for future validation
    $_SESSION['redirect_url'] = $client->getRedirectUrl(); // save redirect url for future validation
    echo 'LoginUrl: <a href="'.$loginUrl.'">' . $loginUrl. '</a>';
}

/**
 * Pretty print whatever passed in
 *
 * @param mixed $anything
 */
function pp($anything)
{
    echo '<pre>' . print_r($anything, true) . '</pre>';
}

/**
 * Add header
 *
 * @param string $h
 */
function h1($h) {
    echo '<h1>' . $h . '</h1>';
}
