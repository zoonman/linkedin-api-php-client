<?php
/**
 * linkedin-client
 * Scope.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Philipp Tkachev <zoonman@gmail.com>
 * @date     8/22/17 09:02
 * @license  http://linkedin-client.com/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://linkedin-client.com/
 */

namespace LinkedIn;

class Scope extends AbstractEnum
{
    const READ_BASIC_PROFILE = 'r_basicprofile';
    const READ_EMAIL_ADDRESS = 'r_emailaddress';
    const MANAGE_COMPANY = 'rw_company_admin';
    const SHARING = 'w_share';
}
