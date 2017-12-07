<?php
/**
 * linkedin-client
 * Client.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Philipp Tkachev <philipp@zoonman.com>
 * @date     8/17/17 18:50
 * @license  http://www.zoonman.com/projects/linkedin-client/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://www.zoonman.com/projects/linkedin-client/
 */

namespace LinkedIn;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use function GuzzleHttp\Psr7\build_query;
use GuzzleHttp\Psr7\Uri;
use LinkedIn\Http\Method;

/**
 * Class Client
 *
 * @package LinkedIn
 */
class Client
{

    /**
     * Grant type
     */
    const OAUTH2_GRANT_TYPE = 'authorization_code';

    /**
     * Response type
     */
    const OAUTH2_RESPONSE_TYPE = 'code';

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var \LinkedIn\AccessToken
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string The URI your users will be sent back to after
     *                            authorization.  This value must match one of
     *                            the defined OAuth 2.0 Redirect URLs in your
     *                            application configuration.
     */
    protected $redirectUrl;

    /**
     * Default authorization URL
     * string
     */
    const OAUTH2_API_ROOT = 'https://www.linkedin.com/oauth/v2/';

    /**
     * Default API root URL
     * string
     */
    const API_ROOT = 'https://api.linkedin.com/v1/';

    /**
     * API Root URL
     *
     * @var string
     */
    protected $apiRoot = self::API_ROOT;

    /**
     * OAuth API URL
     *
     * @var string
     */
    protected $oAuthApiRoot = self::OAUTH2_API_ROOT;

    /**
     * List of default headers
     * @var array
     */
    protected $defaultApiHeaders = [
        'Content-Type' => 'application/json',
        'x-li-format' => 'json'
    ];

    /**
     * Get list of headers
     * @return array
     */
    public function getDefaultApiHeaders()
    {
        return $this->defaultApiHeaders;
    }

    /**
     * Set list of default headers
     * @param array $defaultApiHeaders
     *
     * @return Client
     */
    public function setDefaultApiHeaders($defaultApiHeaders)
    {
        $this->defaultApiHeaders = $defaultApiHeaders;
        return $this;
    }

    /**
     * Obtain API root URL
     * @return string
     */
    public function getApiRoot()
    {
        return $this->apiRoot;
    }

    /**
     * Specify API root URL
     * @param string $apiRoot
     *
     * @return Client
     */
    public function setApiRoot($apiRoot)
    {
        $this->apiRoot = $apiRoot;
        return $this;
    }

    /**
     * Get OAuth API root
     * @return string
     */
    public function getOAuthApiRoot()
    {
        return $this->oAuthApiRoot;
    }

    /**
     * Set OAuth API root
     * @param string $oAuthApiRoot
     *
     * @return Client
     */
    public function setOAuthApiRoot($oAuthApiRoot)
    {
        $this->oAuthApiRoot = $oAuthApiRoot;
        return $this;
    }

    /**
     * Client constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct($clientId = '', $clientSecret = '')
    {
        !empty($clientId) && $this->setClientId($clientId);
        !empty($clientSecret) && $this->setClientSecret($clientSecret);
    }

    /**
     * Get ClientId
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set ClientId
     * @param string $clientId
     *
     * @return Client
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * Get Client Secret
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Set Client Secret
     * @param string $clientSecret
     *
     * @return Client
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * Retrieve Access Token from LinkedIn if we have code provided.
     * If code is not provided, return current Access Token.
     * If current access token is not set, will return null
     *
     * @param string $code
     *
     * @return \LinkedIn\AccessToken|null
     * @throws \LinkedIn\Exception
     */
    public function getAccessToken($code = '')
    {
        if (!empty($code)) {
            $params = [
                'grant_type' => self::OAUTH2_GRANT_TYPE,
                self::OAUTH2_RESPONSE_TYPE => $code,
                'redirect_uri' => $this->getRedirectUrl(),
                'client_id' => $this->getClientId(),
                'client_secret' => $this->getClientSecret()
            ];
            $uri = $this->buildUrl('accessToken', $params);
            $guzzle = new GuzzleClient([
               'base_uri' => $this->getOAuthApiRoot(),
               'headers' => [
                    'Content-Type' => 'application/json',
                    'x-li-format' => 'json'
                ]
            ]);
            try {
                $response = $guzzle->get($uri);
            } catch (RequestException $requestException) {
                $json = self::responseToArray(
                    $requestException->getResponse()
                );
                $lnException = new Exception(
                    $requestException->getMessage(),
                    $requestException->getCode(),
                    $requestException,
                    static::extractErrorDescription($json)
                );
                throw $lnException;
            }
            $json = self::responseToArray($response);
            $this->setAccessToken(
                AccessToken::fromResponseArray($json)
            );
        }
        return $this->accessToken;
    }

    /**
     * Convert API response into Array
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return array
     */
    protected static function responseToArray($response)
    {
        $body = $response->getBody();
        $content = $body->getContents();
        return \GuzzleHttp\json_decode($content, true);
    }

