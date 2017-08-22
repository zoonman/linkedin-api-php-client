<?php
/**
 * linkedin-client
 * bootstrap.php
 *
 * PHP Version 5
 *
 * @category Production
 * @package  Default
 * @author   Philipp Tkachev <philipp@zoonman.com>
 * @date     8/17/17 22:12
 * @license  http://www.zoonman.com/projects/linkedin-client/license.txt linkedin-client License
 * @version  GIT: 1.0
 * @link     http://www.zoonman.com/projects/linkedin-client/
 */

$pathToDotEnvFile = dirname(__DIR__);
if (file_exists($pathToDotEnvFile . '/.env')) {
    $dotenv = new Dotenv\Dotenv($pathToDotEnvFile);
    $dotenv->load();
} elseif (empty(getenv('LINKEDIN_CLIENT_ID')) || empty(getenv('LINKEDIN_CLIENT_SECRET'))) {
    echo "Create .env file with credentials or setup environment variables LINKEDIN_CLIENT_ID & LINKEDIN_CLIENT_SECRET to make tests pass.";
}
