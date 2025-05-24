<?php
// Prevent multiple inclusion
if (defined('DB_CONFIG_INCLUDED')) {
    return;
}

// Start session for CSRF protection if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database configuration - using const for better performance
const DB_HOST = 'localhost';
const DB_USER = '3206_CENTRALIZED_DATABASE';
const DB_PASS = '4562526';
const DB_NAME = 'logs2_audit_management';
const DB_NAME_FINANCIALS = 'logs2_audit_financials';

// Mark as included
define('DB_CONFIG_INCLUDED', true);

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include database helpers first
require_once __DIR__ . '/database_helpers.php';

// Error handling
try {
    // Connect to main audit management database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Main database connection failed: " . $conn->connect_error);
    }
    
    // Connect to financial audit database
    $connFinancials = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME_FINANCIALS);
    if ($connFinancials->connect_error) {
        throw new Exception("Financial database connection failed: " . $connFinancials->connect_error);
    }
    
    // Set charset for both connections
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error setting charset for main database: " . $conn->error);
    }
    if (!$connFinancials->set_charset("utf8mb4")) {
        throw new Exception("Error setting charset for financial database: " . $connFinancials->error);
    }

    // Set timezone
    date_default_timezone_set('Asia/Manila');
    
} catch (Exception $e) {
    // Log the error (in production, use proper logging)
    error_log("Database connection error: " . $e->getMessage());
    
    // If it's an AJAX request, return JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        jsonResponse([
            'success' => false,
            'message' => 'Database connection error. Please try again later.'
        ], 500);
    }
    
    // For regular requests, show a user-friendly error
    die("We're experiencing technical difficulties. Please try again later.");
}

// Add CSRF token to all forms automatically
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('X-CSRF-Token: ' . $_SESSION['csrf_token']);
}
?>
