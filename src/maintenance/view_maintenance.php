<?php
require 'common.php';
include 'templates/header.php';

$maintenance_id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == '1';

if (!$maintenance_id) {
    echo "No maintenance record selected.";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $type_of_service = cleanInput($_POST['type_of_service']);
    $description = cleanInput($_POST['description']);
    $costs_of_parts = cleanInput($_POST['costs_of_parts'], 'int');
    $performed_at = cleanInput($_POST['performed_at']);
    $performed_by = cleanInput($_POST['performed_by']);
    $stmt = $pdo->prepare('UPDATE maintenance SET type_of_service=?, description=?, costs_of_parts=?, performed_at=?, performed_by=? WHERE id=?');
    $stmt->execute([$type_of_service, $description, $costs_of_parts, $performed_at, $performed_by, $maintenance_id]);
    echo '<div class="alert alert-success">Maintenance updated!</div>';
}

// Handle photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $target_dir = "assets/uploads/";
    $target_file = $target_dir . basename($_FILES['photo']['name']);
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        $stmt = $pdo->prepare('SELECT photos FROM maintenance WHERE id=?');
        $stmt->execute([$maintenance_id]);
        $maintenance = $stmt->fetch();
        $photos = $maintenance['photos'] ? json_decode($maintenance['photos'], true) : [];
        $photos[] = basename($_FILES['photo']['name']);
        $stmt = $pdo->prepare('UPDATE maintenance SET photos=? WHERE id=?');
        $stmt->execute([json_encode($photos), $maintenance_id]);
        echo '<div class="alert alert-success">Photo uploaded!</div>';
    } else {
        echo '<div class="alert alert-danger">Photo upload failed.</div>';
    }
}

// Handle photo delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_photo'])) {
    $photo_to_delete = cleanInput($_POST['delete_photo']);
    $stmt = $pdo->prepare('SELECT photos FROM maintenance WHERE id=?');
    $stmt->execute([$maintenance_id]);
    $maintenance = $stmt->fetch();
    $photos = $maintenance['photos'] ? json_decode($maintenance['photos'], true) : [];
    $photos = array_filter($photos, function($p) use ($photo_to_delete) { return $p !== $photo_to_delete; });
    $stmt = $pdo->prepare('UPDATE maintenance SET photos=? WHERE id=?');
    $stmt->execute([json_encode(array_values($photos)), $maintenance_id]);
    @unlink("assets/uploads/" . $photo_to_delete);
    echo '<div class="alert alert-success">Photo deleted!</div>';
}

$stmt = $pdo->prepare('SELECT * FROM maintenance WHERE id = ?');
$stmt->execute([$maintenance_id]);
$maintenance = $stmt->fetch();
if (!$maintenance) {
    echo "Maintenance record not found.";
    exit;
}
$photos = $maintenance['photos'] ? json_decode($maintenance['photos'], true) : [];
if ($type === 'refrigeration') {
    $backLink = "maintenance.php?refrigeration_id=" . urlencode($maintenance['refrigeration_id']) . "&type=refrigeration";
} else {
    $backLink = "maintenance.php?trl_id=" . urlencode($maintenance['trl_id']) . "&type=" . urlencode($type);
}
?>

<h2><?= $edit_mode ? 'Edit' : 'View' ?> Maintenance Details</h2>
<a href="<?= $backLink ?>" class="btn btn-secondary mb-3">Back to <?= $type === 'refrigeration' ? 'Refrigeration Units' : 'Trailers' ?></a>

<?php if ($edit_mode): ?>
<form method="post">
<table class="table table-bordered">
    <tr><th>Type of Service</th><td><input type="text" name="type_of_service" value="<?= htmlspecialchars($maintenance['type_of_service']) ?>" class="form-control"></td></tr>
    <tr><th>Description</th><td><textarea name="description" class="form-control"><?= htmlspecialchars($maintenance['description']) ?></textarea></td></tr>
    <tr><th>Costs of Parts</th><td><input type="number" name="costs_of_parts" value="<?= htmlspecialchars($maintenance['costs_of_parts']) ?>" class="form-control"></td></tr>
    <tr><th>Performed At</th><td><input type="text" name="performed_at" value="<?= htmlspecialchars($maintenance['performed_at']) ?>" class="form-control"></td></tr>
    <tr><th>Performed By</th><td><input type="text" name="performed_by" value="<?= htmlspecialchars($maintenance['performed_by']) ?>" class="form-control"></td></tr>
</table>
<button type="submit" name="update" class="btn btn-primary">Update Maintenance</button>
</form>
<?php else: ?>
<table class="table table-bordered">
    <tr><th>Type of Service</th><td><?= htmlspecialchars($maintenance['type_of_service']) ?></td></tr>
    <tr><th>Description</th><td><?= htmlspecialchars($maintenance['description']) ?></td></tr>
    <tr><th>Costs of Parts</th><td>$<?= number_format($maintenance['costs_of_parts'], 2) ?></td></tr>
    <tr><th>Performed At</th><td><?= htmlspecialchars($maintenance['performed_at']) ?></td></tr>
    <tr><th>Performed By</th><td><?= htmlspecialchars($maintenance['performed_by']) ?></td></tr>
</table>
<?php endif; ?>

<h3>Photos</h3>
<?php if ($edit_mode): ?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="photo" accept="image/*">
    <button type="submit" class="btn btn-success">Add Photo</button>
</form>
<?php endif; ?>
<div class="photo-gallery">
    <?php foreach ($photos as $photo): ?>
        <div style="display:inline-block; margin:10px;">
            <img src="assets/uploads/<?= htmlspecialchars($photo) ?>" style="max-width:200px; display:block;">
            <?php if ($edit_mode): ?>
            <form method="post" style="display:inline;">
                <input type="hidden" name="delete_photo" value="<?= htmlspecialchars($photo) ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'templates/footer.php'; ?>