<?php

require_once '../common/common.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['privilege'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$number_unit = 'primary';
$unit_label = $primary_label;

if(isset($_GET['secondary'])) {
    $number_unit = 'secondary';
    $unit_label = $secondary_label;
}


// Handle add/edit/delete/reorder questions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_question'])) {
        $label = cleanInput($_POST['label']);
        $type = $_POST['type'];
        $options = ($type === 'multi_choice') ? cleanInput($_POST['options']) : null;
        $position = intval($_POST['position']);
        $stmt = $pdo->prepare('INSERT INTO '.$number_unit.'_questions (label, type, options, position) VALUES (?, ?, ?, ?)');
        $stmt->execute([$label, $type, $options, $position]);
    }
    if (isset($_POST['edit_question'])) {
        $id = intval($_POST['id']);
        $label = cleanInput($_POST['label']);
        $type = $_POST['type'];
        $options = ($type === 'multi_choice') ? cleanInput($_POST['options']) : null;
        $position = intval($_POST['position']);
        $stmt = $pdo->prepare('UPDATE '.$number_unit.'_questions SET label=?, type=?, options=?, position=? WHERE id=?');
        $stmt->execute([$label, $type, $options, $position, $id]);
    }
    if (isset($_POST['delete_question'])) {
        $id = intval($_POST['id']);
        $stmt = $pdo->prepare('DELETE FROM '.$number_unit.'_questions WHERE id=?');
        $stmt->execute([$id]);
    }
    if (isset($_POST['move_up']) || isset($_POST['move_down'])) {
        $id = intval($_POST['id']);
        $direction = isset($_POST['move_up']) ? -1 : 1;
        $stmt = $pdo->prepare('SELECT position FROM '.$number_unit.'_questions WHERE id=?');
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();
        $new = $current + $direction;
        $stmt = $pdo->prepare('SELECT id FROM '.$number_unit.'_questions WHERE position=?');
        $stmt->execute([$new]);
        $swap_id = $stmt->fetchColumn();
        if ($swap_id) {
            $pdo->prepare('UPDATE '.$number_unit.'_questions SET position=? WHERE id=?')->execute([$new, $id]);
            $pdo->prepare('UPDATE '.$number_unit.'_questions SET position=? WHERE id=?')->execute([$current, $swap_id]);
        }
    }
}

$smarty->display($theme_current . '/admin/header.tpl');

// Fetch all questions ordered by position
$questions = $pdo->query('SELECT * FROM '.$number_unit.'_questions ORDER BY position ASC')->fetchAll();

$smarty->assign('questions', $questions);
$smarty->assign('unit_label', $unit_label);
$smarty->display($theme_current . '/admin/manage_units.tpl');
$smarty->display($theme_current . '/admin/footer.tpl');