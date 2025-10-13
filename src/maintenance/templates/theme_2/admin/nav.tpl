<nav class="navbar navbar-expand-lg navbar-admin mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_primary.php">Manage {$primary_label|escape} Units</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_secondary.php">Manage {$secondary_label|escape} Units</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="config.php">Site Config</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">User Dashboard</a>
                </li>
            </ul>

            <button class="btn btn-outline-light ms-2" onclick="toggleTheme()">Toggle Theme</button>
            <a class="btn btn-danger ms-2" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>
