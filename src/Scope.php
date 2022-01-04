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
 * @license  http://www.zoonman.com/projects/linkedin-client/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://www.zoonman.com/projects/linkedin-client/
 */

namespace LinkedIn;

/**
 * Class Scope defines list of available permissions
 *
 * @package LinkedIn
 */
class Scope extends AbstractEnum
{
    /**
     * Allows to read basic information about profile, such as name
     */
    const READ_BASIC_PROFILE = 'r_basicprofile';

    /**
     * Enables access to email address field
     */
    const READ_EMAIL_ADDRESS = 'r_emailaddress';

    /**
     * Enables  to manage business company, retrieve analytics
     */
    const MANAGE_COMPANY = 'rw_company_admin';

    /**
     * Enables ability to share content on LinkedIn
     */
    const SHARING = 'w_share';
    /**
     * Manage and delete your data including your profile, posts, invitations, and messages
     */
    const COMPLIANCE = 'w_compliance';
}
