<?php

require 'common/common.php';
$smarty->display($theme_current . '/header.tpl');
if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();


$errors = [];
$success_message = '';

// Get available themes from templates/ directory
$themes_dir = __DIR__ . '/templates/';
if (!is_dir($themes_dir)) {
    $themes_dir = realpath(__DIR__ . '/../templates/');
}
$theme_folders = [];
if ($themes_dir && is_dir($themes_dir)) {
    foreach (scandir($themes_dir) as $file) {
        if ($file[0] !== '.' && is_dir($themes_dir . '/' . $file)) {
            $theme_folders[] = $file;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update email
    if (isset($_POST['email'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address.';
        } else {
            $stmt = $pdo->prepare('UPDATE users SET email = ? WHERE id = ?');
            $stmt->execute([$email, $user_id]);
            $user['email'] = $email;
            $success_message = 'Email updated successfully.';
        }
    }

    // Update theme
    if (isset($_POST['theme']) && in_array($_POST['theme'], $theme_folders)) {
        $stmt = $pdo->prepare('UPDATE users SET theme = ? WHERE id = ?');
        $stmt->execute([$_POST['theme'], $user_id]);
        $user['theme'] = $_POST['theme'];
        $success_message = 'Theme updated successfully.';
    }

    // Update password only if current password is provided
    if (!empty($_POST['current_password']) && isset($_POST['new_password'], $_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'New passwords do not match.';
        } elseif (strlen($new_password) < 8) {
            $errors[] = 'New password must be at least 8 characters long.';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([$hashed_password, $user_id]);
            $success_message = 'Password updated successfully.';
        }
    }
}

$smarty->assign('user', $user);
$smarty->assign('themes', $theme_folders);
$smarty->assign('errors', $errors);
$smarty->assign('success_message', $success_message);
$refresh = '';
if (!empty($success_message)) {
    $refresh = '<meta http-equiv="refresh" content="2">';
}
$smarty->assign('refresh', $refresh);
$smarty->display($theme_current . '/user.tpl');
$smarty->display($theme_current . '/footer.tpl');
