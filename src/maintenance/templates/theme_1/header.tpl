{* Header Template *}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/css/theme.bootstrap_4.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        $(function() {
            $(".tablesorter").tablesorter({
                theme: 'bootstrap',
            });
        });
    </script>

    <style>
        .navbar {
            background-color: var(--bs-body-bg, #343a40);
        }
        .navbar a, .navbar .theme-toggle {
            color: var(--bs-body-color, #fff) !important;
        }
    </style>
    <style>
        [data-bs-theme="dark"] .modern-table thead,
        [data-bs-theme="dark"] .modern-table thead tr,
        [data-bs-theme="dark"] .modern-table th {
            background-color: #232526 !important;
            color: #00bcd4 !important;
            border-color: #222 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.18);
            font-weight: 600;
            letter-spacing: 0.03em;
        }
        [data-bs-theme="dark"] .modern-table td {
            background-color: #181a1b !important;
            color: #e0e0e0 !important;
            border-color: #222 !important;
        }
        [data-bs-theme="light"] .modern-table thead,
        [data-bs-theme="light"] .modern-table thead tr,
        [data-bs-theme="light"] .modern-table th {
            background-color: #e3e6ea !important;
            color: #1565c0 !important;
            border-color: #b0bec5 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            font-weight: 600;
            letter-spacing: 0.03em;
        }
        [data-bs-theme="light"] .modern-table td {
            background-color: #fff !important;
            color: #232526 !important;
            border-color: #b0bec5 !important;
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
{include file="theme_1/nav.tpl"}
<div class="container py-4">
