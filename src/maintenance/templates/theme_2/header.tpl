<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title|default:'Dashboard'}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bs-body-bg, #f8fafc);
            color: var(--bs-body-color, #222);
            transition: background 0.3s, color 0.3s;
        }
        .navbar-main {
            background: linear-gradient(90deg, #232526 0%, #00bcd4 100%);
            box-shadow: 0 2px 16px rgba(0,0,0,0.12);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 2px;
            color: #fff !important;
        }
        .modern-card {
            border-radius: 1.5rem;
            box-shadow: 0 6px 32px rgba(0,0,0,0.10);
            background: #fff;
            border: none;
            transition: box-shadow 0.2s;
        }
        .modern-card:hover {
            box-shadow: 0 12px 48px rgba(0,0,0,0.18);
        }
        .modern-btn {
            border-radius: 2rem;
            font-weight: 600;
            padding: 0.5rem 2rem;
            transition: background 0.2s, color 0.2s;
        }
        .modern-btn-primary {
            background: linear-gradient(90deg, #232526 0%, #00bcd4 100%);
            color: #fff;
            border: none;
        }
        .modern-btn-primary:hover {
            background: linear-gradient(90deg, #00bcd4 0%, #232526 100%);
            color: #fff;
        }
        .modern-table th, .modern-table td {
            vertical-align: middle;
        }
        .modern-badge {
            border-radius: 1rem;
            font-size: 0.95em;
            padding: 0.4em 1em;
            background: #232526;
            color: #fff;
        }
        /* Dark theme */
        [data-bs-theme="dark"] body {
            background: #181a1b;
            color: #e0e0e0;
        }
        [data-bs-theme="dark"] .navbar-main {
            background: linear-gradient(90deg, #181a1b 0%, #232526 100%);
        }
        [data-bs-theme="dark"] .modern-card {
            background: #232526;
            color: #fff;
        }
        [data-bs-theme="dark"] .modern-table {
            background: #232526;
            color: #fff;
        }
        [data-bs-theme="dark"] .modern-badge {
            background: #00bcd4;
            color: #232526;
        }
    </style>
    <script>
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
{include file="theme_2/nav.tpl"}
