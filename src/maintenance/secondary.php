<?php
require_once 'common/common.php';
if (!isset($_SESSION['user_id'])) {
    exit;
}

// Fetch questions for dynamic fields
$questions = $pdo->query('SELECT * FROM secondary_questions ORDER BY position ASC')->fetchAll();

// Handle add/edit secondary_units unit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode'])) {
    if ($_POST['mode'] === 'add') {
        $stmt = $pdo->prepare('INSERT INTO secondary_units (pmy_id, archived) VALUES (?, 0)');
        $stmt->execute([cleanInput($_POST['pmy_id'], 'int')]);
        $secondary_id = $pdo->lastInsertId();
        foreach ($questions as $q) {
            if ($q['type'] === 'multi_choice') {
                $value = isset($_POST['question_' . $q['id']]) ? implode(',', $_POST['question_' . $q['id']]) : '';
            } else {
                $value = $_POST['question_' . $q['id']] ?? '';
            }
            $stmt = $pdo->prepare('INSERT INTO secondary_answers (secondary_id, question_id, value) VALUES (?, ?, ?)');
            $stmt->execute([$secondary_id, $q['id'], $value]);
        }
    } elseif ($_POST['mode'] === 'edit') {
        $id = cleanInput($_POST['id'], 'int');
        $stmt = $pdo->prepare('UPDATE secondary_units SET pmy_id=? WHERE id=?');
        $stmt->execute([cleanInput($_POST['pmy_id'], 'int'), $id]);
        foreach ($questions as $q) {
            if ($q['type'] === 'multi_choice') {
                $value = isset($_POST['question_' . $q['id']]) ? implode(',', $_POST['question_' . $q['id']]) : '';
            } else {
                $value = $_POST['question_' . $q['id']] ?? '';
            }
            $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM secondary_answers WHERE secondary_id=? AND question_id=?');
            $stmtCheck->execute([$id, $q['id']]);
            $exists = $stmtCheck->fetchColumn();
            if ($exists) {
                $stmt = $pdo->prepare('UPDATE secondary_answers SET value=? WHERE secondary_id=? AND question_id=?');
                $stmt->execute([$value, $id, $q['id']]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO secondary_answers (secondary_id, question_id, value) VALUES (?, ?, ?)');
                $stmt->execute([$id, $q['id'], $value]);
            }
        }
    }
}

// Handle archive/unarchive
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archive'])) {
    $id = cleanInput($_POST['id'], 'int');
    $stmt = $pdo->prepare('UPDATE secondary_units SET archived=1 WHERE id=?');
    $stmt->execute([$id]);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unarchive'])) {
    $id = cleanInput($_POST['id'], 'int');
    $stmt = $pdo->prepare('UPDATE secondary_units SET archived=0 WHERE id=?');
    $stmt->execute([$id]);
}

// Fetch units and their answers
$units = $pdo->query('SELECT * FROM secondary_units WHERE archived = 0')->fetchAll();
$archived = $pdo->query('SELECT * FROM secondary_units WHERE archived = 1')->fetchAll();

function getAnswers($pdo, $secondary_id) {
    $stmt = $pdo->prepare('SELECT q.label, q.type, q.options, a.value FROM secondary_answers a JOIN secondary_questions q ON a.question_id = q.id WHERE a.secondary_id = ? ORDER BY q.position ASC');
    $stmt->execute([$secondary_id]);
    return $stmt->fetchAll();
}

// Fetch primary_units for dropdown
$primary_units = $pdo->query('SELECT pmy_id FROM primary_units')->fetchAll();

$smarty->assign('questions', $questions);
$smarty->assign('questions_preview', $questions);
$smarty->assign('questions_first', array_slice($questions, 0, $number_columns));
$smarty->assign('units', array_map(function($unit) use ($pdo, $number_columns) {
    $answers = getAnswers($pdo, $unit['id']);
    $unit['answers'] = $answers;
    $unit['answers_first'] = array_slice($answers, 0, $number_columns);
    return $unit;
}, $units));
$smarty->assign('archived', array_map(function($unit) use ($pdo, $number_columns) {
    $answers = getAnswers($pdo, $unit['id']);
    $unit['answers'] = $answers;
    $unit['answers_first'] = array_slice($answers, 0, $number_columns);
    return $unit;
}, $archived));
$smarty->assign('primary_units', $primary_units);

// Display templates
$smarty->display($theme_current . '/header.tpl');
$smarty->display($theme_current . '/secondary.tpl');
$smarty->display($theme_current . '/footer.tpl');