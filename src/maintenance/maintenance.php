<?php
session_start();
require 'config.php';
include 'templates/header.php';
if (!isset($_SESSION['user_id'])) {
    exit;
}

// Determine context: refrigeration or trailer
$refrigeration_id = $_GET['refrigeration_id'] ?? null;
$trl_id = $_GET['trl_id'] ?? null;

if (!$refrigeration_id && !$trl_id) {
    echo "No equipment selected.";
    exit;
}

// Fetch equipment name
if ($refrigeration_id) {
    $stmt = $pdo->prepare('SELECT model AS name FROM refrigeration WHERE id = ?');
    $stmt->execute([$refrigeration_id]);
} else {
    $stmt = $pdo->prepare('SELECT trl_id FROM trailers WHERE id = ?');
    $stmt->execute([$trl_id]);
}
$eq = $stmt->fetch();

// Add maintenance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_maintenance'])) {
    $refrigeration_id = $_POST['refrigeration_id'] ?? null;
    $trl_id = $_POST['trl_id'] ?? null;
    $type_of_service = $_POST['type_of_service'] ?? '';
    $description = $_POST['description'] ?? '';
    $costs_of_parts = $_POST['costs_of_parts'] ?? 0;
    $performed_at = $_POST['performed_at'] ?? null;
    $performed_by = $_POST['performed_by'] ?? '';

    // Validate refrigeration_id and trl_id immediately after retrieving from POST
    $refrigeration_id = isset($_POST['refrigeration_id']) && $_POST['refrigeration_id'] !== '' ? (int)$_POST['refrigeration_id'] : null;
    $trl_id = isset($_POST['trl_id']) && $_POST['trl_id'] !== '' ? (int)$_POST['trl_id'] : null;

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

// Ensure $maintenance_id is defined
$maintenance_id = $_POST['maintenance_id'] ?? null;

// Ensure refrigeration_id and trl_id are properly set to NULL if empty
$refrigeration_id = isset($_GET['refrigeration_id']) && $_GET['refrigeration_id'] !== '' ? (int)$_GET['refrigeration_id'] : null;
$trl_id = isset($_GET['trl_id']) && $_GET['trl_id'] !== '' ? (int)$_GET['trl_id'] : null;
?>

<h2>Maintenance for <?= htmlspecialchars($eq['name'] ?? '') ?></h2>
<a href="equipment.php" class="btn btn-secondary mb-3">Back to Equipment</a>

<h3>Add Maintenance Record</h3>
<?php if (!empty($msg)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="refrigeration_id" value="<?= $refrigeration_id ?? '' ?>">
    <input type="hidden" name="trl_id" value="<?= $trl_id ?? '' ?>">
    <div class="mb-2">
        <label>Type of Service</label>
        <input type="text" name="type_of_service" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Description</label>
        <textarea name="description" class="form-control" required></textarea>
    </div>
    <div class="mb-2">
        <label>Costs of Parts</label>
        <input type="number" name="costs_of_parts" class="form-control" step="0.01" required>
    </div>
    <div class="mb-2">
        <label>Performed At</label>
        <input type="date" name="performed_at" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Performed By</label>
        <input type="text" name="performed_by" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Photos</label>
        <input type="file" name="photos[]" class="form-control" multiple>
    </div>
    <button class="btn btn-primary" name="add_maintenance">Add Maintenance</button>
</form>

<h3>Maintenance Records</h3>
<table class="table table-bordered">
    <thead><tr><th>ID</th><th>Type</th><th>Description</th><th>Actions</th></tr></thead>
    <tbody>
    <?php
    if ($refrigeration_id) {
        $stmt = $pdo->prepare('SELECT * FROM maintenance WHERE refrigeration_id = ? ORDER BY performed_at DESC');
        $stmt->execute([$refrigeration_id]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM maintenance WHERE trl_id = ? ORDER BY performed_at DESC');
        $stmt->execute([$trl_id]);
    }
    while ($row = $stmt->fetch()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['type_of_service']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td>
                <a href="view_maintenance.php?id=<?= $row['id'] ?>" class="btn btn-info">View Maintenance</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
