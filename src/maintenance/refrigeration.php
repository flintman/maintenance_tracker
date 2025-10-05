<?php
require_once 'common/common.php';
if (!isset($_SESSION['user_id'])) {
    echo 'Access denied';
    exit;
}

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

// Fetch trailers for dropdown
$trailers = $pdo->query('SELECT trl_id FROM trailers')->fetchAll();

// Assign all required variables to Smarty

$smarty->assign('questions', $questions);
$smarty->assign('questions_preview', $questions);
$smarty->assign('units', array_map(function($unit) use ($pdo) {
    $unit['answers'] = getAnswers($pdo, $unit['id']);
    $unit['answers_preview'] = $unit['answers'];
    return $unit;
}, $units));
$smarty->assign('archived', array_map(function($unit) use ($pdo) {
    $unit['answers'] = getAnswers($pdo, $unit['id']);
    $unit['answers_preview'] = $unit['answers'];
    return $unit;
}, $archived));
$smarty->assign('trailers', $trailers);

// Display templates
$smarty->display($theme_current . '/header.tpl');
$smarty->display($theme_current . '/refrigeration.tpl');
$smarty->display($theme_current . '/footer.tpl');