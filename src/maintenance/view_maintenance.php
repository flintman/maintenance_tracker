<?php
require 'common/common.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$smarty->display($theme_current . '/header.tpl');

$maintenance_id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == '1';
$source = $_GET['source'] ?? null;

$alert = '';
if (!$maintenance_id) {
    $alert = '<div class="alert alert-warning">' . $smarty->getTemplateVars('NO_MAINTENANCE_SELECTED') . '</div>';
    $smarty->assign('alert', $alert);
    $smarty->display($theme_current . '/view_maintenance.tpl');
    $smarty->display($theme_current . '/footer.tpl');
    exit;
}
// Determine which ID is set (pmy_id or secondary_id) and get unit_id
$stmt = $pdo->prepare('SELECT pmy_id, secondary_id FROM maintenance WHERE id=?');
$stmt->execute([$maintenance_id]);
$maintenance_data = $stmt->fetch();

if ($maintenance_data['pmy_id']) {
    $id = $maintenance_data['pmy_id'];
} elseif ($maintenance_data['secondary_id']) {
    $id = $maintenance_data['secondary_id'];
} else {
    $id = null;
}

$stmt = $pdo->prepare('SELECT unit_id FROM equipment WHERE id=?');
$stmt->execute([$id]);
$unit_id = $stmt->fetchColumn();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $type_of_service = cleanInput($_POST['type_of_service']);
    $description = cleanInput($_POST['description']);
    $costs_of_parts = cleanInput($_POST['costs_of_parts'], 'int');
    $performed_at = cleanInput($_POST['performed_at']);
    $performed_by = cleanInput($_POST['performed_by']);
    $stmt = $pdo->prepare('UPDATE maintenance SET type_of_service=?, description=?, costs_of_parts=?, performed_at=?, performed_by=? WHERE id=?');
    $stmt->execute([$type_of_service, $description, $costs_of_parts, $performed_at, $performed_by, $maintenance_id]);
    $alert = '<div class="alert alert-success">' . $smarty->getTemplateVars('MAINTENANCE_UPDATED') . '</div>';
}

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $target_dir = "assets/uploads/" . $unit_id . "/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($_FILES['photo']['name']);
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        $stmt = $pdo->prepare('SELECT photos FROM maintenance WHERE id=?');
        $stmt->execute([$maintenance_id]);
        $maintenance = $stmt->fetch();
        $photos = $maintenance['photos'] ? json_decode($maintenance['photos'], true) : [];
        $photos[] = basename($_FILES['photo']['name']);
        $stmt = $pdo->prepare('UPDATE maintenance SET photos=? WHERE id=?');
        $stmt->execute([json_encode($photos), $maintenance_id]);
        $alert = '<div class="alert alert-success">' . $smarty->getTemplateVars('PHOTO_UPLOADED') . '</div>';
    } else {
        $alert = '<div class="alert alert-danger">' . $smarty->getTemplateVars('PHOTO_UPLOAD_FAILED') . '</div>';
    }
}

// Handle photo delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_photo'])) {
    $photo_to_delete = cleanInput($_POST['delete_photo']);
    $stmt = $pdo->prepare('SELECT pmy_id, photos FROM maintenance WHERE id=?');
    $stmt->execute([$maintenance_id]);
    $maintenance = $stmt->fetch();
    $photos = $maintenance['photos'] ? json_decode($maintenance['photos'], true) : [];
    $photos = array_filter($photos, function($p) use ($photo_to_delete) { return $p !== $photo_to_delete; });
    $stmt = $pdo->prepare('UPDATE maintenance SET photos=? WHERE id=?');
    $stmt->execute([json_encode(array_values($photos)), $maintenance_id]);
    @unlink("assets/uploads/" . $unit_id . "/" . $photo_to_delete);
    $alert = '<div class="alert alert-success">' . $smarty->getTemplateVars('PHOTO_DELETED') . '</div>';
}

$stmt = $pdo->prepare('SELECT * FROM maintenance WHERE id = ?');
$stmt->execute([$maintenance_id]);
$maintenance = $stmt->fetch();
    if (!$maintenance) {
    $alert = '<div class="alert alert-warning">' . $smarty->getTemplateVars('MAINTENANCE_NOT_FOUND') . '</div>';
    $smarty->assign('alert', $alert);
    $smarty->display($theme_current . '/view_maintenance.tpl');
    $smarty->display($theme_current . '/footer.tpl');
    exit;
}
$photos = $maintenance['photos'] ? json_decode($maintenance['photos'], true) : [];

if ($source === 'dashboard' ) {
    $backLink = "index.php";
    $backLabel = "Dashboard";
} else {
    if ($type === 'secondary_units') {
        $backLink = "maintenance.php?secondary_id=" . urlencode($maintenance['secondary_id']) . "&type=secondary_units";
        $backLabel = $secondary_label . " Units";
    } else {
        $backLink = "maintenance.php?pmy_id=" . urlencode($maintenance['pmy_id']) . "&type=" . urlencode($type);
        $backLabel = $primary_label . " Units";
    }
}

$smarty->assign('unit_id', $unit_id);
$smarty->assign('maintenance', $maintenance);
$smarty->assign('photos', $photos);
$smarty->assign('edit_mode', $edit_mode);
$smarty->assign('type', $type);
$smarty->assign('backLink', $backLink);
$smarty->assign('backLabel', $backLabel);
$smarty->assign('alert', $alert);
$smarty->display($theme_current . '/view_maintenance.tpl');
$smarty->display($theme_current . '/footer.tpl');
