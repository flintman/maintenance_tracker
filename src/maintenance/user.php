<?php

require 'common/common.php';
if (!isset($_SESSION['user_id'])) {
    exit;
}

$smarty->display($theme_current . '/header.tpl');
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
    // Generate new API key
    if (isset($_POST['generate_api_key'])) {
        // Generate a secure random API key (28 chars, numbers, letters, symbols)
        $key_bytes = random_bytes(28); // 28 chars base64url
        $api_key = rtrim(strtr(base64_encode($key_bytes), '+/', '-_'), '=');
        $stmt = $pdo->prepare('UPDATE users SET api_key = ? WHERE id = ?');
        $stmt->execute([$api_key, $user_id]);
        $user['api_key'] = $api_key;
        $success_message = $smarty->getTemplateVars('API_KEY_GENERATED');
    }

    // Update email
    if (isset($_POST['email'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = $smarty->getTemplateVars('INVALID_EMAIL_ADDRESS');
        } else {
            $stmt = $pdo->prepare('UPDATE users SET email = ? WHERE id = ?');
            $stmt->execute([$email, $user_id]);
            $user['email'] = $email;
            $success_message = $smarty->getTemplateVars('EMAIL_UPDATED_SUCCESS');
        }
    }
    if (isset($_POST['nickname'])) {
        $nickname = trim($_POST['nickname']);
        if (strlen($nickname) > 50) {
            $errors[] = $smarty->getTemplateVars('NICKNAME_TOO_LONG');
        } else {
            $stmt = $pdo->prepare('UPDATE users SET nickname = ? WHERE id = ?');
            $stmt->execute([$nickname, $user_id]);
            $user['nickname'] = $nickname;
            $success_message = $smarty->getTemplateVars('NICKNAME_UPDATED_SUCCESS');
        }
    }

    // Update theme
    if (isset($_POST['theme']) && in_array($_POST['theme'], $theme_folders)) {
        $stmt = $pdo->prepare('UPDATE users SET theme = ? WHERE id = ?');
        $stmt->execute([$_POST['theme'], $user_id]);
        $user['theme'] = $_POST['theme'];
        $success_message = $smarty->getTemplateVars('THEME_UPDATED_SUCCESS');
    }

    // Update password only if current password is provided
    if (!empty($_POST['current_password']) && isset($_POST['new_password'], $_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (!password_verify($current_password, $user['password'])) {
            $errors[] = $smarty->getTemplateVars('CURRENT_PASSWORD_INCORRECT');
        } elseif ($new_password !== $confirm_password) {
            $errors[] = $smarty->getTemplateVars('NEW_PASSWORDS_MISMATCH');
        } elseif (strlen($new_password) < 8) {
            $errors[] = $smarty->getTemplateVars('NEW_PASSWORD_TOO_SHORT');
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([$hashed_password, $user_id]);
            $success_message = $smarty->getTemplateVars('PASSWORD_UPDATED_SUCCESS');
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
