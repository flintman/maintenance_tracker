<?php
require_once 'common/common.php';
include 'common/header.php';
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
$maintenance_id = cleanInput($_POST['maintenance_id'] ?? null, 'int');
?>

<h2>Maintenance for <?= htmlspecialchars($equipment_name ?? '') ?></h2>
<a href="<?= $refrigeration_id ? 'refrigeration.php' : 'trailer.php' ?>" class="btn btn-secondary mb-3">
    Back to <?= $refrigeration_id ? 'Refrigeration Units' : 'Trailers' ?>
</a>
<h3>
    <button class="btn btn-link collapsed text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#addMaintenanceForm" aria-expanded="false" aria-controls="addMaintenanceForm" onclick="toggleArrow(this)">
        <span class="me-2 arrow">➤</span> <span class="toggle-text">Add Maintenance Record</span>
    </button>
</h3>

<div class="collapse" id="addMaintenanceForm">
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="refrigeration_id" value="<?= $refrigeration_id ?? '' ?>">
        <input type="hidden" name="trl_id" value="<?= $trl_id ?? '' ?>">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">Service Details</div>
            <div class="card-body">
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="type_of_service">Type of Service</label>
                        <input type="text" name="type_of_service" id="type_of_service" class="form-control" required placeholder="e.g. Inspection, Repair">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="performed_by">Performed By</label>
                        <input type="text" name="performed_by" id="performed_by" class="form-control" required placeholder="Technician Name">
                    </div>
                </div>
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="performed_at">Performed On</label>
                        <input type="date" name="performed_at" id="performed_at" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="costs_of_parts">Costs of Parts</label>
                        <input type="number" name="costs_of_parts" id="costs_of_parts" class="form-control" step="0.01" required placeholder="0.00">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold" for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3" required placeholder="Describe the maintenance performed..."></textarea>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white fw-bold">Photos</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold" for="photos">Upload Photos</label>
                    <input type="file" name="photos[]" id="photos" class="form-control" multiple>
                    <small class="form-text text-muted">You can select multiple images.</small>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button class="btn btn-success px-4" name="add_maintenance">Submit</button>
        </div>
    </form>
</div>

<script>
function toggleArrow(button) {
    const arrow = button.querySelector('.arrow');
    arrow.textContent = button.getAttribute('aria-expanded') === 'true' ? '▼' : '➤';
}

// Initialize arrow direction and theme-specific styles on page load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
        const arrow = button.querySelector('.arrow');
        arrow.textContent = button.getAttribute('aria-expanded') === 'true' ? '▼' : '➤';
        button.classList.add('text-' + document.documentElement.getAttribute('data-bs-theme'));
    });
});
</script>

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
                <a href="view_maintenance.php?id=<?= $row['id'] ?>&type=<?= $refrigeration_id ? 'refrigeration' : 'trailer' ?>" class="btn btn-info">View</a>
                <?php if (($_SESSION['privilege'] ?? '') === 'admin'): ?>
                <a href="view_maintenance.php?id=<?= $row['id'] ?>&type=<?= $refrigeration_id ? 'refrigeration' : 'trailer' ?>&edit=1" class="btn btn-warning">Edit</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
