<?php
require_once 'common/common.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$smarty->display($theme_current . '/header.tpl');
// Determine context: secondary_units or primary
$secondary_id = cleanInput($_GET['secondary_id'] ?? null, 'int');
$pmy_id = cleanInput($_GET['pmy_id'] ?? null, 'int');

// Ensure secondary_id and pmy_id are properly set to NULL if empty
$secondary_id = $secondary_id ?: null;
$pmy_id = $pmy_id ?: null;

if (!$secondary_id && !$pmy_id) {
    echo $smarty->getTemplateVars('NO_EQUIPMENT_SELECTED');
    exit;
}

// Fetch equipment name
if ($secondary_id) {
    // Fetch answer to question 1 for this secondary_units unit
    $stmt = $pdo->prepare('SELECT unit_id FROM equipment WHERE id = ? and equipment_level = 2');
    $stmt->execute([$secondary_id]);
    $unit_id = $stmt->fetchColumn();
    $equipment_name = $secondary_label . ' on ' . $primary_label . ' ' . $unit_id;
} else {
    $stmt = $pdo->prepare('SELECT unit_id FROM equipment WHERE id = ? and equipment_level = 1');
    $stmt->execute([$pmy_id]);
    $unit_id = $stmt->fetchColumn();
    $equipment_name = $primary_label . ' ' . $unit_id;
}
$smarty->assign('equipment_name', $equipment_name);

// Add maintenance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_maintenance'])) {
    $secondary_id = cleanInput($_POST['secondary_id'] ?? null, 'int');
    $pmy_id = cleanInput($_POST['pmy_id'] ?? null, 'int');
    $type_of_service = cleanInput($_POST['type_of_service']);
    $description = cleanInput($_POST['description']);
    $costs_of_parts = cleanInput($_POST['costs_of_parts'], 'float');
    $performed_at = cleanInput($_POST['performed_at']);
    $performed_by = cleanInput($_POST['performed_by']);

    // Ensure secondary_id and pmy_id are properly set to NULL if empty
    $secondary_id = $secondary_id ?: null;
    $pmy_id = $pmy_id ?: null;

    // Insert maintenance record
    $stmt = $pdo->prepare('INSERT INTO maintenance (secondary_id, pmy_id, type_of_service, description, costs_of_parts, performed_at, performed_by) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$secondary_id, $pmy_id, $type_of_service, $description, $costs_of_parts, $performed_at, $performed_by]);
    $maintenance_id = $pdo->lastInsertId();

    // Handle photo uploads
    $uploaded_photos = [];
    if (isset($_FILES['photos']) && $unit_id) {
        $upload_dir = 'assets/uploads/' . $unit_id . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        foreach ($_FILES['photos']['name'] as $index => $name) {
            $filename = basename($name);
            $target = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['photos']['tmp_name'][$index], $target)) {
                $uploaded_photos[] = $filename;
            }
        }
    }

    // Update maintenance record with photos
    if (!empty($uploaded_photos)) {
        $stmt = $pdo->prepare('UPDATE maintenance SET photos = ? WHERE id = ?');
        $stmt->execute([json_encode($uploaded_photos), $maintenance_id]);
    }

    $msg = $smarty->getTemplateVars('MAINTENANCE_RECORD_ADDED');
    $smarty->assign('msg', $msg ?? '');
}

// Fetch maintenance records
if ($secondary_id) {
    $stmt = $pdo->prepare('SELECT * FROM maintenance WHERE secondary_id = ? ORDER BY performed_at DESC');
    $stmt->execute([$secondary_id]);
} else {
    $stmt = $pdo->prepare('SELECT * FROM maintenance WHERE pmy_id = ? ORDER BY performed_at DESC');
    $stmt->execute([$pmy_id]);
}
$records = $stmt->fetchAll();

$smarty->assign('pmy_id', $pmy_id);
$smarty->assign('secondary_id', $secondary_id);
$smarty->assign('records', $records);
// Ensure $maintenance_id is defined
$maintenance_id = cleanInput($_POST['maintenance_id'] ?? null, 'int');

$smarty->display($theme_current . '/maintenance.tpl');
$smarty->display($theme_current . '/footer.tpl');
?>


