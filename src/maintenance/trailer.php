<?php
require_once 'common/common.php';
if (!isset($_SESSION['user_id'])) {
    echo 'Access denied';
    exit;
}

// Fetch dynamic trailer questions
$questions = $pdo->query('SELECT * FROM trailer_questions ORDER BY position ASC')->fetchAll();

// Handle add/edit trailer
if (($_SESSION['privilege'] ?? '') === 'admin') {
    // Archive/unarchive
    if (isset($_GET['archive'])) {
        $archive_id = cleanInput($_GET['archive'], 'int');
        $stmt = $pdo->prepare('UPDATE trailers SET archived = 1 WHERE id = ?');
        $stmt->execute([$archive_id]);
        header('Location: trailer.php');
        exit;
    }
    if (isset($_GET['unarchive'])) {
        $unarchive_id = cleanInput($_GET['unarchive'], 'int');
        $stmt = $pdo->prepare('UPDATE trailers SET archived = 0 WHERE id = ?');
        $stmt->execute([$unarchive_id]);
        header('Location: trailer.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode'])) {
        if ($_POST['mode'] === 'add') {
            $trl_id = cleanInput($_POST['trl_id'], 'int');
            $stmt = $pdo->prepare('INSERT INTO trailers (trl_id) VALUES (?)');
            $stmt->execute([$trl_id]);
            $trailer_id = $pdo->lastInsertId();
            foreach ($questions as $q) {
                if ($q['type'] === 'multi_choice') {
                    $value = isset($_POST['question_' . $q['id']]) ? implode(',', $_POST['question_' . $q['id']]) : '';
                } else {
                    $value = $_POST['question_' . $q['id']] ?? '';
                }
                $stmt = $pdo->prepare('INSERT INTO trailer_answers (trailer_id, question_id, value) VALUES (?, ?, ?)');
                $stmt->execute([$trailer_id, $q['id'], $value]);
            }
        } elseif ($_POST['mode'] === 'edit') {
            $id = cleanInput($_POST['id'], 'int');
            $trl_id = cleanInput($_POST['trl_id'], 'int');
            $stmt = $pdo->prepare('UPDATE trailers SET trl_id = ? WHERE id = ?');
            $stmt->execute([$trl_id, $id]);
            foreach ($questions as $q) {
                if ($q['type'] === 'multi_choice') {
                    $value = isset($_POST['question_' . $q['id']]) ? implode(',', $_POST['question_' . $q['id']]) : '';
                } else {
                    $value = $_POST['question_' . $q['id']] ?? '';
                }
                $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM trailer_answers WHERE trailer_id=? AND question_id=?');
                $stmtCheck->execute([$id, $q['id']]);
                $exists = $stmtCheck->fetchColumn();
                if ($exists) {
                    $stmt = $pdo->prepare('UPDATE trailer_answers SET value=? WHERE trailer_id=? AND question_id=?');
                    $stmt->execute([$value, $id, $q['id']]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO trailer_answers (trailer_id, question_id, value) VALUES (?, ?, ?)');
                    $stmt->execute([$id, $q['id'], $value]);
                }
            }
        }
    }
}
include_once 'common/header.php';

// Fetch trailers
$active = $pdo->query('SELECT * FROM trailers WHERE archived = 0')->fetchAll();
$archived = $pdo->query('SELECT * FROM trailers WHERE archived = 1')->fetchAll();

function getTrailerAnswers($pdo, $trailer_id) {
    $stmt = $pdo->prepare('SELECT q.label, q.type, q.options, a.value FROM trailer_answers a JOIN trailer_questions q ON a.question_id = q.id WHERE a.trailer_id = ? ORDER BY q.position ASC');
    $stmt->execute([$trailer_id]);
    return $stmt->fetchAll();
}

