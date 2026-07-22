<?php
header('Content-Type: text/html; charset=utf-8');
echo "<h1>Kofei Deployment Diagnostics</h1>";

// 1. Check if .env exists and print its values
echo "<h3>1. Checking .env File</h3>";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "<p style='color:green;'>✓ .env file exists.</p>";
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    echo "<ul>";
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            // Mask password
            if (stripos($key, 'pass') !== false) {
                $val = "********";
            }
            echo "<li><strong>$key:</strong> '$val'</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color:red;'>✗ .env file NOT found at: $envPath</p>";
}

// 2. Check Database Connection
echo "<h3>2. Checking Database Connection</h3>";
include_once(__DIR__ . '/site/backend/dbcon.php');
if (isset($conn) && $conn) {
    echo "<p style='color:green;'>✓ Database connection successful!</p>";
    mysqli_close($conn);
} else {
    echo "<p style='color:red;'>✗ Database connection failed. Details: " . mysqli_connect_error() . "</p>";
}

// 3. Check Files and Permissions
echo "<h3>3. Checking File Presence & Permissions</h3>";
$filesToCheck = [
    'site/style.css',
    'site/styles/sign-in-style.css',
    'site/styles/sign-up-style.css',
    'site/script.js',
    'site/renderFiles.js',
    'site/functions/signUp.js',
    'src/logo-kofai.png',
    'src/close-white.png'
];

echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
echo "<tr><th>File Path</th><th>Status</th><th>File Size</th><th>Permissions (CHMOD)</th></tr>";

foreach ($filesToCheck as $file) {
    $fullPath = __DIR__ . '/' . $file;
    echo "<tr>";
    echo "<td>$file</td>";
    if (file_exists($fullPath)) {
        $perms = decoct(fileperms($fullPath) & 0777);
        $size = filesize($fullPath);
        echo "<td style='color:green;'>✓ Exists</td>";
        echo "<td>$size bytes</td>";
        echo "<td>$perms</td>";
    } else {
        echo "<td style='color:red;'>✗ Missing / Not Found</td>";
        echo "<td>-</td>";
        echo "<td>-</td>";
    }
    echo "</tr>";
}
echo "</table>";

?>
