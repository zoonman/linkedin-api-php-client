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
 * @license  http://www.zoonman.com/projects/linkedin-client/license.txt
 *           linkedin-client License
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
     * Client Id
     * @var string
     */
    protected $clientId;

    /**
     * Client Secret
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
    const API_ROOT = 'https://api.linkedin.com/v2/';

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
     * Use oauth2_access_token parameter instead of Authorization header
     *
     * @var bool
     */
    protected $useTokenParam = false;

    /**
     * @return bool
     */
    public function isUsingTokenParam()
    {
        return $this->useTokenParam;
    }

    /**
     * @param bool $useTokenParam
     *
     * @return Client
     */
    public function setUseTokenParam($useTokenParam)
    {
        $this->useTokenParam = $useTokenParam;
        return $this;
    }

    /**
     * List of default headers
     *
     * @var array
     */
    protected $apiHeaders = [
        'Content-Type' => 'application/json',
        'x-li-format' => 'json',
    ];

    /**
     * Get list of headers
     *
     * @return array
     */
    public function getApiHeaders()
    {
        return $this->apiHeaders;
    }

    /**
     * Set list of default headers
     *
     * @param array $apiHeaders
     *
     * @return Client
     */
    public function setApiHeaders($apiHeaders)
    {
        $this->apiHeaders = $apiHeaders;
        return $this;
    }

    /**
     * Obtain API root URL
     *
     * @return string
     */
    public function getApiRoot()
    {
        return $this->apiRoot;
    }

    /**
     * Specify API root URL
     *
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
     *
     * @return string
     */
    public function getOAuthApiRoot()
    {
        return $this->oAuthApiRoot;
    }

    /**
     * Set OAuth API root
     *
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
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set ClientId
     *
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
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Set Client Secret
     *
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
            $uri = $this->buildUrl('accessToken', []);
            $guzzle = new GuzzleClient([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-li-format' => 'json',
                    'Connection' => 'Keep-Alive'
                ]
            ]);
            try {
                $response = $guzzle->post($uri, ['form_params' => [
                    'grant_type' => self::OAUTH2_GRANT_TYPE,
                    self::OAUTH2_RESPONSE_TYPE => $code,
                    'redirect_uri' => $this->getRedirectUrl(),
                    'client_id' => $this->getClientId(),
                    'client_secret' => $this->getClientSecret(),
                ]]);
            } catch (RequestException $exception) {
                throw Exception::fromRequestException($exception);
            }
            $this->setAccessToken(
                AccessToken::fromResponse($response)
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
    public static function responseToArray($response)
    {
        return \GuzzleHttp\json_decode(
            $response->getBody()->getContents(),
            true
        );
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
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        return $this->getCurrentScheme() . '://' . $host . $path;
    }

    /**
     * Get unique state or specified state
     *
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
     *
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
     * @param array $scope Permissions that your application requires
     *
     * @return string
     */
    public function getLoginUrl(
        array $scope = [Scope::READ_BASIC_PROFILE, Scope::READ_EMAIL_ADDRESS]
    ) {
        $params = [
            'response_type' => self::OAUTH2_RESPONSE_TYPE,
            'client_id' => $this->getClientId(),
            'redirect_uri' => $this->getRedirectUrl(),
            'state' => $this->getState(),
            'scope' => implode(' ', $scope),
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
     * @param array  $params
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
     *
     * @param string $endpoint
     * @param array  $params
     * @param string $method
     *
     * @return array
     * @throws \LinkedIn\Exception
     */
    public function api($endpoint, array $params = [], $method = Method::GET)
    {
        $headers = $this->getApiHeaders();
        $options = $this->prepareOptions($params, $method);
        Method::isMethodSupported($method);
        if ($this->isUsingTokenParam()) {
            $params['oauth2_access_token'] = $this->accessToken->getToken();
        } else {
            $headers['Authorization'] = 'Bearer ' . $this->accessToken->getToken();
        }
        $guzzle = new GuzzleClient([
            'base_uri' => $this->getApiRoot(),
            'headers' => $headers,
        ]);
        if (!empty($params) && Method::GET === $method) {
            $endpoint .= '?' . build_query($params);
        }
        try {
            $response = $guzzle->request($method, $endpoint, $options);
        } catch (RequestException $requestException) {
            throw Exception::fromRequestException($requestException);
        }
        return self::responseToArray($response);
    }

    /**
     * Make API call to LinkedIn using GET method
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return array
     * @throws \LinkedIn\Exception
     */
    public function get($endpoint, array $params = [])
    {
        return $this->api($endpoint, $params, Method::GET);
    }

    /**
     * Make API call to LinkedIn using POST method
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return array
     * @throws \LinkedIn\Exception
     */
    public function post($endpoint, array $params = [])
    {
        return $this->api($endpoint, $params, Method::POST);
    }

    /**
     * Make API call to LinkedIn using DELETE method
     *
     * @param string $endpoint
     * @param array  $params
     *
     * @return array
     * @throws \LinkedIn\Exception
     */
    public function delete($endpoint, array $params = [])
    {
        return $this->api($endpoint, $params, Method::DELETE);
    }

    /**
     * @param $path
     * @return array
     * @throws Exception
     */
    public function upload($path)
    {
        $headers = $this->getApiHeaders();
        unset($headers['Content-Type']);
        if (!$this->isUsingTokenParam()) {
            $headers['Authorization'] = 'Bearer ' . $this->accessToken->getToken();
        }
        $guzzle = new GuzzleClient([
            'base_uri' => $this->getApiRoot()
        ]);
        $fileinfo = pathinfo($path);
        $filename = preg_replace('/\W+/', '_', $fileinfo['filename']);
        if (isset($fileinfo['extension'])) {
            $filename .= '.' . $fileinfo['extension'];
        }
        $options = [
            'multipart' => [
                [
                    'name' => 'source',
                    'filename' => $filename,
                    'contents' => fopen($path, 'r')
                ]
            ],
            'headers' => $headers,
        ];
        try {
            $response = $guzzle->request(Method::POST, 'media/upload', $options);
        } catch (RequestException $requestException) {
            throw Exception::fromRequestException($requestException);
        }
        return self::responseToArray($response);
    }

    /**
     * @param array $params
     * @param string $method
     * @return mixed
     */
    protected function prepareOptions(array $params, $method)
    {
        $options = [];
        if ($method === Method::POST) {
            $options['body'] = \GuzzleHttp\json_encode($params);
        }
        return $options;
    }
}
