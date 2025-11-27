<?php
/**
 * Main Configuration File
 * Resume Builder CMS
 */

// Error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Security settings - must be set before session starts
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS

// Include required files
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Application settings
define('APP_NAME', 'Resume Builder CMS');
define('APP_VERSION', '1.0.0');

// Dynamically detect BASE_URL (without port, with project subdirectory if applicable)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
// Remove port from host if present
$host = preg_replace('/:\d+$/', '', $host);
// Get the directory path (project name)
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = dirname($scriptName);
// Remove leading slash and ensure it ends with / if not empty
$basePath = $basePath === '/' ? '' : rtrim($basePath, '/');
// Construct BASE_URL
define('BASE_URL', $protocol . '://' . $host . $basePath);

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
?>

