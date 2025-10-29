<?php
require_once 'common/common.php';

// Get active message for message board
$stmt = $pdo->query('SELECT message FROM admin_message WHERE active = 1 LIMIT 1');
$active_message = $stmt->fetchColumn();

$smarty->assign('message_board', $active_message ?: $smarty->getTemplateVars('NO_MESSAGES_AT_THIS_TIME'));


// Auto-login via remember me cookie if session not set
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE remember_token = ?');
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['privilege'] = $user['privilege'];
        // Optionally, refresh the cookie expiry
        setcookie('remember_me', $token, time() + 60*60*24*30, '/', '', true, true);
        // Force reload to ensure session-dependent UI is correct
        header('Location: index.php');
        exit;
    }
}

if (isset($_SESSION['user_id'])) {
    // Dashboard logic
    $stmt = $pdo->query('SELECT COUNT(*) FROM equipment WHERE equipment_level = 1');
    $primary_count = $stmt->fetchColumn();
    $stmt = $pdo->query('SELECT COUNT(*) FROM equipment WHERE equipment_level = 2');
    $unit_count = $stmt->fetchColumn();
    $stmt = $pdo->query('
        SELECT
            m.*,
            eq_primary.unit_id AS primary_id,
            eq_secondary.id AS secondary_id,
            eq_secondary.unit_id AS secondary_primary_id,
            CASE
                WHEN m.secondary_id IS NOT NULL THEN (
                    SELECT a.value
                    FROM answers a
                    JOIN questions q ON a.question_id = q.id
                    WHERE a.id = m.secondary_id AND q.equipment_level = 2 AND q.id = 1
                    LIMIT 1
                )
                ELSE NULL
            END AS secondary_answer_1
        FROM maintenance m
        LEFT JOIN equipment eq_secondary ON m.secondary_id = eq_secondary.id AND eq_secondary.equipment_level = 2
    LEFT JOIN equipment eq_primary ON m.pmy_id = eq_primary.id AND eq_primary.equipment_level = 1
        ORDER BY m.id DESC
        LIMIT 5
    ');
    $latest_maintenance = $stmt->fetchAll();

    // Get user type for admin permissions
    $stmt = $pdo->prepare('SELECT privilege FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user_type = $stmt->fetchColumn();

    $smarty->assign('primary_count', $primary_count);
    $smarty->assign('unit_count', $unit_count);
    $smarty->assign('latest_maintenance', $latest_maintenance);
    $smarty->assign('user_type', $user_type);
    $smarty->display($theme_current . '/header.tpl');
    $smarty->display($theme_current . '/dashboard.tpl');
    $smarty->display($theme_current . '/footer.tpl');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['username']) ?? '';
    $password = cleanInput($_POST['password']) ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE LOWER(username) = LOWER(?)');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['privilege'] = $user['privilege'];
        // Handle Remember Me
        if (!empty($_POST['remember_me'])) {
            $token = bin2hex(random_bytes(32));
            $stmt2 = $pdo->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
            $stmt2->execute([$token, $user['id']]);
            setcookie('remember_me', $token, time() + 60*60*24*30, '/', '', true, true); // 30 days, secure, httpOnly
        } else {
            // Clear any previous token/cookie
            setcookie('remember_me', '', time() - 3600, '/', '', true, true);
            $stmt2 = $pdo->prepare('UPDATE users SET remember_token = NULL WHERE id = ?');
            $stmt2->execute([$user['id']]);
        }
        header('Location: index.php');
        exit;
    } else {
        $error = $smarty->getTemplateVars('INVALID_CREDENTIALS');
    }
}

$smarty->assign('error', !empty($error) ? $error : '');
$smarty->display($theme_current . '/header.tpl');
$smarty->display($theme_current . '/login.tpl');
$smarty->display($theme_current . '/footer.tpl');
