<?php
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo 'Access denied';
    exit;
}

// Handle add/edit/archive
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare('INSERT INTO refrigeration (trl_id, model, serial, refrigerant) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_POST['trl_id'], $_POST['model'], $_POST['serial'], $_POST['refrigerant']]);
    }
    if (isset($_POST['edit'])) {
        $stmt = $pdo->prepare('UPDATE refrigeration SET trl_id=?, model=?, serial=?, refrigerant=? WHERE id=?');
        $stmt->execute([$_POST['trl_id'], $_POST['model'], $_POST['serial'], $_POST['refrigerant'], $_POST['id']]);
    }
    if (isset($_POST['archive'])) {
        $stmt = $pdo->prepare('UPDATE refrigeration SET archived=1 WHERE id=?');
        $stmt->execute([$_POST['id']]);
    }
}

include_once 'templates/header.php';
// Fetch refrigeration units
$units = $pdo->query('SELECT * FROM refrigeration WHERE archived = 0')->fetchAll();
$archived = $pdo->query('SELECT * FROM refrigeration WHERE archived = 1')->fetchAll();
// Fetch trailers for dropdown
$trailers = $pdo->query('SELECT trl_id FROM trailers')->fetchAll();
?>

<h2>Refrigeration Units</h2>
<h3>Add Refrigeration Unit</h3>
<form method="post">
    <input type="hidden" name="add" value="1">
    <div class="mb-3">
        <label class="form-label">Trailer ID</label>
        <select name="trl_id" class="form-control" required>
            <option value=""></option>
            <?php foreach ($trailers as $trl): ?>
                <option value="<?= $trl['trl_id'] ?>"><?= $trl['trl_id'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Model</label>
        <input type="text" name="model" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Serial</label>
        <input type="text" name="serial" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Refrigerant</label>
        <input type="text" name="refrigerant" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Add</button>
</form>

<h3>Active Refrigeration Units</h3>
<table class="table">
    <thead><tr><th>Trailer ID</th><th>Model</th><th>Serial</th><th>Refrigerant</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($units as $unit): ?>
        <tr>
            <td><?= htmlspecialchars($unit['trl_id']) ?></td>
            <td><?= htmlspecialchars($unit['model']) ?></td>
            <td><?= htmlspecialchars($unit['serial']) ?></td>
            <td><?= htmlspecialchars($unit['refrigerant']) ?></td>
            <td>
                <form method="post" style="display:inline-block">
                    <input type="hidden" name="id" value="<?= $unit['id'] ?>">
                    <button name="archive" class="btn btn-warning btn-sm">Archive</button>
                </form>
                <a href="maintenance.php?refrigeration_id=<?= $unit['id'] ?>" class="btn btn-info btn-sm">View Maintenance</a>
                <button class="btn btn-secondary btn-sm" onclick="editUnit(<?= $unit['id'] ?>, '<?= htmlspecialchars($unit['trl_id']) ?>', '<?= htmlspecialchars($unit['model']) ?>', '<?= htmlspecialchars($unit['serial']) ?>', '<?= htmlspecialchars($unit['refrigerant']) ?>')">Edit</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h3>Archived Refrigeration Units</h3>
<table class="table">
    <thead><tr><th>Trailer ID</th><th>Model</th><th>Serial</th><th>Refrigerant</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($archived as $unit): ?>
        <tr>
            <td><?= htmlspecialchars($unit['trl_id']) ?></td>
            <td><?= htmlspecialchars($unit['model']) ?></td>
            <td><?= htmlspecialchars($unit['serial']) ?></td>
            <td><?= htmlspecialchars($unit['refrigerant']) ?></td>
            <td>
                <form method="post" style="display:inline-block">
                    <input type="hidden" name="id" value="<?= $unit['id'] ?>">
                    <button name="unarchive" class="btn btn-success btn-sm">Unarchive</button>
                </form>
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
        <select name="trl_id" id="editTrlId" class="form-control" required>
            <option value=""></option>
            <?php foreach ($trailers as $trl): ?>
                <option value="<?= $trl['trl_id'] ?>"><?= $trl['trl_id'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Model</label>
        <input type="text" name="model" id="editModel" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Serial</label>
        <input type="text" name="serial" id="editSerial" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Refrigerant</label>
        <input type="text" name="refrigerant" id="editRefrigerant" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <button type="button" class="btn btn-secondary" onclick="document.getElementById('editForm').style.display='none'">Cancel</button>
</form>

<script>
function editUnit(id, trlId, model, serial, refrigerant) {
    document.getElementById('editId').value = id;
    document.getElementById('editTrlId').value = trlId;
    document.getElementById('editModel').value = model;
    document.getElementById('editSerial').value = serial;
    document.getElementById('editRefrigerant').value = refrigerant;
    document.getElementById('editForm').style.display = 'block';
    window.scrollTo(0,document.body.scrollHeight);
}
</script>
