<?php
require_once 'common.php';
if (isset($_SESSION['user_id'])) {
    // Dashboard logic
    // Count trailers
    $stmt = $pdo->query('SELECT COUNT(*) FROM trailers');
    $trailer_count = $stmt->fetchColumn();
    // Count refrigeration units
    $stmt = $pdo->query('SELECT COUNT(*) FROM refrigeration');
    $unit_count = $stmt->fetchColumn();
    // Latest maintenance tickets
    $stmt = $pdo->query('SELECT m.*, COALESCE(r.model, t.trl_id) AS equipment FROM maintenance m LEFT JOIN refrigeration r ON m.refrigeration_id = r.id LEFT JOIN trailers t ON m.trl_id = t.id ORDER BY m.performed_at DESC LIMIT 5');
    $latest_maintenance = $stmt->fetchAll();
    include 'templates/header.php';
?>
<div class="container mt-5">
    <h2 class="mb-4">Dashboard</h2>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Trailers</h5>
                    <p class="card-text display-4"><?= $trailer_count ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Refrigeration Units</h5>
                    <p class="card-text display-4"><?= $unit_count ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Latest Maintenance</h5>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($latest_maintenance as $m): ?>
                        <li class="list-group-item bg-info text-white">
                            <a href="view_maintenance.php?id=<?= $m['id'] ?>&type=<?= $m['refrigeration_id'] ? 'refrigeration' : 'trailer' ?>" class="text-white text-decoration-none">
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
</div>
<?php
    include 'templates/footer.php';
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
include 'templates/header.php';
?>
<h2>Login</h2>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
<?php include 'templates/footer.php'; ?>
