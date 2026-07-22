<?php

// Check if .env file exists and parse it manually if getenv isn't populated (common on shared hosting)
$envPath = __DIR__ . '/../../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Parse key-value pair
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $name = trim($parts[0]);
            $value = trim($parts[1]);
            // Remove quotes if present
            $value = trim($value, "\"' ");
            if (!getenv($name)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

$host = getenv('DB_HOST') ?: "127.0.0.1";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$db = getenv('DB_NAME') ?: "kofei_db";
$port = getenv('DB_PORT') ?: 3306;

// Disable throwing exceptions for mysqli connection to avoid 500 Internal Server Error
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @mysqli_connect($host, $user, $pass, $db, (int) $port);

if (!$conn) {
    // Log or handle error without crashing with 500 error
    error_log("Database connection failed: " . mysqli_connect_error());
}

?>