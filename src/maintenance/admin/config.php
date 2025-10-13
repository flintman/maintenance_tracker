<?php
require_once '../common/common.php';

// Fetch all config as key=>value
$stmt = $pdo->query("SELECT config_name, config_value FROM admin_config");
$config = [];
foreach ($stmt as $row) {
    $config[$row['config_name']] = $row['config_value'];
}

// Set defaults if not present
if (!isset($config['default_theme'])) $config['default_theme'] = 'theme_1';
if (!isset($config['columns_to_show'])) $config['columns_to_show'] = 3;
if (!isset($config['primary_label'])) $config['primary_label'] = 'Primary';
if (!isset($config['secondary_label'])) $config['secondary_label'] = 'Secondary';

// Handle form submission

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $default_theme = $_POST['default_theme'] ?? 'theme_1';
    $columns_to_show = max(1, (int)($_POST['columns_to_show'] ?? 3));
    $primary_unit = trim($_POST['primary_unit'] ?? 'Primary');
    $secondary_unit = trim($_POST['secondary_unit'] ?? 'Secondary');

    // Always update (assumes row exists and config_name is unique)
    $stmt = $pdo->prepare("UPDATE admin_config SET config_value=? WHERE config_name='default_theme'");
    $stmt->execute([$default_theme]);

    $stmt = $pdo->prepare("UPDATE admin_config SET config_value=? WHERE config_name='columns_to_show'");
    $stmt->execute([$columns_to_show]);

    $stmt = $pdo->prepare("UPDATE admin_config SET config_value=? WHERE config_name='primary_unit'");
    $stmt->execute([$primary_unit]);

    $stmt = $pdo->prepare("UPDATE admin_config SET config_value=? WHERE config_name='secondary_unit'");
    $stmt->execute([$secondary_unit]);

    $config['default_theme'] = $default_theme;
    $config['columns_to_show'] = $columns_to_show;
    $config['primary_unit'] = $primary_unit;
    $config['secondary_unit'] = $secondary_unit;
    $msg = "Configuration updated!";
}

// Get available themes
$themes = [];
$themes_dir = realpath(__DIR__ . '/../templates');
foreach (scandir($themes_dir) as $theme) {
    if ($theme[0] !== '.' && is_dir($themes_dir . '/' . $theme)) {
        $themes[] = $theme;
    }
}

$smarty->assign('themes', $themes);
$smarty->assign('config', $config);
$smarty->assign('msg', $msg ?? null);
$smarty->display($config['default_theme'] . '/admin/header.tpl');
$smarty->display($config['default_theme'] . '/admin/config.tpl');
$smarty->display($config['default_theme'] . '/admin/footer.tpl');