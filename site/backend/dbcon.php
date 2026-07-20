<?php

$host = getenv('DB_HOST') ?: "127.0.0.1";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$db = getenv('DB_NAME') ?: "kofei_db";
$port = getenv('DB_PORT') ?: 3306;

$conn = mysqli_connect($host, $user, $pass, $db, (int) $port);

if (!$conn) {
    echo "Connection failed.";
}

?>