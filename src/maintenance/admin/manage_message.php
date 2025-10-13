<?php
require_once '../common/common.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['privilege'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$success_msg = '';
$error_msg = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Add new message
    if (isset($_POST['add_message'])) {
        $message = trim($_POST['message'] ?? '');
        if (!empty($message)) {
            try {
                $stmt = $pdo->prepare('INSERT INTO admin_message (message, active) VALUES (?, NULL)');
                $stmt->execute([$message]);
                $success_msg = 'Message added successfully!';
            } catch (PDOException $e) {
                $error_msg = 'Error adding message: ' . $e->getMessage();
            }
        } else {
            $error_msg = 'Message cannot be empty.';
        }
    }

    // Edit existing message
    elseif (isset($_POST['edit_message'])) {
        $id = (int)($_POST['message_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');
        if ($id > 0 && !empty($message)) {
            try {
                $stmt = $pdo->prepare('UPDATE admin_message SET message = ? WHERE id = ?');
                $stmt->execute([$message, $id]);
                $success_msg = 'Message updated successfully!';
            } catch (PDOException $e) {
                $error_msg = 'Error updating message: ' . $e->getMessage();
            }
        } else {
            $error_msg = 'Invalid message ID or empty message.';
        }
    }

    // Activate message (deactivate all others first)
    elseif (isset($_POST['activate_message'])) {
        $id = (int)($_POST['message_id'] ?? 0);
        if ($id > 0) {
            try {
                $pdo->beginTransaction();

                // Deactivate all messages first by setting active to NULL
                $stmt = $pdo->prepare('UPDATE admin_message SET active = NULL');
                $stmt->execute();

                // Activate the selected message
                $stmt = $pdo->prepare('UPDATE admin_message SET active = 1 WHERE id = ?');
                $stmt->execute([$id]);

                $pdo->commit();
                $success_msg = 'Message activated successfully!';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error_msg = 'Error activating message: ' . $e->getMessage();
            }
        } else {
            $error_msg = 'Invalid message ID.';
        }
    }

    // Delete message
    elseif (isset($_POST['delete_message'])) {
        $id = (int)($_POST['message_id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare('DELETE FROM admin_message WHERE id = ?');
                $stmt->execute([$id]);
                $success_msg = 'Message deleted successfully!';
            } catch (PDOException $e) {
                $error_msg = 'Error deleting message: ' . $e->getMessage();
            }
        } else {
            $error_msg = 'Invalid message ID.';
        }
    }
}

// Fetch all messages - active first, then by date (newest first)
$stmt = $pdo->query('
    SELECT id, message, active, performed_at
    FROM admin_message
    ORDER BY (active IS NOT NULL AND active = 1) DESC, performed_at DESC
');
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$smarty->assign('messages', $messages);
$smarty->assign('success_msg', $success_msg);
$smarty->assign('error_msg', $error_msg);

$smarty->display($theme_current . '/admin/header.tpl');
$smarty->display($theme_current . '/admin/manage_message.tpl');
$smarty->display($theme_current . '/admin/footer.tpl');
?>