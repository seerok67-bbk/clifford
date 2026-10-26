<?php
/**
 * Authentication and Access Control Helpers
 * SineSine - Movie Rating and Review Website
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if a user is currently logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if the logged-in user is an admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Get current logged in user data array
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id'        => $_SESSION['user_id'],
        'username'  => $_SESSION['username'],
        'full_name' => $_SESSION['full_name'],
        'email'     => $_SESSION['email'],
        'role'      => $_SESSION['role'],
        'avatar'    => $_SESSION['avatar'] ?? 'default-avatar.png'
    ];
}

/**
 * Require normal user authentication (redirect to login if guest)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash_message'] = [
            'type' => 'warning',
            'message' => 'Please log in to continue.'
        ];
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'index.php';
        header('Location: /sinesine/login.php');
        exit;
    }
}

/**
 * Require administrator privilege (redirect if not admin)
 */
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Access denied. Administrator privileges required.'
        ];
        header('Location: /sinesine/index.php');
        exit;
    }
}

