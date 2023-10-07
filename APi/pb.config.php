<?php
// Your anger may wash over me, but it won't breach the fortress of my inner peace
session_start();
date_default_timezone_set('America/Sao_Paulo');
set_time_limit(0);

try {
    define('DB_TYPE', 'pgsql');
    define('DB_HOST', '25.27.185.191');
    define('DB_PORT', 5432);
    define('DB_USER', 'postgres');
    define('DB_PASS', '12345');
    define('DB_NAME', '3.24');

    $pdo = new PDO('' . DB_TYPE . ':dbname=' . DB_NAME . ';host=' . DB_HOST . ';port=' . DB_PORT . '', DB_USER, DB_PASS);

    // Set PDO to throw exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle the exception gracefully, e.g., log the error
    // You can replace this with your preferred error-handling strategy
    error_log('Database connection error: ' . $e->getMessage());
    // Optionally, you can also display an error message to the user
    echo 'An error occurred. Please try again later.';
    // Terminate script execution
    exit;
}
function _uname($n)
{

    if (is_null($n)) {
        $n = '';
    }
    $n = preg_replace('/[^[:alnum:] -]/', '', $n);
    $n = trim($n);
    $n = htmlspecialchars($n, ENT_QUOTES, 'UTF-8');
    return ($n);
}
function encripitar($senha)
{
    $salt = '/x!a@r-$r%an¨.&e&+f*f(f(a)';
    $output = hash_hmac('md5', $senha, $salt);
    return $output;
}
?>
