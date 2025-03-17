<?php
require_once __DIR__ . 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV('DB_HOST');
$dbname = $_ENV('DB_NAME');
$user = $_ENV('DB_USER');
$password = $_ENV('DB_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;user=$user;password=$password");
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>