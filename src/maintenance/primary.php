<?php
require_once 'common/common.php';
if (!isset($_SESSION['user_id'])) {
    exit;
}

// Fetch dynamic primary questions
$questions = $pdo->query('SELECT * FROM primary_questions ORDER BY position ASC')->fetchAll();

// Handle add/edit primary
if (($_SESSION['privilege'] ?? '') === 'admin') {
    // Archive/unarchive
    if (isset($_GET['archive'])) {
        $archive_id = cleanInput($_GET['archive'], 'int');
        $stmt = $pdo->prepare('UPDATE primary_units SET archived = 1 WHERE id = ?');
        $stmt->execute([$archive_id]);
        header('Location: primary.php');
        exit;
    }
    if (isset($_GET['unarchive'])) {
        $unarchive_id = cleanInput($_GET['unarchive'], 'int');
        $stmt = $pdo->prepare('UPDATE primary_units SET archived = 0 WHERE id = ?');
        $stmt->execute([$unarchive_id]);
        header('Location: primary.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode'])) {
        if ($_POST['mode'] === 'add') {
            $pmy_id = cleanInput($_POST['pmy_id'], 'int');
            $stmt = $pdo->prepare('INSERT INTO primary_units (pmy_id) VALUES (?)');
            $stmt->execute([$pmy_id]);
            $primary_id = $pdo->lastInsertId();
            foreach ($questions as $q) {
                if ($q['type'] === 'multi_choice') {
                    $value = isset($_POST['question_' . $q['id']]) ? implode(',', $_POST['question_' . $q['id']]) : '';
                } else {
                    $value = $_POST['question_' . $q['id']] ?? '';
                }
                $stmt = $pdo->prepare('INSERT INTO primary_answers (primary_id, question_id, value) VALUES (?, ?, ?)');
                $stmt->execute([$primary_id, $q['id'], $value]);
            }
        } elseif ($_POST['mode'] === 'edit') {
            $id = cleanInput($_POST['id'], 'int');
            $pmy_id = cleanInput($_POST['pmy_id'], 'int');
            $stmt = $pdo->prepare('UPDATE primary_units SET pmy_id = ? WHERE id = ?');
            $stmt->execute([$pmy_id, $id]);
            foreach ($questions as $q) {
                if ($q['type'] === 'multi_choice') {
                    $value = isset($_POST['question_' . $q['id']]) ? implode(',', $_POST['question_' . $q['id']]) : '';
                } else {
                    $value = $_POST['question_' . $q['id']] ?? '';
                }
                $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM primary_answers WHERE primary_id=? AND question_id=?');
                $stmtCheck->execute([$id, $q['id']]);
                $exists = $stmtCheck->fetchColumn();
                if ($exists) {
                    $stmt = $pdo->prepare('UPDATE primary_answers SET value=? WHERE primary_id=? AND question_id=?');
                    $stmt->execute([$value, $id, $q['id']]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO primary_answers (primary_id, question_id, value) VALUES (?, ?, ?)');
                    $stmt->execute([$id, $q['id'], $value]);
                }
            }
        }
    }
}
// Fetch primary_units
$active = $pdo->query('SELECT * FROM primary_units WHERE archived = 0')->fetchAll();
$archived = $pdo->query('SELECT * FROM primary_units WHERE archived = 1')->fetchAll();

function getPrimaryAnswers($pdo, $primary_id) {
    $stmt = $pdo->prepare('SELECT q.label, q.type, q.options, a.value FROM primary_answers a JOIN primary_questions q ON a.question_id = q.id WHERE a.primary_id = ? ORDER BY q.position ASC');
    $stmt->execute([$primary_id]);
    return $stmt->fetchAll();
}

// Prepare answers for Smarty
foreach ($active as &$pmy) {
    $answers = getPrimaryAnswers($pdo, $pmy['id']);
    $pmy['answers'] = $answers;
    $pmy['answers_first'] = array_slice($answers, 0, $number_columns);
    $pmy['answers_json'] = htmlspecialchars(json_encode(array_column($answers, 'value', 'label')), ENT_QUOTES);
}
unset($pmy);
foreach ($archived as &$pmy) {
    $answers = getPrimaryAnswers($pdo, $pmy['id']);
    $pmy['answers'] = $answers;
    $pmy['answers_first'] = array_slice($answers, 0, $number_columns);
}
unset($pmy);

$smarty->assign('is_admin', ($_SESSION['privilege'] ?? '') === 'admin');
$smarty->assign('questions', $questions);
$smarty->assign('questions_first', array_slice($questions, 0, $number_columns));
$smarty->assign('active', $active);
$smarty->assign('archived', $archived);
$smarty->assign('theme', $_COOKIE['theme'] ?? 'light');
$smarty->display($theme_current . '/header.tpl');
$smarty->display($theme_current . '/primary.tpl');
$smarty->display($theme_current . '/footer.tpl');