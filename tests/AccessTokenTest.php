<?php
/**
 * linkedin-client
 * AccessTokenTest.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Aleksey Salnikov <me@iamsalnikov.ru>
 * @date     8/25/17 15:57
 * @license  http://www.zoonman.com/projects/linkedin-client/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://www.zoonman.com/projects/linkedin-client/
 */

namespace LinkedIn;

use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest
 *
 * @package LinkedIn
 */
class AccessTokenTest extends TestCase
{
    /**
     * @dataProvider getValidResponseTestTable()
     * @param AccessToken $expectedToken
     * @param array $response
     */
    public function testConstructorFromResponseArray($expectedToken, $response)
    {
        $token = AccessToken::fromResponseArray($response);
        $this->assertEquals($expectedToken->getToken(), $token->getToken());
    }

    public function getValidResponseTestTable()
    {
        return [
            [
                'expectedToken' => new AccessToken('test', 0),
                'response' => [
                    'access_token' => 'test',
                    'expires_in' => 0,
                ],
            ]
        ];
    }

    /**
     * @dataProvider getInvalidResponseTestTable()
     *
     * @param string $exceptionClass
     * @param string $exceptionMessage
     * @param mixed $response
     */
    public function testConstructorFromResponseArrayWithException($exceptionClass, $exceptionMessage, $response)
    {
        $this->setExpectedException($exceptionClass, $exceptionMessage);
        AccessToken::fromResponseArray($response);
    }

    public function getInvalidResponseTestTable()
    {
        return [
            [
                'expectedException' => \InvalidArgumentException::class,
                'exceptionMessage' => 'Argument is not array',
                'response' => null,
            ],
            [
                'expectedException' => \InvalidArgumentException::class,
                'exceptionMessage' => 'Access token is not available',
                'response' => [],
            ],
            [
                'expectedException' => \InvalidArgumentException::class,
                'exceptionMessage' => 'Access token is not available',
                'response' => [
                    'access_token' => null,
                ],
            ],
            [
                'expectedException' => \InvalidArgumentException::class,
                'exceptionMessage' => 'Access token is not available',
                'response' => [
                    'expires_in' => 1,
                ],
            ],
            [
                'expectedException' => \InvalidArgumentException::class,
                'exceptionMessage' => 'Access token expiration date is not specified',
                'response' => [
                    'access_token' => 'hello',
                ],
            ],
            [
                'expectedException' => \InvalidArgumentException::class,
                'exceptionMessage' => 'Access token expiration date is not specified',
                'response' => [
                    'access_token' => 'hello',
                    'expires_in' => null,
                ],
            ],
        ];
    }

    public function testToString()
    {
        $token = new AccessToken('hello', 1);
        $this->assertEquals('hello', (string) $token);
    }

    public function testJsonSerialize()
    {
        $token = new AccessToken('hello', 1);
        $this->assertEquals('{"token":"hello","expiresAt":1}', json_encode($token));
    }
}
