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
     * Request a minimum information about the user
     * Use this scope when implementing "Sign In with LI"
     */
    const READ_LITE_PROFILE = 'r_liteprofile';
    
    const READ_FULL_PROFILE = 'r_fullprofile';

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
    
    /**
     * Post, comment and like posts on behalf of an organization.
     */
    const SHARE_AS_ORGANIZATION = 'w_organization_social';
    
    /**
     * Retrieve organizations' posts, comments, and likes. 
     */
    const READ_ORGANIZATION_SHARES = 'r_organization_social';
    
    /**
     * Post, comment and like posts on behalf of an authenticated member.
     */
    const SHARE_AS_USER = 'w_member_social';
    
    /**
     * Restricted API! 
     */
    const READ_USER_CONTENT = 'r_member_social';
    
    /**
     * Read and write access to ads.
     */
    const ADS_MANAGEMENT = 'rw_ads';
}
