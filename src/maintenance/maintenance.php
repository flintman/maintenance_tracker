<?php
require_once 'common/common.php';
$smarty->display($theme_current . '/header.tpl');
if (!isset($_SESSION['user_id'])) {
    exit;
}

// Determine context: secondary_units or primary
$secondary_id = cleanInput($_GET['secondary_id'] ?? null, 'int');
$pmy_id = cleanInput($_GET['pmy_id'] ?? null, 'int');

// Ensure secondary_id and pmy_id are properly set to NULL if empty
$secondary_id = $secondary_id ?: null;
$pmy_id = $pmy_id ?: null;

if (!$secondary_id && !$pmy_id) {
    echo "No equipment selected.";
    exit;
}

// Fetch equipment name
if ($secondary_id) {
    // Fetch answer to question 1 for this secondary_units unit
    $stmt_q1 = $pdo->prepare('SELECT value FROM secondary_answers WHERE secondary_id = ? AND question_id = 1 LIMIT 1');
    $stmt_q1->execute([$secondary_id]);
    $equipment_name = $stmt_q1->fetchColumn();
} else {
    $stmt = $pdo->prepare('SELECT pmy_id FROM primary_units WHERE id = ?');
    $stmt->execute([$pmy_id]);
    $equipment_name = $stmt->fetchColumn();
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
    if (isset($_FILES['photos'])) {
        foreach ($_FILES['photos']['name'] as $index => $name) {
            $filename = basename($name);
            $target = 'assets/uploads/' . $filename;
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

    $msg = 'Maintenance record added successfully!';
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


