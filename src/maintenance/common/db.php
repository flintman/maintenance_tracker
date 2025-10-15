<?php
if (!defined('MAINTENANCE_TRACKER_INIT')) {
    http_response_code(403);
    exit('Direct access not permitted.');
}

$host = 'db';
$db   = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER');
$pass = getenv('MYSQL_PASSWORD');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; font-size: 1.2em; margin: 2em 0;'>
        PLEASE WAIT FOR DATABASE TO LOAD.<br>
        If this continues for more than 2 minutes and first load of the database, please check for errors.
    </div>";
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}