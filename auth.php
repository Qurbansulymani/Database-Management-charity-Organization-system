<?php
// auth.php - Include this file at the top of every protected page
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Store the current URL for redirecting after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Optional: Check user role for authorization
function checkRole($allowed_roles) {
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
        header('Location: unauthorized.php');
        exit;
    }
}

// Get current user info
function getCurrentUser() {
    return [
        'username' => $_SESSION['username'] ?? 'Unknown',
        'role' => $_SESSION['user_role'] ?? 'user'
    ];
}
?>