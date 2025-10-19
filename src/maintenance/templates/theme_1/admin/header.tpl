<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$ADMIN_DASHBOARD_TITLE|escape}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../templates/theme_1/css/admin.css">
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
{include file="theme_1/admin/nav.tpl"}
