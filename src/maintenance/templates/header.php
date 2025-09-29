<?php
$theme = $_COOKIE['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= htmlspecialchars($theme) ?>">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
    function toggleTheme() {
        let theme = document.documentElement.getAttribute('data-bs-theme');
        theme = theme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', theme);
        document.cookie = 'theme=' + theme + ';path=/';
    }
    </script>
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<div class="container py-4">
