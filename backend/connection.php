<?php
require_once __DIR__ . '/vendor/autoload.php'; // Autoload Composer

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apikey = $_ENV['API_KEY'];

$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    error_log("Database error: " . $conn->connect_error);
    http_response_code(500);
    exit("Internal Server Error");
}
?>
