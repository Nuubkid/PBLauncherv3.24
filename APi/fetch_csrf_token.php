<?php
// Start the session
session_start();

// Generate and store a CSRF token in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a random token
}

// Return the CSRF token as plain text
echo $_SESSION['csrf_token'];
