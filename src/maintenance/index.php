<?php
require_once 'common/common.php';

// Get active message for message board
$stmt = $pdo->query('SELECT message FROM admin_message WHERE active = 1 LIMIT 1');
$active_message = $stmt->fetchColumn();

$smarty->assign('message_board', $active_message ?: 'No messages at this time.');


if (isset($_SESSION['user_id'])) {
    // Dashboard logic
    $stmt = $pdo->query('SELECT COUNT(*) FROM primary_units');
    $primary_count = $stmt->fetchColumn();
    $stmt = $pdo->query('SELECT COUNT(*) FROM secondary_units');
    $unit_count = $stmt->fetchColumn();
    $stmt = $pdo->query('
        SELECT
            m.*,
            t.pmy_id AS primary_id,
            r.id AS secondary_id,
            r.pmy_id AS secondary_primary_id,
            CASE
                WHEN m.secondary_id IS NOT NULL THEN (
                    SELECT ra.value
                    FROM secondary_answers ra
                    WHERE ra.secondary_id = m.secondary_id AND ra.question_id = 1
                    LIMIT 1
                )
                ELSE NULL
            END AS secondary_answer_1
        FROM maintenance m
        LEFT JOIN secondary_units r ON m.secondary_id = r.id
        LEFT JOIN primary_units t ON m.pmy_id = t.id
        ORDER BY m.performed_at DESC
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
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['privilege'] = $user['privilege'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}

$smarty->assign('error', !empty($error) ? $error : '');
$smarty->display($theme_current . '/header.tpl');
$smarty->display($theme_current . '/login.tpl');
$smarty->display($theme_current . '/footer.tpl');
