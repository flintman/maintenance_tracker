<?php
require_once '../common.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['privilege'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}
include 'templates/header.php';

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
$stmt = $pdo->query('SELECT COUNT(*) FROM trailers');
$trailer_count = $stmt->fetchColumn();
$stmt = $pdo->query('SELECT COUNT(*) FROM refrigeration');
$unit_count = $stmt->fetchColumn();
$stmt = $pdo->query('SELECT COUNT(*) FROM users');
$user_count = $stmt->fetchColumn();
$stmt = $pdo->query('SELECT m.*, COALESCE(t.trl_id) AS equipment FROM maintenance m LEFT JOIN refrigeration r ON m.refrigeration_id = r.id LEFT JOIN trailers t ON m.trl_id = t.id ORDER BY m.performed_at DESC LIMIT 5');
$latest_maintenance = $stmt->fetchAll();
?>
<div class="container mt-5">
    <h2>Admin Dashboard</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <div class="col d-flex">
            <div class="card admin-card mb-3 w-100 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Trailers</h5>
                    <p class="card-text display-6 mb-0"><?= $trailer_count ?></p>
                </div>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card admin-card mb-3 w-100 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Refrigeration Units</h5>
                    <p class="card-text display-6 mb-0"><?= $unit_count ?></p>
                </div>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card admin-card mb-3 w-100 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Users</h5>
                    <p class="card-text display-6 mb-0"><?= $user_count ?></p>
                </div>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card admin-card mb-3 w-100 h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Latest Maintenance</h5>
                    <ul class="list-group list-group-flush w-100">
                        <?php foreach ($latest_maintenance as $m): ?>
                        <li class="list-group-item">
                            <a href="../view_maintenance.php?id=<?= $m['id'] ?>&type=<?= $m['refrigeration_id'] ? 'refrigeration' : 'trailer' ?>" class="text-decoration-none">
                                <strong><?= htmlspecialchars($m['type_of_service']) ?></strong> - <?= htmlspecialchars($m['equipment']) ?> <br>
                                <small><?= htmlspecialchars($m['performed_at']) ?> by <?= htmlspecialchars($m['performed_by']) ?></small>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card admin-card">
                <div class="card-body">
                    <h5 class="card-title">Quick Add User</h5>
                    <?= $user_add_msg ?>
                    <form method="post">
                        <div class="mb-2">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Confirm Password</label>
                            <input type="password" name="password2" class="form-control" required>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'templates/footer.php'; ?>
