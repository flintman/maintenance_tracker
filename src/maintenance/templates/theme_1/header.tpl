{* Header Template *}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$APP_TITLE|escape}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
    <link rel="stylesheet" href="templates/theme_1/css/common.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        // Localized labels for theme toggle
        var LIGHT_MODE_LABEL = '{$LIGHT_MODE_LABEL|escape}';
        var DARK_MODE_LABEL = '{$DARK_MODE_LABEL|escape}';

        function getThemeCookie() {
            const match = document.cookie.match(/(?:^|; )theme=([^;]*)/);
            return match ? decodeURIComponent(match[1]) : null;
        }
        function setTheme(theme) {
            document.documentElement.setAttribute('data-bs-theme', theme);
            document.cookie = 'theme=' + theme + ';path=/;max-age=' + (60*60*24*30);
        }
        document.addEventListener('DOMContentLoaded', () => {
            let theme = getThemeCookie();
            if (!theme) {
                // Default to light if not set
                theme = 'light';
                setTheme(theme);
            } else {
                setTheme(theme);
            }
        });
        function toggleTheme() {
            let theme = document.documentElement.getAttribute('data-bs-theme');
            theme = theme === 'dark' ? 'light' : 'dark';
            setTheme(theme);
        }
    </script>

</head>
<body>
{if $update_warning|default:''|strip ne ''}
    <div style="color:red;font-weight:bold;text-align:center;">{$update_warning|escape}</div>
{/if}
{include file="theme_1/nav.tpl"}
<div class="container py-4">
