<?php
require_once '../common.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['privilege'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}
include 'templates/header.php';

// Handle edit user
$edit_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = intval($_POST['id']);
    $username = cleanInput($_POST['username']);
    $email = cleanInput($_POST['email'], 'email');
    $privilege = $_POST['privilege'] ?? 'user';
    $password = $_POST['password'] ?? '';
    if ($password) {
        if (strlen($password) < 6) {
            $edit_msg = '<div class="alert alert-danger">Password must be at least 6 characters.</div>';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, privilege=?, password=? WHERE id=?');
            $stmt->execute([$username, $email, $privilege, $hash, $id]);
            $edit_msg = '<div class="alert alert-success">User updated (password changed)!</div>';
        }
    } else {
        $stmt = $pdo->prepare('UPDATE users SET username=?, email=?, privilege=? WHERE id=?');
        $stmt->execute([$username, $email, $privilege, $id]);
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
?>

<div class="container mt-5">
    <h2>Manage Users</h2>
    <?= $edit_msg . $delete_msg ?>
    <h3 class="mt-4">All Users</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Privilege</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <form method="post">
                <td><?= $user['id'] ?></td>
                <td><input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="form-control"></td>
                <td><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control"></td>
                <td>
                    <select name="privilege" class="form-control">
                        <option value="user" <?= $user['privilege'] === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $user['privilege'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <input type="password" name="password" class="form-control mb-1" placeholder="New password (optional)">
                    <button type="submit" name="edit_user" class="btn btn-primary btn-sm">Save</button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="if(confirm('Are you sure you want to delete this user?')) { this.form.submit(); }" name="delete_user">Delete</button>
                </td>
                </form>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include 'templates/footer.php'; ?>