<?php
require_once '../common.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['privilege'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}
include 'templates/header.php';

// Handle add/edit/delete/reorder questions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_question'])) {
        $label = cleanInput($_POST['label']);
        $type = $_POST['type'];
        $options = ($type === 'multi_choice') ? cleanInput($_POST['options']) : null;
        $position = intval($_POST['position']);
        $stmt = $pdo->prepare('INSERT INTO trailer_questions (label, type, options, position) VALUES (?, ?, ?, ?)');
        $stmt->execute([$label, $type, $options, $position]);
    }
    if (isset($_POST['edit_question'])) {
        $id = intval($_POST['id']);
        $label = cleanInput($_POST['label']);
        $type = $_POST['type'];
        $options = ($type === 'multi_choice') ? cleanInput($_POST['options']) : null;
        $position = intval($_POST['position']);
        $stmt = $pdo->prepare('UPDATE trailer_questions SET label=?, type=?, options=?, position=? WHERE id=?');
        $stmt->execute([$label, $type, $options, $position, $id]);
    }
    if (isset($_POST['delete_question'])) {
        $id = intval($_POST['id']);
        $stmt = $pdo->prepare('DELETE FROM trailer_questions WHERE id=?');
        $stmt->execute([$id]);
    }
    if (isset($_POST['move_up']) || isset($_POST['move_down'])) {
        $id = intval($_POST['id']);
        $direction = isset($_POST['move_up']) ? -1 : 1;
        $stmt = $pdo->prepare('SELECT position FROM trailer_questions WHERE id=?');
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();
        $new = $current + $direction;
        $stmt = $pdo->prepare('SELECT id FROM trailer_questions WHERE position=?');
        $stmt->execute([$new]);
        $swap_id = $stmt->fetchColumn();
        if ($swap_id) {
            $pdo->prepare('UPDATE trailer_questions SET position=? WHERE id=?')->execute([$new, $id]);
            $pdo->prepare('UPDATE trailer_questions SET position=? WHERE id=?')->execute([$current, $swap_id]);
        }
    }
}

// Fetch all questions ordered by position
$questions = $pdo->query('SELECT * FROM trailer_questions ORDER BY position ASC')->fetchAll();
?>
<div class="container mt-5">
    <h2>Manage Trailer Questions</h2>
    <form method="post" class="mb-4">
        <div class="row">
            <div class="col-md-2">
                <input type="text" name="label" class="form-control" placeholder="Question label" required>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-control" id="typeSelect" required onchange="toggleOptionsInput(this)">
                    <option value="string">Short Text</option>
                    <option value="text">Long Text (textarea)</option>
                    <option value="number">Number</option>
                    <option value="date">Date</option>
                    <option value="multi_choice">Multi-Choice</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="options" id="optionsInput" class="form-control" style="display:none" placeholder="Choices (comma separated)">
            </div>
            <div class="col-md-2">
                <input type="number" name="position" class="form-control" placeholder="Order" min="0" value="<?= count($questions) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_question" class="btn btn-success">Add Question</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered">
        <thead><tr><th>Label</th><th>Type</th><th>Options</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($questions as $q): ?>
            <tr>
                <form method="post">
                    <td><input type="text" name="label" value="<?= htmlspecialchars($q['label']) ?>" class="form-control"></td>
                    <td>
                        <select name="type" class="form-control" onchange="toggleOptionsInput(this)">
                            <option value="string" <?= $q['type']=='string'?'selected':'' ?>>Short Text</option>
                            <option value="text" <?= $q['type']=='text'?'selected':'' ?>>Long Text</option>
                            <option value="number" <?= $q['type']=='number'?'selected':'' ?>>Number</option>
                            <option value="date" <?= $q['type']=='date'?'selected':'' ?>>Date</option>
                            <option value="multi_choice" <?= $q['type']=='multi_choice'?'selected':'' ?>>Multi-Choice</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="options" value="<?= htmlspecialchars($q['options']) ?>" class="form-control" <?= $q['type']=='multi_choice'?'':'style="display:none"' ?> placeholder="Choices (comma separated)">
                    </td>
                    <td>
                        <input type="number" name="position" value="<?= $q['position'] ?>" class="form-control" min="0">
                        <button type="submit" name="move_up" class="btn btn-sm btn-secondary">↑</button>
                        <button type="submit" name="move_down" class="btn btn-sm btn-secondary">↓</button>
                    </td>
                    <td>
                        <input type="hidden" name="id" value="<?= $q['id'] ?>">
                        <button type="submit" name="edit_question" class="btn btn-primary btn-sm">Save</button>
                        <button type="submit" name="delete_question" class="btn btn-danger btn-sm" onclick="return confirm('Delete this question?')">Delete</button>
                    </td>
                </form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
function toggleOptionsInput(sel) {
    var optionsInput = sel.parentNode.parentNode.querySelector('input[name="options"]');
    if (sel.value === 'multi_choice') {
        optionsInput.style.display = 'block';
    } else {
        optionsInput.style.display = 'none';
    }
}
</script>
<?php include 'templates/footer.php'; ?>