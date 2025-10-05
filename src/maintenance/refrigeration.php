<?php
require_once 'common.php';
if (!isset($_SESSION['user_id'])) {
    echo 'Access denied';
    exit;
}
include_once 'templates/header.php';

// Fetch questions for dynamic fields
$questions = $pdo->query('SELECT * FROM refrigeration_questions ORDER BY position ASC')->fetchAll();

// Handle add/edit refrigeration unit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode'])) {
    if ($_POST['mode'] === 'add') {
        $stmt = $pdo->prepare('INSERT INTO refrigeration (trl_id, archived) VALUES (?, 0)');
        $stmt->execute([cleanInput($_POST['trl_id'], 'int')]);
        $refrigeration_id = $pdo->lastInsertId();
        foreach ($questions as $q) {
            if ($q['type'] === 'multi_choice') {
                $value = isset($_POST['question_' . $q['id']]) ? implode(',', $_POST['question_' . $q['id']]) : '';
            } else {
                $value = $_POST['question_' . $q['id']] ?? '';
            }
            $stmt = $pdo->prepare('INSERT INTO refrigeration_answers (refrigeration_id, question_id, value) VALUES (?, ?, ?)');
            $stmt->execute([$refrigeration_id, $q['id'], $value]);
        }
    } elseif ($_POST['mode'] === 'edit') {
        $id = cleanInput($_POST['id'], 'int');
        $stmt = $pdo->prepare('UPDATE refrigeration SET trl_id=? WHERE id=?');
        $stmt->execute([cleanInput($_POST['trl_id'], 'int'), $id]);
        foreach ($questions as $q) {
            if ($q['type'] === 'multi_choice') {
                $value = isset($_POST['question_' . $q['id']]) ? implode(',', $_POST['question_' . $q['id']]) : '';
            } else {
                $value = $_POST['question_' . $q['id']] ?? '';
            }
            $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM refrigeration_answers WHERE refrigeration_id=? AND question_id=?');
            $stmtCheck->execute([$id, $q['id']]);
            $exists = $stmtCheck->fetchColumn();
            if ($exists) {
                $stmt = $pdo->prepare('UPDATE refrigeration_answers SET value=? WHERE refrigeration_id=? AND question_id=?');
                $stmt->execute([$value, $id, $q['id']]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO refrigeration_answers (refrigeration_id, question_id, value) VALUES (?, ?, ?)');
                $stmt->execute([$id, $q['id'], $value]);
            }
            $stmt = $pdo->prepare('UPDATE refrigeration_answers SET value=? WHERE refrigeration_id=? AND question_id=?');
            $stmt->execute([$value, $id, $q['id']]);
        }
    }
}

// Handle archive/unarchive
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archive'])) {
    $id = cleanInput($_POST['id'], 'int');
    $stmt = $pdo->prepare('UPDATE refrigeration SET archived=1 WHERE id=?');
    $stmt->execute([$id]);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unarchive'])) {
    $id = cleanInput($_POST['id'], 'int');
    $stmt = $pdo->prepare('UPDATE refrigeration SET archived=0 WHERE id=?');
    $stmt->execute([$id]);
}

// Fetch units and their answers
$units = $pdo->query('SELECT * FROM refrigeration WHERE archived = 0')->fetchAll();
$archived = $pdo->query('SELECT * FROM refrigeration WHERE archived = 1')->fetchAll();

function getAnswers($pdo, $refrigeration_id) {
    $stmt = $pdo->prepare('SELECT q.label, q.type, q.options, a.value FROM refrigeration_answers a JOIN refrigeration_questions q ON a.question_id = q.id WHERE a.refrigeration_id = ? ORDER BY q.position ASC');
    $stmt->execute([$refrigeration_id]);
    return $stmt->fetchAll();
}

// Fetch trailers for dropdown: only those not already associated with a refrigeration unit
$trailers = $pdo->query('SELECT trl_id FROM trailers WHERE trl_id NOT IN (SELECT trl_id FROM refrigeration)')->fetchAll();
?>

