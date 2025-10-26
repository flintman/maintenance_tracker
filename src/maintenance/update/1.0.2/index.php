<?php
define('MAINTENANCE_TRACKER_INIT', true);
require_once __DIR__ . '/../../common/db.php';
if (!file_exists(__DIR__ . '/../../common/db.php')) {
    die("Database configuration file not found: " . __DIR__ . '/../../common/db.php');
}

$updatedVersion = '1.0.2';

// 1. Alter equipment table: rename pmy_id to unit_id
try {
    $pdo->exec("ALTER TABLE equipment CHANGE pmy_id unit_id INT");
    echo "equipment table updated: pmy_id renamed to unit_id.<br>\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') === false && strpos($e->getMessage(), 'check that column/key exists') === false) {
        die("Error altering equipment table: " . $e->getMessage());
    }
}

// 2. Move files in assets/uploads/ to assets/uploads/{unit_id}/
$uploads_dir = __DIR__ . '/../../assets/uploads/';
if (is_dir($uploads_dir)) {
    $files = scandir($uploads_dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || is_dir($uploads_dir . $file)) continue;
        // Find all maintenance records where this file is in the photos JSON
        $stmt = $pdo->prepare("SELECT id, pmy_id, secondary_id FROM maintenance WHERE JSON_CONTAINS(photos, '" . json_encode($file) . "')");
        $stmt->execute();
        $found = false;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $unit_id = null;
            if (!empty($row['pmy_id'])) {
                // Use pmy_id to look up equipment.unit_id (primary)
                $eq = $pdo->prepare("SELECT unit_id FROM equipment WHERE id = ? AND equipment_level = 1");
                $eq->execute([$row['pmy_id']]);
                $unit_id = $eq->fetchColumn();
            } elseif (!empty($row['secondary_id'])) {
                // Use secondary_id to look up equipment.unit_id (secondary)
                $eq = $pdo->prepare("SELECT unit_id FROM equipment WHERE id = ? AND equipment_level = 2");
                $eq->execute([$row['secondary_id']]);
                $unit_id = $eq->fetchColumn();
            }
            if ($unit_id) {
                $target_dir = $uploads_dir . $unit_id . '/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                rename($uploads_dir . $file, $target_dir . $file);
                echo "Moved $file to $unit_id/<br>\n";
                $found = true;
                break; // Only move once per file
            }
        }
        if (!$found) {
            echo "Could not determine unit_id for $file, leaving in place.<br>\n";
        }
    }
} else {
    echo "Uploads directory not found.<br>\n";
}

// 3. Update the version in admin_config table
$stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_config WHERE config_name = 'version'");
$stmt->execute();
$exists = $stmt->fetchColumn();
if ($exists) {
    $stmt = $pdo->prepare("UPDATE admin_config SET config_value = ? WHERE config_name = 'version'");
    $stmt->execute([$updatedVersion]);
} else {
    $stmt = $pdo->prepare("INSERT INTO admin_config (config_name, config_value) VALUES ('version', ?)");
    $stmt->execute([$updatedVersion]);
}
echo "Migration to 1.0.2 complete.<br>\n";
echo "<a href='../../index.php'>Return to the application</a>";

?>
