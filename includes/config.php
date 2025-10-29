<?php
// Start session
session_start();

// Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Your XAMPP default username
define('DB_PASS', '');     // Your XAMPP default password
define('DB_NAME', 'smartuniverse');

// Base URL
define('BASE_URL', 'http://localhost/smart-uni-verse/');

// API Keys
define('OPENAI_API_KEY', 'YOUR_OPENAI_API_KEY_HERE');

// --- Database Connection (PDO) ---
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// --- Helper Functions ---

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check for admin role
function isAdmin() {
    return (isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

// (Add more helper functions here as needed)


// --- NEW ---
// Include the new gamification helper
require_once('gamification_helper.php');
?>