<h2>Refrigeration Units</h2>
<?php if (($_SESSION['privilege'] ?? '') === 'admin'): ?>
<h3>
    <button class="btn btn-link text-decoration-none" type="button" id="toggleAddEditBtn">
        <span class="me-2 arrow" id="addEditArrow">➤</span> <span class="toggle-text" id="addEditText">Add Refrigeration Unit</span>
    </button>
</h3>
<?php endif; ?>
<div id="addEditRefrigerationForm" style="display:none;">
    <form method="post" id="addEditForm">
        <input type="hidden" name="mode" id="addEditMode" value="add">
        <input type="hidden" name="id" id="addEditId" value="">
        <div class="mb-3">
            <label class="form-label">Trailer ID</label>
            <select name="trl_id" id="addEditTrlId" class="form-control" required>
                <option value=""></option>
                <?php foreach ($trailers as $trl): ?>
                    <option value="<?= $trl['trl_id'] ?>"><?= $trl['trl_id'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php foreach ($questions as $q): ?>
        <div class="mb-3">
            <label class="form-label"><?= htmlspecialchars($q['label']) ?></label>
            <?php if ($q['type'] === 'multi_choice'): ?>
                <select name="question_<?= $q['id'] ?>[]" id="addEditQ<?= $q['id'] ?>" class="form-control" multiple>
                    <?php foreach (explode(',', $q['options']) as $opt): ?>
                        <option value="<?= htmlspecialchars(trim($opt)) ?>"><?= htmlspecialchars(trim($opt)) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ($q['type'] === 'text'): ?>
                <textarea name="question_<?= $q['id'] ?>" id="addEditQ<?= $q['id'] ?>" class="form-control"></textarea>
            <?php elseif ($q['type'] === 'number'): ?>
                <input type="number" name="question_<?= $q['id'] ?>" id="addEditQ<?= $q['id'] ?>" class="form-control">
            <?php elseif ($q['type'] === 'date'): ?>
                <input type="date" name="question_<?= $q['id'] ?>" id="addEditQ<?= $q['id'] ?>" class="form-control">
            <?php else: ?>
                <input type="text" name="question_<?= $q['id'] ?>" id="addEditQ<?= $q['id'] ?>" class="form-control">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <button class="btn btn-success" id="addEditSubmitBtn">Submit</button>
        <button type="button" class="btn btn-secondary" id="addEditCancelBtn">Cancel</button>
    </form>
</div>

<h3>Active Refrigeration Units</h3>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Trailer ID</th>
                <?php foreach (array_slice($questions, 0, 3) as $q): ?>
                    <th><?= htmlspecialchars($q['label']) ?></th>
                <?php endforeach; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($units as $unit): ?>
                <?php $answers = getAnswers($pdo, $unit['id']); ?>
                <tr>
                    <td><?= htmlspecialchars($unit['trl_id']) ?></td>
                    <?php foreach (array_slice($answers, 0, 3) as $a): ?>
                        <td>
                            <?php if ($a['type'] === 'multi_choice'): ?>
                                <?php foreach (explode(',', $a['value']) as $val): ?>
                                    <span class="badge bg-info text-dark"><?= htmlspecialchars(trim($val)) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?= htmlspecialchars($a['value']) ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <a href="maintenance.php?refrigeration_id=<?= $unit['id'] ?>&type=refrigeration" class="btn btn-info btn-sm">View Maintenance</a>
                        <?php if (($_SESSION['privilege'] ?? '') === 'admin'): ?>
                        <form method="post" style="display:inline-block">
                            <input type="hidden" name="id" value="<?= $unit['id'] ?>">
                            <button name="archive" class="btn btn-warning btn-sm">Archive</button>
                        </form>
                        <button class="btn btn-secondary btn-sm" onclick="editUnit(<?= $unit['id'] ?>, <?= $unit['trl_id'] ?>)">Edit</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (($_SESSION['privilege'] ?? '') === 'admin'): ?>
<h3>Archived Refrigeration Units</h3>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Trailer ID</th>
                <?php foreach (array_slice($questions, 0, 3) as $q): ?>
                    <th><?= htmlspecialchars($q['label']) ?></th>
                <?php endforeach; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($archived as $unit): ?>
                <?php $answers = getAnswers($pdo, $unit['id']); ?>
                <tr>
                    <td><?= htmlspecialchars($unit['trl_id']) ?></td>
                    <?php foreach (array_slice($answers, 0, 3) as $a): ?>
                        <td>
                            <?php if ($a['type'] === 'multi_choice'): ?>
                                <?php foreach (explode(',', $a['value']) as $val): ?>
                                    <span class="badge bg-info text-dark"><?= htmlspecialchars(trim($val)) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?= htmlspecialchars($a['value']) ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
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
</div>

<script>
let lastEditId = null;
let addEditMode = 'add';
document.getElementById('toggleAddEditBtn').addEventListener('click', function() {
    const formDiv = document.getElementById('addEditRefrigerationForm');
    const arrow = document.getElementById('addEditArrow');
    if (formDiv.style.display === 'none') {
        formDiv.style.display = 'block';
        arrow.textContent = '▼';
        document.getElementById('addEditText').textContent = addEditMode === 'edit' ? 'Edit Refrigeration Unit' : 'Add Refrigeration Unit';
    } else {
        formDiv.style.display = 'none';
        arrow.textContent = '➤';
    }
});
// Build a JS array of question info
var questionInfo = [
<?php foreach ($questions as $q): ?>
    {id: <?= json_encode($q['id']) ?>, type: <?= json_encode($q['type']) ?>, label: <?= json_encode($q['label']) ?>},
<?php endforeach; ?>
];

document.getElementById('addEditCancelBtn').addEventListener('click', function() {
    document.getElementById('addEditRefrigerationForm').style.display = 'none';
    document.getElementById('addEditArrow').textContent = '➤';
    addEditMode = 'add';
    document.getElementById('addEditMode').value = 'add';
    document.getElementById('addEditId').value = '';
    document.getElementById('addEditText').textContent = 'Add Refrigeration Unit';
    // Clear form fields
    document.getElementById('addEditTrlId').value = '';
    // Dynamically clear all question fields
    questionInfo.forEach(function(q) {
        var el = document.getElementById('addEditQ' + q.id);
        if (el) {
            if (q.type === 'multi_choice') {
                for (var i = 0; i < el.options.length; i++) {
                    el.options[i].selected = false;
                }
            } else {
                el.value = '';
            }
        }
    });
});
function editUnit(id, trlId) {
    // Show the add/edit form in edit mode
    const formDiv = document.getElementById('addEditRefrigerationForm');
    const arrow = document.getElementById('addEditArrow');
    addEditMode = 'edit';
    document.getElementById('addEditMode').value = 'edit';
    document.getElementById('addEditId').value = id;
    document.getElementById('addEditText').textContent = 'Edit Refrigeration Unit';
    document.getElementById('addEditTrlId').value = trlId;
    // Find the unit and its answers in JS
    var units = <?php echo json_encode($units); ?>;
    var answersMap = {};
    <?php foreach ($units as $unit): ?>
        answersMap[<?= $unit['id'] ?>] = {};
        <?php $ans = getAnswers($pdo, $unit['id']); ?>
        <?php foreach ($ans as $a): ?>
            answersMap[<?= $unit['id'] ?>][<?= json_encode($a['label']) ?>] = <?= json_encode($a['value']) ?>;
        <?php endforeach; ?>
    <?php endforeach; ?>
    // Dynamically populate all question fields
    questionInfo.forEach(function(q) {
        var val = answersMap[id][q.label] || '';
        var el = document.getElementById('addEditQ' + q.id);
        if (el) {
            if (q.type === 'multi_choice') {
                var opts = val.split(',');
                for (var i = 0; i < el.options.length; i++) {
                    el.options[i].selected = opts.includes(el.options[i].value);
                }
            } else {
                el.value = val;
            }
        }
    });
    formDiv.style.display = 'block';
    arrow.textContent = '▼';
    window.scrollTo(0,document.body.scrollHeight);
}
</script>
<?php endif; ?>
<?php include 'templates/footer.php'; ?>