<?php
/**
 * linkedin-client
 * Method.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Philipp Tkachev <zoonman@gmail.com>
 * @date     8/22/17 09:15
 * @license  http://linkedin-client.com/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://linkedin-client.com/
 */

namespace LinkedIn\Http;

use LinkedIn\AbstractEnum;

class Method extends AbstractEnum
{

    /**
     *
     */
    const CONNECT = 'CONNECT';

    /**
     * The GET method requests a representation of the specified resource.
     * Requests using GET should only retrieve data.
     */
    const GET = 'GET';

    /**
     *
     */
    const HEAD = 'HEAD';

    /**
     *
     */
    const POST = 'POST';

    /**
     *
     */
    const PUT = 'PUT';

    /**
     *
     */
    const PATCH = 'PATCH';

    /**
     *
     */
    const OPTIONS = 'OPTIONS';

    /**
     *
     */
    const DELETE = 'DELETE';

    /**
     *
     */
    const TRACE = 'TRACE';
}
