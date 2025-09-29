<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    echo 'Access denied';
    exit;
}

// Archive/unarchive
if (isset($_GET['archive'])) {
    $stmt = $pdo->prepare('UPDATE trailers SET archived = 1 WHERE id = ?');
    $stmt->execute([$_GET['archive']]);
    header('Location: index.php');
    exit;
}
if (isset($_GET['unarchive'])) {
    $stmt = $pdo->prepare('UPDATE trailers SET archived = 0 WHERE id = ?');
    $stmt->execute([$_GET['unarchive']]);
    header('Location: index.php');
    exit;
}

include_once 'templates/header.php';
// Add trailer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $trl_id = $_POST['trl_id'] ?? '';
    $axles = $_POST['axles'] ?? '';
    $door_type = $_POST['door_type'] ?? '';
    $length = $_POST['length'] ?? '';
    $stmt = $pdo->prepare('INSERT INTO trailers (trl_id, axles, door_type, length) VALUES (?, ?, ?, ?)');
    $stmt->execute([$trl_id, $axles, $door_type, $length]);
}
// Edit trailer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'] ?? '';
    $trl_id = $_POST['trl_id'] ?? '';
    $door_type = $_POST['door_type'] ?? '';
    $length = $_POST['length'] ?? '';
    $stmt = $pdo->prepare('UPDATE trailers SET trl_id = ?, door_type = ?, length = ? WHERE id = ?');
    $stmt->execute([$trl_id, $door_type, $length, $id]);
}
// Fetch trailers
$active = $pdo->query('SELECT * FROM trailers WHERE archived = 0')->fetchAll();
$archived = $pdo->query('SELECT * FROM trailers WHERE archived = 1')->fetchAll();


?>
    <h2>Equipment</h2>
    <h3>Add Trailer</h3>
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
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
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
                    <a href="equipment.php?archive=<?= $trl['id'] ?>" class="btn btn-warning btn-sm">Archive</a>
                    <a href="maintenance.php?trl_id=<?= $trl['id'] ?>" class="btn btn-info btn-sm">View Maintenance</a>
                    <button class="btn btn-secondary btn-sm" onclick="editTrl(<?= $trl['id'] ?>, <?= $trl['axles'] ?>, '<?= htmlspecialchars(addslashes($trl['door_type'])) ?>', <?= $trl['length'] ?>)">Edit</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
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
    </script>
