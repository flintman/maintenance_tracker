<?php
require_once 'common/common.php';
$smarty->display($theme_current . '/header.tpl');
if (!isset($_SESSION['user_id'])) {
    exit;
}

// Determine context: refrigeration or trailer
$refrigeration_id = cleanInput($_GET['refrigeration_id'] ?? null, 'int');
$trl_id = cleanInput($_GET['trl_id'] ?? null, 'int');

// Ensure refrigeration_id and trl_id are properly set to NULL if empty
$refrigeration_id = $refrigeration_id ?: null;
$trl_id = $trl_id ?: null;

if (!$refrigeration_id && !$trl_id) {
    echo "No equipment selected.";
    exit;
}

// Fetch equipment name
if ($refrigeration_id) {
    // Fetch answer to question 1 for this refrigeration unit
    $stmt_q1 = $pdo->prepare('SELECT value FROM refrigeration_answers WHERE refrigeration_id = ? AND question_id = 1 LIMIT 1');
    $stmt_q1->execute([$refrigeration_id]);
    $equipment_name = $stmt_q1->fetchColumn();
} else {
    $stmt = $pdo->prepare('SELECT trl_id FROM trailers WHERE id = ?');
    $stmt->execute([$trl_id]);
    $equipment_name = $stmt->fetchColumn();
}
$smarty->assign('equipment_name', $equipment_name);

// Add maintenance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_maintenance'])) {
    $refrigeration_id = cleanInput($_POST['refrigeration_id'] ?? null, 'int');
    $trl_id = cleanInput($_POST['trl_id'] ?? null, 'int');
    $type_of_service = cleanInput($_POST['type_of_service']);
    $description = cleanInput($_POST['description']);
    $costs_of_parts = cleanInput($_POST['costs_of_parts'], 'int');
    $performed_at = cleanInput($_POST['performed_at']);
    $performed_by = cleanInput($_POST['performed_by']);

    // Ensure refrigeration_id and trl_id are properly set to NULL if empty
    $refrigeration_id = $refrigeration_id ?: null;
    $trl_id = $trl_id ?: null;
    $smarty->assign('trl_id', $trl_id);

    // Insert maintenance record
    $stmt = $pdo->prepare('INSERT INTO maintenance (refrigeration_id, trl_id, type_of_service, description, costs_of_parts, performed_at, performed_by) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$refrigeration_id, $trl_id, $type_of_service, $description, $costs_of_parts, $performed_at, $performed_by]);
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
if ($refrigeration_id) {
    $stmt = $pdo->prepare('SELECT * FROM maintenance WHERE refrigeration_id = ? ORDER BY performed_at DESC');
    $stmt->execute([$refrigeration_id]);
} else {
    $stmt = $pdo->prepare('SELECT * FROM maintenance WHERE trl_id = ? ORDER BY performed_at DESC');
    $stmt->execute([$trl_id]);
}
$records = $stmt->fetchAll();

$smarty->assign('refrigeration_id', $refrigeration_id);
$smarty->assign('records', $records);
// Ensure $maintenance_id is defined
$maintenance_id = cleanInput($_POST['maintenance_id'] ?? null, 'int');

$smarty->display($theme_current . '/maintenance.tpl');
$smarty->display($theme_current . '/footer.tpl');
?>


