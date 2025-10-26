<?php
require_once 'common/common.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$number_unit = 'primary';
$unit_label = $primary_label;
$redirect_url = 'units.php';

if(isset($_GET['secondary'])) {
    $number_unit = 'secondary';
    $unit_label = $secondary_label;
    $redirect_url = 'units.php?secondary=1';
}

// Fetch dynamic questions for the current unit type
$equipment_level = ($number_unit === 'primary') ? 1 : 2;
$stmt = $pdo->prepare('SELECT * FROM questions WHERE equipment_level = ? ORDER BY position ASC');
$stmt->execute([$equipment_level]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle add/edit primary
if (($_SESSION['privilege'] ?? '') === 'admin') {
    // Archive/unarchive
    if (isset($_GET['archive'])) {
        $archive_id = cleanInput($_GET['archive'], 'int');
        if ($number_unit === 'secondary') {
            $stmt = $pdo->prepare('UPDATE equipment SET archived = 1 WHERE id = ? and equipment_level = 2');
        } else {
            $stmt = $pdo->prepare('UPDATE equipment SET archived = 1 WHERE id = ? and equipment_level = 1');
        }
        $stmt->execute([$archive_id]);
        header('Location: ' . $redirect_url);
        exit;
    }
    if (isset($_GET['unarchive'])) {
        $unarchive_id = cleanInput($_GET['unarchive'], 'int');
        if ($number_unit === 'secondary') {
            $stmt = $pdo->prepare('UPDATE equipment SET archived = 0 WHERE id = ? and equipment_level = 2');
        } else {
            $stmt = $pdo->prepare('UPDATE equipment SET archived = 0 WHERE id = ? and equipment_level = 1');
        }
        $stmt->execute([$unarchive_id]);
        header('Location: ' . $redirect_url);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mode'])) {
        if ($_POST['mode'] === 'add') {
            $unit_id = cleanInput($_POST['unit_id'], 'int');
            $stmt = $pdo->prepare('INSERT INTO equipment (unit_id, equipment_level) VALUES (?, ?)');
            $stmt->execute([$unit_id, $number_unit === 'primary' ? 1 : 2]);
            $primary_id = $pdo->lastInsertId();
            foreach ($questions as $q) {
                if ($q['type'] === 'multi_choice') {
                    $value = isset($_POST['question_' . $q['id']]) ? implode(',', $_POST['question_' . $q['id']]) : '';
                } else {
                    $value = $_POST['question_' . $q['id']] ?? '';
                }
                $stmt = $pdo->prepare('INSERT INTO answers (equipment_id, question_id, value) VALUES (?, ?, ?)');
                $stmt->execute([$primary_id, $q['id'], $value]);
            }
        } elseif ($_POST['mode'] === 'edit') {
            $id = cleanInput($_POST['id'], 'int');
            $unit_id = cleanInput($_POST['unit_id'], 'int');
            $stmt = $pdo->prepare('UPDATE equipment SET unit_id = ? WHERE id = ? and equipment_level = ?');
            $stmt->execute([$unit_id, $id, $number_unit === 'primary' ? 1 : 2]);
            foreach ($questions as $q) {
                if ($q['type'] === 'multi_choice') {
                    $value = isset($_POST['question_' . $q['id']]) ? implode(',', $_POST['question_' . $q['id']]) : '';
                } else {
                    $value = $_POST['question_' . $q['id']] ?? '';
                }
                $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM answers WHERE equipment_id=? AND question_id=?');
                $stmtCheck->execute([$id, $q['id']]);
                $exists = $stmtCheck->fetchColumn();
                if ($exists) {
                    $stmt = $pdo->prepare('UPDATE answers SET value=? WHERE equipment_id=? AND question_id=?');
                    $stmt->execute([$value, $id, $q['id']]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO answers (equipment_id, question_id, value) VALUES (?, ?, ?)');
                    $stmt->execute([$id, $q['id'], $value]);

                }
            }
        }
    }
}

if ($number_unit === 'secondary') {
    $active = $pdo->query('SELECT * FROM equipment WHERE archived = 0 AND equipment_level = 2')->fetchAll();
} else {
    $active = $pdo->query('SELECT * FROM equipment WHERE archived = 0 AND equipment_level = 1')->fetchAll();
}
if ($number_unit === 'secondary') {
    $archived = $pdo->query('SELECT * FROM equipment WHERE archived = 1 AND equipment_level = 2')->fetchAll();
} else {
    $archived = $pdo->query('SELECT * FROM equipment WHERE archived = 1 AND equipment_level = 1')->fetchAll();
}

function getPrimaryAnswers($pdo, $primary_id, $number_unit) {
    $stmt = $pdo->prepare('SELECT q.label, q.type, q.options, a.value FROM answers a JOIN questions q ON a.question_id = q.id WHERE a.equipment_id = ? ORDER BY q.position ASC');
    $stmt->execute([$primary_id]);
    return $stmt->fetchAll();
}

// Prepare answers for Smarty
foreach ($active as &$pmy) {
    $answers = getPrimaryAnswers($pdo, $pmy['id'], $number_unit);
    $pmy['answers'] = $answers;
    $pmy['answers_first'] = array_slice($answers, 0, $number_columns);
    $pmy['answers_json'] = htmlspecialchars(json_encode(array_column($answers, 'value', 'label')), ENT_QUOTES);
}
unset($pmy);
foreach ($archived as &$pmy) {
    $answers = getPrimaryAnswers($pdo, $pmy['id'], $number_unit);
    $pmy['answers'] = $answers;
    $pmy['answers_first'] = array_slice($answers, 0, $number_columns);
}
unset($pmy);

$primary_units = $pdo->query('SELECT unit_id FROM equipment WHERE equipment_level = 1')->fetchAll();
$smarty->assign('primary_units', $primary_units);

$smarty->assign('is_admin', ($_SESSION['privilege'] ?? '') === 'admin');
$smarty->assign('questions', $questions);
$smarty->assign('questions_first', array_slice($questions, 0, $number_columns));
$smarty->assign('active', $active);
$smarty->assign('archived', $archived);
$smarty->assign('unit_label', $unit_label);
$smarty->assign('number_unit', $number_unit);
$smarty->assign('secondary_id', isset($_GET['secondary']) ? 1 : 0);
$smarty->assign('theme', $_COOKIE['theme'] ?? 'light');
$smarty->display($theme_current . '/header.tpl');
$smarty->display($theme_current . '/units.tpl');
$smarty->display($theme_current . '/footer.tpl');