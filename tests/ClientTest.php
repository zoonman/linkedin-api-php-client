<?php
/**
 * linkedin-client
 * ClientTest.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Philipp Tkachev <philipp@zoonman.com>
 * @date     8/17/17 19:57
 * @license  http://www.zoonman.com/projects/linkedin-client/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://www.zoonman.com/projects/linkedin-client/
 */

namespace LinkedIn;

/**
 * Class ClientTest
 *
 * @package LinkedIn
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \LinkedIn\Client
     */
    public $client;

    /**
     * Setup test environment
     */
    public function setUp()
    {
        $this->client = new Client(
            getenv('LINKEDIN_CLIENT_ID'),
            getenv('LINKEDIN_CLIENT_SECRET')
        );
    }

    /**
     * Make sure, that user redirect gets prepared correctly
     */
    public function testGetLoginUrl()
    {
        $actual = $this->client->getLoginUrl();
        $this->assertNotEmpty($actual);
    }
}
