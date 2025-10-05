<?php
require_once 'common/common.php';

if (isset($_SESSION['user_id'])) {
    // Dashboard logic
    $stmt = $pdo->query('SELECT COUNT(*) FROM trailers');
    $trailer_count = $stmt->fetchColumn();
    $stmt = $pdo->query('SELECT COUNT(*) FROM refrigeration');
    $unit_count = $stmt->fetchColumn();
    $stmt = $pdo->query('
        SELECT
            m.*,
            t.trl_id AS trailer_id,
            r.id AS refrigeration_id,
            r.trl_id AS refrigeration_trailer_id,
            CASE
                WHEN m.refrigeration_id IS NOT NULL THEN (
                    SELECT ra.value
                    FROM refrigeration_answers ra
                    WHERE ra.refrigeration_id = m.refrigeration_id AND ra.question_id = 1
                    LIMIT 1
                )
                ELSE NULL
            END AS refrigeration_answer_1
        FROM maintenance m
        LEFT JOIN refrigeration r ON m.refrigeration_id = r.id
        LEFT JOIN trailers t ON m.trl_id = t.id
        ORDER BY m.performed_at DESC
        LIMIT 5
    ');
    $latest_maintenance = $stmt->fetchAll();
    $smarty->assign('trailer_count', $trailer_count);
    $smarty->assign('unit_count', $unit_count);
    $smarty->assign('latest_maintenance', $latest_maintenance);
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
