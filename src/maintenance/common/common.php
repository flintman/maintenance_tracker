<?php
require 'include/smarty-5.6.0/libs/Smarty.class.php';

// Initialize Smarty
use Smarty\Smarty;
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__ . '/../templates/');
$smarty->setCompileDir(__DIR__ . '/../templates_c/');

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
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

if (!isset($_SESSION)) session_start();

// Fetch all admin_config key-value pairs into $admin_config associative array
$stmt = $pdo->query("SELECT config_name, config_value FROM admin_config");
$admin_config = [];
while ($row = $stmt->fetch()) {
    $admin_config[$row['config_name']] = $row['config_value'];
}
$theme_current = $admin_config['default_theme'] ?? 'theme_1'; // Default theme
$number_columns = $admin_config['columns_to_show'] ?? 3;
$primary_label = $admin_config['primary_unit'] ?? 'Primary';
$secondary_label = $admin_config['secondary_unit'] ?? 'Secondary';

// Helper function to sanitize and validate inputs
function cleanInput($data, $type = 'string') {
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'string':
        default:
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

// If user is signed in and has a theme preference in DB, use it
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT theme FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user && !empty($user['theme'])) {
        $theme_current = $user['theme'];
    }
}

// Assign common variables to Smarty
$smarty->assign('theme', $_COOKIE['theme'] ?? 'light');
$smarty->assign('session', $_SESSION ?? []);
$smarty->assign('primary_label', $primary_label);
$smarty->assign('secondary_label', $secondary_label);