?>
    <h2>Equipment</h2>
    <?php if (($_SESSION['privilege'] ?? '') === 'admin'): ?>
    <h3>
        <button class="btn btn-link text-decoration-none" type="button" id="toggleAddEditBtn">
            <span class="me-2 arrow" id="addEditArrow">➤</span> <span class="toggle-text" id="addEditText">Add Trailer</span>
        </button>
    </h3>
    <div id="addEditTrailerForm" style="display:none;">
        <form method="post" id="addEditForm">
            <input type="hidden" name="mode" id="addEditMode" value="add">
            <input type="hidden" name="id" id="addEditId" value="">
            <div class="mb-3">
                <label class="form-label">Trailer ID</label>
                <input type="number" name="trl_id" id="addEditTrlId" class="form-control" required>
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
    <?php endif; ?>
    <h3>Active Trailers</h3>
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
        <?php foreach ($active as $trl): ?>
            <?php $answers = getTrailerAnswers($pdo, $trl['id']); ?>
            <tr>
                <td><?= htmlspecialchars($trl['trl_id']) ?></td>
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
                    <a href="maintenance.php?trl_id=<?= $trl['id'] ?>&type=trailer" class="btn btn-info btn-sm">View Maintenance</a>
                    <?php if (($_SESSION['privilege'] ?? '') === 'admin'): ?>
                    <a href="trailer.php?archive=<?= $trl['id'] ?>" class="btn btn-warning btn-sm">Archive</a>
                    <button class="btn btn-secondary btn-sm" onclick="editTrl(<?= $trl['id'] ?>, '<?= htmlspecialchars(json_encode(array_column($answers, 'value', 'label')), ENT_QUOTES) ?>')">Edit</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (($_SESSION['privilege'] ?? '') === 'admin'): ?>
    <h3>Archived Trailers</h3>
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
        <?php foreach ($archived as $trl): ?>
            <?php $answers = getTrailerAnswers($pdo, $trl['id']); ?>
            <tr>
                <td><?= htmlspecialchars($trl['trl_id']) ?></td>
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
                    <a href="trailer.php?unarchive=<?= $trl['id'] ?>" class="btn btn-success btn-sm">Unarchive</a>
                    <a href="maintenance.php?trl_id=<?= $trl['id'] ?>" class="btn btn-info btn-sm">View Maintenance</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <script>
    // Build a JS array of question info
    var questionInfo = [
    <?php foreach ($questions as $q): ?>
        {id: <?= json_encode($q['id']) ?>, type: <?= json_encode($q['type']) ?>, label: <?= json_encode($q['label']) ?>},
    <?php endforeach; ?>
    ];

    document.getElementById('toggleAddEditBtn').addEventListener('click', function() {
        const formDiv = document.getElementById('addEditTrailerForm');
        const arrow = document.getElementById('addEditArrow');
        if (formDiv.style.display === 'none') {
            formDiv.style.display = 'block';
            arrow.textContent = '▼';
            document.getElementById('addEditText').textContent = 'Add Trailer';
        } else {
            formDiv.style.display = 'none';
            arrow.textContent = '➤';
        }
    });
    document.getElementById('addEditCancelBtn').addEventListener('click', function() {
        document.getElementById('addEditTrailerForm').style.display = 'none';
        document.getElementById('addEditArrow').textContent = '➤';
        document.getElementById('addEditMode').value = 'add';
        document.getElementById('addEditId').value = '';
        document.getElementById('addEditText').textContent = 'Add Trailer';
        document.getElementById('addEditTrlId').value = '';

        // Clear dynamic question fields
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
    // Map trailer id to trl_id for edit form population
    var trailerIdToTrlId = {};
    <?php foreach ($active as $trl): ?>
        trailerIdToTrlId[<?= $trl['id'] ?>] = <?= json_encode($trl['trl_id']) ?>;
    <?php endforeach; ?>

    function editTrl(id, answersMapStr) {
        var answersMap = {};
        try {
            answersMap = JSON.parse(answersMapStr);
        } catch (e) {}
        const formDiv = document.getElementById('addEditTrailerForm');
        const arrow = document.getElementById('addEditArrow');
        document.getElementById('addEditMode').value = 'edit';
        document.getElementById('addEditId').value = id;
        document.getElementById('addEditText').textContent = 'Edit Trailer';
        document.getElementById('addEditTrlId').value = trailerIdToTrlId[id] || '';
        // Populate dynamic question fields using answersMap
        questionInfo.forEach(function(q) {
            var val = answersMap[q.label] || '';
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