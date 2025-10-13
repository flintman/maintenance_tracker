<?php

require_once '../common/common.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['privilege'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}
$smarty->display($theme_current . '/admin/header.tpl');

// Handle quick add user
$user_add_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = cleanInput($_POST['username']);
    $email = cleanInput($_POST['email'], 'email');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    if ($password !== $password2) {
        $user_add_msg = '<div class="alert alert-danger">Passwords do not match.</div>';
    } elseif (strlen($password) < 6) {
        $user_add_msg = '<div class="alert alert-danger">Password must be at least 6 characters.</div>';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, privilege) VALUES (?, ?, ?, ?)');
        try {
            $stmt->execute([$username, $email, $hash, 'user']);
            $user_add_msg = '<div class="alert alert-success">User added!</div>';
        } catch (PDOException $e) {
            $user_add_msg = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Dashboard stats
$stmt = $pdo->query('SELECT COUNT(*) FROM primary_units');
$primary_count = $stmt->fetchColumn();
$stmt = $pdo->query('SELECT COUNT(*) FROM secondary_units');
$unit_count = $stmt->fetchColumn();
$stmt = $pdo->query('SELECT COUNT(*) FROM users');
$user_count = $stmt->fetchColumn();

$smarty->assign('primary_count', $primary_count);
$smarty->assign('unit_count', $unit_count);
$smarty->assign('user_count', $user_count);
$smarty->assign('user_add_msg', $user_add_msg);
$smarty->display($theme_current . '/admin/index.tpl');
$smarty->display($theme_current . '/admin/footer.tpl');
