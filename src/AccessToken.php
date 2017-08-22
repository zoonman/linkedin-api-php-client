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
class AccessToken
{

    /**
     * @var string
     */
    protected $token;

    /**
     * @var int
     */
    protected $expiresIn;

    /**
     * AccessToken constructor.
     *
     * @param string $token
     * @param int    $expiresIn
     */
    public function __construct($token = '', $expiresIn = 0)
    {
        $this->setToken($token);
        $this->setExpiresIn($expiresIn);
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
     * Get token expiration
     *
     * @return int seconds
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * Set token expiration time
     *
     * @param int $expiresIn seconds
     *
     * @return AccessToken
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
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
            $responseArray['expires_in']
        );
    }
}
