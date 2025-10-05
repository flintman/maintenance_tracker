<?php
$theme = $_COOKIE['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f8;
        }
        .navbar-admin {
            background: linear-gradient(90deg, #1976d2 0%, #00bcd4 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .navbar-brand {
            font-weight: bold;
            letter-spacing: 2px;
            color: #fff !important;
        }
        .admin-card {
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            background: #fff;
        }
        .list-group-item {
            background: #fff;
            color: #1976d2;
            border: none;
            font-weight: 500;
        }
        .list-group-item a {
            color: #1976d2;
            text-decoration: none;
        }
        .list-group-item a:hover {
            color: #00bcd4;
            text-decoration: underline;
        }
        /* Dark theme styles */
        [data-bs-theme="dark"] body {
            background: #181a1b;
        }
        [data-bs-theme="dark"] .navbar-admin {
            background: linear-gradient(90deg, #232526 0%, #414345 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.32);
        }
        [data-bs-theme="dark"] .navbar-brand {
            color: #00bcd4 !important;
        }
        [data-bs-theme="dark"] .admin-card {
            background: #232526;
            color: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.32);
        }
        [data-bs-theme="dark"] .list-group-item {
            background: #232526;
            color: #00bcd4;
        }
        [data-bs-theme="dark"] .list-group-item a {
            color: #00bcd4;
        }
        [data-bs-theme="dark"] .list-group-item a:hover {
            color: #fff;
        }
    </style>
    <script>
        // Restore theme from cookie
        document.addEventListener('DOMContentLoaded', () => {
            const theme = document.cookie.split('; ').find(row => row.startsWith('theme='))?.split('=')[1];
            if (theme) {
                document.documentElement.setAttribute('data-bs-theme', theme);
            }
        });
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
