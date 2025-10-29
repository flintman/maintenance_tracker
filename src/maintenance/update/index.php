<?php
// run_migrations.php
// Run all SQL migrations in update/ folder, track in DB, and delete folder if all succeed
define('MAINTENANCE_TRACKER_INIT', true);
require_once __DIR__ . '/../common/db.php';

$updateDir = __DIR__ . '/sql';

// Get current database version
$stmt = $pdo->query("SELECT config_value FROM admin_config WHERE config_name = 'version' LIMIT 1");
$database_version = $stmt->fetchColumn();
if (!$database_version) {
    $database_version = '0.0.0';
}

// Get all .sql files in update/sql, sorted by version (filename should be version.sql)
if (!is_dir($updateDir)) {
    echo "No update/sql folder found.<br>";
    exit(0);
}
$files = glob($updateDir . '/*.sql');
if (!$files) {
    echo "No SQL files to apply.<br>";
    exit(0);
}
$files = array_map('basename', $files);
usort($files, function($a, $b) {
    return version_compare(str_replace('.sql','',$a), str_replace('.sql','',$b));
});

$ranAny = false;
foreach ($files as $file) {
    $ver = str_replace('.sql','',$file);
    if (version_compare($ver, $database_version, '>')) {
        $sql = file_get_contents($updateDir . '/' . $file);
        try {
            $pdo->exec($sql);
            echo "Applied migration: $file<br>\n";
            $ranAny = true;
        } catch (Exception $e) {
            echo "Failed to apply $file: " . $e->getMessage() . "<br>\n";
            echo "<b>Some migrations failed.</b>";
            exit(1);
        }
    }
}

if ($ranAny) {
    echo "<b>All migrations completed. Please delete the update folder for security.</b>";
} else {
    echo "No new migrations to apply.<br>";
}