    /**
     * Set AccessToken object
     *
     * @param AccessToken|string $accessToken
     *
     * @return Client
     */
    public function setAccessToken($accessToken)
    {
        if (is_string($accessToken)) {
            $accessToken = new AccessToken($accessToken);
        }
        if (is_object($accessToken) && $accessToken instanceof AccessToken) {
            $this->accessToken = $accessToken;
        } else {
            throw new \InvalidArgumentException('$accessToken must be instance of \LinkedIn\AccessToken class');
        }
        return $this;
    }

    /**
     * Retrieve current active scheme
     *
     * @return string
     */
    protected function getCurrentScheme()
    {
        $scheme = 'http';
        if (isset($_SERVER['HTTPS']) && "on" === $_SERVER["HTTPS"]) {
            $scheme = 'https';
        }
        return $scheme;
    }

    /**
     * Get current URL
     * @return string
     */
    public function getCurrentUrl()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']  : 'localhost';
        $path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI']  : '/';
        return $this->getCurrentScheme() .'://'. $host .  $path;
    }

    /**
     * Get unique state or specified state
     * @return string
     */
    public function getState()
    {
        if (empty($this->state)) {
            $this->setState(
                rtrim(
                    base64_encode(uniqid('', true)),
                    '='
                )
            );
        }
        return $this->state;
    }

    /**
     * Set State
     * @param string $state
     *
     * @return Client
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Retrieve URL which will be used to send User to LinkedIn
     * for authentication
     *
     * @param array  $scope       Permissions that your application requires
     *
     * @return string
     */
    public function getLoginUrl(array $scope = array('r_basicprofile', 'r_emailaddress'))
    {

        $params = [
            'response_type' => self::OAUTH2_RESPONSE_TYPE,
            'client_id' => $this->getClientId(),
            'redirect_uri' => $this->getRedirectUrl(),
            'state' => $this->getState(),
            'scope' => implode(' ', $scope)
        ];
        $uri = $this->buildUrl('authorization', $params);
        return $uri;
    }

    /**
     * @return string The URI your users will be sent back to after
     *                            authorization.  This value must match one of
     *                            the defined OAuth 2.0 Redirect URLs in your
     *                            application configuration.
     */
    public function getRedirectUrl()
    {
        if (empty($this->redirectUrl)) {
            $this->setRedirectUrl($this->getCurrentUrl());
        }
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl The URI your users will be sent back to after
     *                            authorization.  This value must match one of
     *                            the defined OAuth 2.0 Redirect URLs in your
     *                            application configuration.
     *
     * @return Client
     */
    public function setRedirectUrl($redirectUrl)
    {
        $redirectUrl = filter_var($redirectUrl, FILTER_VALIDATE_URL);
        if (false === $redirectUrl) {
            throw new \InvalidArgumentException('The argument is not an URL');
        }
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * @param string $endpoint
     * @param array $params
     *
     * @return string
     */
    protected function buildUrl($endpoint, $params)
    {
        $url = $this->getOAuthApiRoot();
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $authority = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH);
        $path .= trim($endpoint, '/');
        $fragment = '';
        $uri = Uri::composeComponents(
            $scheme,
            $authority,
            $path,
            build_query($params),
            $fragment
        );
        return $uri;
    }

    /**
     * Perform API call to LinkedIn
     * @param string $endpoint
     * @param array  $params
     * @param string $method
     *
     * @return array
     * @throws \LinkedIn\Exception
     */
    public function api($endpoint, array $params = array(), $method = Method::GET)
    {
        $headers = $this->getDefaultApiHeaders();
        $headers['Authorization'] = 'Bearer ' . $this->accessToken->getToken();
        $guzzle = new GuzzleClient([
            'base_uri' => $this->getApiRoot(),
            'headers' => $headers
        ]);
        $uri = $endpoint;
        $options = [];
        //$params['oauth2_access_token'] = $this->accessToken->getToken();
        switch ($method) {
            case Method::GET:
                if (!empty($params)) {
                    $uri .= '?' . build_query($params);
                }
                break;
            case Method::POST:
                $options['body'] = \GuzzleHttp\json_encode($params);
                break;
            default:
                throw new Exception(
                    "Method not defined",
                    1,
                    null,
                    "Please, pass correct method!"
                );
        }

        try {
            $response = $guzzle->request($method, $uri, $options);
        } catch (RequestException $requestException) {
            $json = self::responseToArray(
                $requestException->getResponse()
            );
            $lnException = new Exception(
                $requestException->getMessage(),
                $requestException->getCode(),
                $requestException,
                static::extractErrorDescription($json)
            );
            throw $lnException;
        }
        return self::responseToArray($response);
    }

    /**
     * @param $json
     *
     * @return null|string
     */
    private static function extractErrorDescription($json)
    {
      if (isset($json['error_description'])) {
        return $json['error_description'];
      } elseif (isset($json['message'])) {
        return $json['message'];
      } else {
        return null;
      }
    }

    /**
     * Make API call to LinkedIn using GET method
     * @param string $endpoint
     * @param array $params
     *
     * @return array
     */
    public function get($endpoint, array $params = array())
    {
        return $this->api($endpoint, $params, Method::GET);
    }

    /**
     * Make API call to LinkedIn using POST method
     * @param string $endpoint
     * @param array $params
     *
     * @return array
     */
    public function post($endpoint, array $params = array())
    {
        return $this->api($endpoint, $params, Method::POST);
    }
}
