<nav class="navbar navbar-expand-lg navbar-admin mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbarNav" aria-controls="adminNavbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_refrigeration.php">Manage Refrigeration</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_trailer.php">Manage Trailers</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <button class="btn btn-outline-secondary theme-toggle me-2" onclick="toggleTheme()">
                        <span id="theme-label">Toggle Theme</span>
                    </button>
                </li>
                <li class="nav-item"><a class="nav-link text-light" href="../index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<script>
    function updateThemeLabel() {
        const theme = document.documentElement.getAttribute('data-bs-theme');
        document.getElementById('theme-label').textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
    }
    document.addEventListener('DOMContentLoaded', updateThemeLabel);
    function toggleTheme() {
        let theme = document.documentElement.getAttribute('data-bs-theme');
        theme = theme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', theme);
        document.cookie = 'theme=' + theme + ';path=/';
        updateThemeLabel();
    }
</script>
<div class="container">