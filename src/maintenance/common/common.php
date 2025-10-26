<?php
require_once __DIR__ . '/include/smarty-5.6.0/libs/Smarty.class.php';
define('MAINTENANCE_TRACKER_INIT', true);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Initialize Smarty
use Smarty\Smarty;
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__ . '/../templates/');
$smarty->setCompileDir(__DIR__ . '/../templates_c/');

// Fetch all admin_config key-value pairs into $admin_config associative array
$stmt = $pdo->query("SELECT config_name, config_value FROM admin_config");
$admin_config = [];
while ($row = $stmt->fetch()) {
    $admin_config[$row['config_name']] = $row['config_value'];
}
$theme_current = $admin_config['default_theme'] ?? 'theme_1';
$number_columns = $admin_config['columns_to_show'] ?? 3;
$primary_label = $admin_config['primary_unit'] ?? 'Primary';
$secondary_label = $admin_config['secondary_unit'] ?? 'Secondary';
$message_board = $admin_config['message_board'] ?? '0';

if (!isset($_SESSION)) session_start();
// If user is signed in and has a theme preference in DB, use it
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT theme, nickname FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user && !empty($user['theme'])) {
        $theme_current = $user['theme'];
    }
    if ($user && !empty($user['nickname'])) {
        $_SESSION['nickname'] = $user['nickname'];
    }
}

// Check for version mismatch which sees if it needs to upgrade
$database_version = $admin_config['version'] ?? '0.0.0';
$version = getenv('MAINTENANCE_TRACKER_VERSION') ?: '0.0.0';

if (($version !== $database_version) && (isset($_SESSION['user_id']) && ($_SESSION['privilege'] ?? '') === 'admin')) {
    // Redirect to update script if versions do not match
    header('Location: ../update/index.php?from_version=' . urlencode($database_version));
    exit;
}

$api_keys = [];
$stmt = $pdo->query("SELECT api_key FROM users WHERE api_key IS NOT NULL AND api_key != ''");
while ($row = $stmt->fetch()) {
    $api_keys[] = $row['api_key'];
}

require_once __DIR__ . '/language/english.php';

// Assign common variables to Smarty
$smarty->assign('theme', $_COOKIE['theme'] ?? 'light');
$smarty->assign('session', $_SESSION ?? []);
$smarty->assign('primary_label', $primary_label);
$smarty->assign('secondary_label', $secondary_label);
$smarty->assign('message_board', $message_board);
$smarty->assign('software_version', getenv('MAINTENANCE_TRACKER_VERSION') ?: '0.0.0');