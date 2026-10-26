<?php
/**
 * User Logout Script
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Unset all session variables
$_SESSION = [];

// Destroy session cookie if set
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Start a fresh session to send a goodbye flash message
session_start();
setFlashMessage('info', 'You have been successfully logged out.');

header('Location: /sinesine/index.php');
exit;

