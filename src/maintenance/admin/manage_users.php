<?php
require_once '../common/common.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['privilege'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}
$smarty->display($theme_current . '/admin/header.tpl');

// Handle edit user
$edit_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = intval($_POST['id']);
    $username = cleanInput($_POST['username']);
    $email = cleanInput($_POST['email'], 'email');
    $nickname = cleanInput($_POST['nickname']);
    $privilege = $_POST['privilege'] ?? 'user';
    $password = $_POST['password'] ?? '';
    if ($password) {
        if (strlen($password) < 6) {
            $edit_msg = '<div class="alert alert-danger">Password must be at least 6 characters.</div>';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, privilege=?, password=?, nickname=? WHERE id=?');
            $stmt->execute([$username, $email, $privilege, $hash, $nickname, $id]);
            $edit_msg = '<div class="alert alert-success">User updated (password changed)!</div>';
        }
    } else {
        $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, privilege=?, nickname=? WHERE id=?');
        $stmt->execute([$username, $email, $privilege, $nickname, $id]);
        $edit_msg = '<div class="alert alert-success">User updated!</div>';
    }
}

// Handle delete user
$delete_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare('DELETE FROM users WHERE id=?');
    $stmt->execute([$id]);
    $delete_msg = '<div class="alert alert-success">User deleted!</div>';
}

// Fetch all users
$stmt = $pdo->query('SELECT * FROM users ORDER BY id ASC');
$users = $stmt->fetchAll();

$smarty->assign('users', $users);
$smarty->assign('edit_msg', $edit_msg);
$smarty->assign('delete_msg', $delete_msg);
$smarty->display($theme_current . '/admin/manage_users.tpl');
$smarty->display($theme_current . '/admin/footer.tpl');