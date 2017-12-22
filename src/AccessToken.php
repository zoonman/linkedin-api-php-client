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
     * AccessToken constructor.
     *
     * @param string $token
     * @param int    $expiresAt
     */
    public function __construct($token = '', $expiresAt = 0)
    {
        $this->setToken($token);
        $this->setExpiresAt($expiresAt);
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
     * The number of seconds remaining, from the time it was requested, before the token will expire.
     *
     * @return int seconds
     */
    public function getExpiresIn()
    {
        return $this->expiresAt - time();
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
        return new static(
            $responseArray['access_token'],
            $responseArray['expires_in'] + time()
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
        ];
    }
}
