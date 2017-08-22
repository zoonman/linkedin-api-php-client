<?php
/**
 * linkedin-client
 * Exception.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Philipp Tkachev <philipp@zoonman.com>
 * @date     8/17/17 23:11
 * @license  http://www.zoonman.com/projects/linkedin-client/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://www.zoonman.com/projects/linkedin-client/
 */

namespace LinkedIn;

class Exception extends \Exception
{
    protected $description;
    public function __construct(
        $message = "",
        $code = 0,
        $previousException = null,
        $description
    ) {
        parent::__construct($message, $code, $previousException);
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }
}
