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
// Fetch trailers
$active = $pdo->query('SELECT * FROM trailers WHERE archived = 0')->fetchAll();
$archived = $pdo->query('SELECT * FROM trailers WHERE archived = 1')->fetchAll();

function getTrailerAnswers($pdo, $trailer_id) {
    $stmt = $pdo->prepare('SELECT q.label, q.type, q.options, a.value FROM trailer_answers a JOIN trailer_questions q ON a.question_id = q.id WHERE a.trailer_id = ? ORDER BY q.position ASC');
    $stmt->execute([$trailer_id]);
    return $stmt->fetchAll();
}

// Prepare answers for Smarty
foreach ($active as &$trl) {
    $answers = getTrailerAnswers($pdo, $trl['id']);
    $trl['answers'] = $answers;
    $trl['answers_first3'] = array_slice($answers, 0, 3);
    $trl['answers_json'] = htmlspecialchars(json_encode(array_column($answers, 'value', 'label')), ENT_QUOTES);
}
unset($trl);
foreach ($archived as &$trl) {
    $answers = getTrailerAnswers($pdo, $trl['id']);
    $trl['answers'] = $answers;
    $trl['answers_first3'] = array_slice($answers, 0, 3);
}
unset($trl);

$smarty->assign('is_admin', ($_SESSION['privilege'] ?? '') === 'admin');
$smarty->assign('questions', $questions);
$smarty->assign('questions_first3', array_slice($questions, 0, 3));
$smarty->assign('active', $active);
$smarty->assign('archived', $archived);
$smarty->assign('theme', $_COOKIE['theme'] ?? 'light');
$smarty->display($theme_current . '/header.tpl');
$smarty->display($theme_current . '/trailer.tpl');
$smarty->display($theme_current . '/footer.tpl');