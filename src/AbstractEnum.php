<?php
/**
 * linkedin-client
 * AbstractEnum.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Philipp Tkachev <zoonman@gmail.com>
 * @date     8/22/17 09:10
 * @license  http://www.zoonman.com/projects/linkedin-client/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://www.zoonman.com/projects/linkedin-client/
 */

namespace LinkedIn;

/**
 * Class AbstractEnum
 *
 * @package LinkedIn
 */
abstract class AbstractEnum
{

    /**
     * @return array
     */
    public static function getMap()
    {
        $spl = new \ReflectionClass(get_called_class());
        return $spl->getConstants();
    }

    /**
     * @return array
     */
    public static function getValues()
    {
        return array_values(static::getMap());
    }
}
