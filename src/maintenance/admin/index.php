<?php
require_once '../common.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['privilege'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}
include 'templates/header.php';
?>
<div class="container mt-5">
    <h2>Admin Dashboard</h2>
    <div class="alert alert-info">Welcome, Admin!</div>
    <ul class="list-group mb-4">

    </ul>
</div>
<?php include 'templates/footer.php'; ?>
