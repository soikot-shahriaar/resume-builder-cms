<?php
/**
 * User Logout
 * Resume Builder CMS
 */

require_once 'config/config.php';

// Logout user
logoutUser();

// Redirect to login page with message
redirectWithMessage('login.php', 'You have been logged out successfully.', 'success');
?>

