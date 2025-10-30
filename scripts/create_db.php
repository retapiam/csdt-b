<?php
// Crea la base de datos definida en .env si no existe (MySQL/XAMPP)

$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$db   = getenv('DB_DATABASE') ?: 'csdt_database';
$port = (int) (getenv('DB_PORT') ?: 3306);

$mysqli = @new mysqli($host, $user, $pass, '', $port);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "MySQL connect error: " . $mysqli->connect_error . PHP_EOL);
    exit(1);
}

$dbEscaped = $mysqli->real_escape_string($db);
$sql = "CREATE DATABASE IF NOT EXISTS `{$dbEscaped}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!$mysqli->query($sql)) {
    fwrite(STDERR, "Create DB error: " . $mysqli->error . PHP_EOL);
    exit(2);
}

echo "DB_OK\n";

