<?php
require_once 'common.php';
if (!isset($_SESSION['user_id'])) {
    echo 'Access denied';
    exit;
}
if (($_SESSION['privilege'] ?? '') === 'admin') {
    // Archive/unarchive
    if (isset($_GET['archive'])) {
        $archive_id = cleanInput($_GET['archive'], 'int');
        $stmt = $pdo->prepare('UPDATE trailers SET archived = 1 WHERE id = ?');
        $stmt->execute([$archive_id]);
        header('Location: index.php');
        exit;
    }
    if (isset($_GET['unarchive'])) {
        $unarchive_id = cleanInput($_GET['unarchive'], 'int');
        $stmt = $pdo->prepare('UPDATE trailers SET archived = 0 WHERE id = ?');
        $stmt->execute([$unarchive_id]);
        header('Location: index.php');
        exit;
    }

    // Add trailer
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
        $trl_id = cleanInput($_POST['trl_id'], 'int');
        $axles = cleanInput($_POST['axles'], 'int');
        $door_type = cleanInput($_POST['door_type']);
        $length = cleanInput($_POST['length'], 'int');
        $stmt = $pdo->prepare('INSERT INTO trailers (trl_id, axles, door_type, length) VALUES (?, ?, ?, ?)');
        $stmt->execute([$trl_id, $axles, $door_type, $length]);
    }
    // Edit trailer
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
        $id = cleanInput($_POST['id'], 'int');
        $trl_id = cleanInput($_POST['trl_id'], 'int');
        $door_type = cleanInput($_POST['door_type']);
        $length = cleanInput($_POST['length'], 'int');
        $stmt = $pdo->prepare('UPDATE trailers SET trl_id = ?, door_type = ?, length = ? WHERE id = ?');
        $stmt->execute([$trl_id, $door_type, $length, $id]);
    }
}
include_once 'templates/header.php';
// Fetch trailers
$active = $pdo->query('SELECT * FROM trailers WHERE archived = 0')->fetchAll();
$archived = $pdo->query('SELECT * FROM trailers WHERE archived = 1')->fetchAll();

?>
    <h2>Equipment</h2>
    <?php if (($_SESSION['privilege'] ?? '') === 'admin'): ?>
    <h3>
        <button class="btn btn-link collapsed text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#addTrailerForm" aria-expanded="false" aria-controls="addTrailerForm" onclick="toggleArrow(this)">
            <span class="me-2 arrow">➤</span> <span class="toggle-text">Add Trailer</span>
        </button>
    </h3>
    <div class="collapse" id="addTrailerForm">
        <form method="post">
            <input type="hidden" name="add" value="1">
            <div class="mb-3">
                <label class="form-label">Trailer ID</label>
                <input type="number" name="trl_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Axles</label>
                <input type="number" name="axles" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Door Type</label>
                <input type="text" name="door_type" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Length</label>
                <input type="number" name="length" class="form-control" required>
            </div>
            <button class="btn btn-success">Submit</button>
        </form>
    </div>
    <?php endif; ?>
    <h3>Active Trailers</h3>
    <table class="table">
        <thead><tr><th>Trailer ID</th><th>Axles</th><th>Door Type</th><th>Length</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($active as $trl): ?>
            <tr>
                <td><?= htmlspecialchars($trl['trl_id']) ?></td>
                <td><?= htmlspecialchars($trl['axles']) ?></td>
                <td><?= htmlspecialchars($trl['door_type']) ?></td>
                <td><?= htmlspecialchars($trl['length']) ?></td>
                <td>
                    <a href="maintenance.php?trl_id=<?= $trl['id'] ?>&type=trailer" class="btn btn-info btn-sm">View Maintenance</a>
                    <?php if (($_SESSION['privilege'] ?? '') === 'admin'): ?>
                    <a href="equipment.php?archive=<?= $trl['id'] ?>" class="btn btn-warning btn-sm">Archive</a>
                    <button class="btn btn-secondary btn-sm" onclick="editTrl(<?= $trl['id'] ?>, <?= $trl['axles'] ?>, '<?= htmlspecialchars(addslashes($trl['door_type'])) ?>', <?= $trl['length'] ?>)">Edit</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (($_SESSION['privilege'] ?? '') === 'admin'): ?>
    <h3>Archived Trailers</h3>
    <table class="table">
        <thead><tr><th>Trailer ID</th><th>Axles</th><th>Door Type</th><th>Length</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($archived as $trl): ?>
            <tr>
                <td><?= htmlspecialchars($trl['trl_id']) ?></td>
                <td><?= htmlspecialchars($trl['axles']) ?></td>
                <td><?= htmlspecialchars($trl['door_type']) ?></td>
                <td><?= htmlspecialchars($trl['length']) ?></td>
                <td>
                    <a href="equipment.php?unarchive=<?= $trl['id'] ?>" class="btn btn-success btn-sm">Unarchive</a>
                    <a href="maintenance.php?trl_id=<?= $trl['id'] ?>" class="btn btn-info btn-sm">View Maintenance</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <form id="editForm" method="post" style="display:none">
        <input type="hidden" name="edit" value="1">
        <input type="hidden" name="id" id="editId">
        <div class="mb-3">
            <label class="form-label">Trailer ID</label>
            <input type="number" name="trl_id" id="editTrlId" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Axles</label>
            <input type="number" name="axles" id="editAxles" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Door Type</label>
            <input type="text" name="door_type" id="editDoorType" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Length</label>
            <input type="number" name="length" id="editLength" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('editForm').style.display='none'">Cancel</button>
    </form>

    <script>
    function editTrl(id, axles, doorType, length) {
        document.getElementById('editId').value = id;
        document.getElementById('editTrlId').value = id;
        document.getElementById('editAxles').value = axles;
        document.getElementById('editDoorType').value = doorType;
        document.getElementById('editLength').value = length;
        document.getElementById('editForm').style.display = 'block';
        window.scrollTo(0,document.body.scrollHeight);
    }
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
    <?php endif; ?>