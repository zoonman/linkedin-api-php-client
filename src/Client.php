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

/**
 * Class Client
 *
 * @package LinkedIn
 */
class Client
{

    const OAUTH2_GRANT_TYPE = 'authorization_code';

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
     * string
     */
    const OAUTH2_API_ROOT = 'https://www.linkedin.com/oauth/v2/';
    const API_ROOT = 'https://api.linkedin.com/v1/';

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
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
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
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
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
     * @param string $code
     *
     * @return \LinkedIn\AccessToken
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
               'base_uri' => self::OAUTH2_API_ROOT,
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
                    $json['error_description']
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
     *
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
     * @param string $accessToken
     *
     * @return Client
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
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
     * @return string
     */
    public function getCurrentUrl()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']  : 'localhost';
        $path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI']  : '/';
        return $this->getCurrentScheme() .'://'. $host .  $path;
    }

    /**
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
     * @param mixed $state
     *
     * @return Client
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
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
        $scheme = parse_url(self::OAUTH2_API_ROOT, PHP_URL_SCHEME);
        $authority = parse_url(self::OAUTH2_API_ROOT, PHP_URL_HOST);
        $path = parse_url(self::OAUTH2_API_ROOT, PHP_URL_PATH);
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
     * @param string $endpoint
     * @param array  $params
     * @param string $method
     *
     * @return array
     * @throws \LinkedIn\Exception
     */
    public function api($endpoint, array $params = array(), $method = 'GET')
    {
        $guzzle = new GuzzleClient([
            'base_uri' => self::API_ROOT,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken->getToken(),
                'Content-Type' => 'application/json',
                'x-li-format' => 'json'
            ]
        ]);
        $uri = $endpoint;
        //$params['oauth2_access_token'] = $this->accessToken->getToken();
        if (!empty($params)) {
            $uri .= '?' . build_query($params);
        }

        try {
            $response = $guzzle->request($method, $uri);
        } catch (RequestException $requestException) {
            $json = self::responseToArray(
                $requestException->getResponse()
            );
            $lnException = new Exception(
                $requestException->getMessage(),
                $requestException->getCode(),
                $requestException,
                $json['error_description']
            );
            throw $lnException;
        }
        return self::responseToArray($response);
    }
}
