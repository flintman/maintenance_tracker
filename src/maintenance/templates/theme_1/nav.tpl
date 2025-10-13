{* Navigation Template *}
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Maintenance Tracker</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                {if isset($session.user_id)}
                <li class="nav-item">
                    <a class="nav-link" href="primary.php">{$primary_label|escape} Units</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="secondary.php">{$secondary_label|escape} Units</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="user.php">User Info</a>
                </li>
                {if $session.privilege == 'admin'}
                <li class="nav-item">
                    <a class="nav-link" href="admin/index.php">Admin Dashboard</a>
                </li>
                {/if}
                {/if}
            </ul>
            <button class="btn btn-outline-secondary theme-toggle me-2" onclick="toggleTheme()">
                <span id="theme-label">Toggle Theme</span>
            </button>
            {if isset($session.user_id)}
              <a class="btn btn-danger ms-2" href="logout.php">Logout</a>
            {/if}
        </div>
    </div>
</nav>
<script>
    function updateThemeLabel() {
        const theme = document.documentElement.getAttribute('data-bs-theme');
        document.getElementById('theme-label').textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
    }
    document.addEventListener('DOMContentLoaded', updateThemeLabel);
    document.addEventListener('DOMContentLoaded', function() {
        document.documentElement.addEventListener('change', updateThemeLabel);
    });
    function toggleTheme() {
        let theme = document.documentElement.getAttribute('data-bs-theme');
        theme = theme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', theme);
        document.cookie = 'theme=' + theme + ';path=/';
        updateThemeLabel();
    }
</script>
