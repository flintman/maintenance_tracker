<?php
require 'config.php';
include 'templates/header.php';

// Get the maintenance ID from the query string
$maintenance_id = $_GET['id'] ?? null;
if (!$maintenance_id) {
    echo "No maintenance record selected.";
    exit;
}

// Fetch the maintenance record
$stmt = $pdo->prepare('SELECT * FROM maintenance WHERE id = ?');
$stmt->execute([$maintenance_id]);
$maintenance = $stmt->fetch();

if (!$maintenance) {
    echo "Maintenance record not found.";
    exit;
}

// Decode photos JSON
$photos = $maintenance['photos'] ? json_decode($maintenance['photos'], true) : [];
?>

<h2>Maintenance Details</h2>
<a href="maintenance.php" class="btn btn-secondary mb-3">Back to Maintenance</a>

<table class="table table-bordered">
    <tr><th>Type of Service</th><td><?= htmlspecialchars($maintenance['type_of_service']) ?></td></tr>
    <tr><th>Description</th><td><?= htmlspecialchars($maintenance['description']) ?></td></tr>
    <tr><th>Costs of Parts</th><td>$<?= number_format($maintenance['costs_of_parts'], 2) ?></td></tr>
    <tr><th>Performed At</th><td><?= htmlspecialchars($maintenance['performed_at']) ?></td></tr>
    <tr><th>Performed By</th><td><?= htmlspecialchars($maintenance['performed_by']) ?></td></tr>
</table>

<h3>Photos</h3>
<div class="photo-gallery">
    <?php foreach ($photos as $photo): ?>
        <img src="assets/uploads/<?= htmlspecialchars($photo) ?>" style="max-width:200px; margin:10px;">
    <?php endforeach; ?>
</div>

<?php include 'templates/footer.php'; ?>