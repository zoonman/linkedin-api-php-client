<?php
/**
 * linkedin-client
 * AccessToken.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Philipp Tkachev <philipp@zoonman.com>
 * @date     8/17/17 22:55
 * @license  http://www.zoonman.com/projects/linkedin-client/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://www.zoonman.com/projects/linkedin-client/
 */

namespace LinkedIn;

/**
 * Class AccessToken
 *
 * @package LinkedIn
 */
class AccessToken implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $token;

    /**
     * When token will expire.
     *
     * Please, pay attention that LinkedIn API always returns "expires in" time,
     * which is amount of seconds before token will expire since now.
     * If you are going to store token somewhere, you have to keep "expires at"
     * or two values - "expires in" and "token created".
     * Using "expires at" approach lets you have efficient queries to find
     * tokens will soon expire and be proactive with regards to your
     * B2C communication.
     *
     * @var int
     */
    protected $expiresAt;

    /**
     *
     * @var string
     */
    protected $refreshToken;

    /**
     * @var int
     */
    protected $refreshTokenExpiresAt;

    /**
     * AccessToken constructor.
     *
     * @param string $token
     * @param int    $expiresAt
     * @param string $refreshToken
     * @param int    $refreshTokenExpiresAt
     */
    public function __construct($token = '', $expiresAt = 0, $refreshToken = '', $refreshTokenExpiresAt = 0)
    {
        $this->setToken($token);
        $this->setExpiresAt($expiresAt);

        $this->setRefreshToken($refreshToken);
        $this->setRefreshTokenExpiresAt($refreshTokenExpiresAt);
    }

    /**
     * Get token string
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get RefreshToken string
     *
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Set token string
     *
     * @param string $token
     *
     * @return AccessToken
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Set RefreshToken string
     *
     * @param string $token
     *
     * @return AccessToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    /**
     * The number of seconds remaining, from the time it was requested, before the token will expire.
     *
     * @return int seconds
     */
    public function getExpiresIn()
    {
        return $this->expiresAt - time();
    }

    /**
     * @return int seconds
     */
    public function getRefreshTokenExpiresIn()
    {
        return $this->refreshTokenExpiresAt - time();
    }

    /**
     * Set token expiration time
     *
     * @param int $expiresIn amount of seconds before expiration
     *
     * @return AccessToken
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresAt = $expiresIn + time();
        return $this;
    }

    /**
     *
     * @param int $expiresIn amount of seconds before expiration
     *
     * @return AccessToken
     */
    public function setRefreshTokenExpiresIn($expiresIn)
    {
        $this->refreshTokenExpiresAt = $expiresIn + time();
        return $this;
    }

    /**
     * Dynamically typecast token object into string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getToken();
    }

    /**
     * Get Unix epoch time when token will expire
     *
     * @return int
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     *
     * @return int
     */
    public function getRefreshTokenExpiresAt()
    {
        return $this->refreshTokenExpiresAt;
    }

    /**
     * Set Unix epoch time when token will expire
     *
     * @param int $expiresAt seconds, unix time
     *
     * @return AccessToken
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @param int $expiresAt seconds, unix time
     *
     * @return AccessToken
     */
    public function setRefreshTokenExpiresAt($expiresAt)
    {
        $this->refreshTokenExpiresAt = $expiresAt;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isExpired()
    {
        return $this->expiresAt < time() + 60 * 5;
    }

    /**
     *
     * @return boolean
     */
    public function isRefreshTokenExpired()
    {
        return !$this->refreshTokenExpiresAt || $this->refreshTokenExpiresAt < time() + 60 * 5;
    }

    /**
     * Convert API response into AccessToken
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return self
     */
    public static function fromResponse($response)
    {
        return static::fromResponseArray(
            Client::responseToArray($response)
        );
    }

    /**
     * Instantiate access token object
     *
     * @param $responseArray
     *
     * @return \LinkedIn\AccessToken
     */
    public static function fromResponseArray($responseArray)
    {
        if (!is_array($responseArray)) {
            throw new \InvalidArgumentException(
                'Argument is not array'
            );
        }
        if (!isset($responseArray['access_token'])) {
            throw new \InvalidArgumentException(
                'Access token is not available'
            );
        }
        if (!isset($responseArray['expires_in'])) {
            throw new \InvalidArgumentException(
                'Access token expiration date is not specified'
            );
        }

        if (!isset($responseArray['refresh_token'])) {
            throw new \InvalidArgumentException(
                'Refresh token is not available'
            );
        }
        if (!isset($responseArray['refresh_token_expires_in'])) {
            throw new \InvalidArgumentException(
                'Refresh token expiration date is not specified'
            );
        }

        return new static(
            $responseArray['access_token'],
            $responseArray['expires_in'] + time(),
            $responseArray['refresh_token'],
            $responseArray['refresh_token_expires_in'] + time()
        );
    }

    /**
     * Specify data format for json_encode()
     */
    public function jsonSerialize()
    {
        return [
            'token' => $this->getToken(),
            'expiresAt' => $this->getExpiresAt(),
            'refreshToken' => $this->getRefreshToken(),
            'refreshTokenExpiresAt' => $this->getRefreshTokenExpiresAt(),
        ];
    }
}