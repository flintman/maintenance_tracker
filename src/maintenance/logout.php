<?php
session_start();
session_destroy();
// Remove remember_me cookie and token
if (isset($_COOKIE['remember_me'])) {
	require_once 'common/common.php';
	$stmt = $pdo->prepare('UPDATE users SET remember_token = NULL WHERE remember_token = ?');
	$stmt->execute([$_COOKIE['remember_me']]);
	setcookie('remember_me', '', time() - 3600, '/', '', true, true);
}
header('Location: index.php');
exit;
