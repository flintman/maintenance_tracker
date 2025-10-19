<nav class="navbar navbar-expand-lg navbar-admin mb-4">
    <div class="container-fluid">
    <a class="navbar-brand" href="index.php">{$ADMIN_PANEL_TITLE|escape}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">{$ADMIN_MANAGE_USERS_TITLE|escape}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_units.php">{$ADMIN_MANAGE_LABEL|escape} {$primary_label|escape} {$UNITS_TITLE|escape}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_units.php?secondary=0">{$ADMIN_MANAGE_LABEL|escape} {$secondary_label|escape} {$UNITS_TITLE|escape}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_message.php">{$ADMIN_MANAGE_MESSAGES_TITLE|escape}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="config.php">{$ADMIN_SITE_CONFIG|escape}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">{$ADMIN_USER_DASHBOARD|escape}</a>
                </li>
            </ul>

            <button class="btn btn-outline-light ms-2" onclick="toggleTheme()">{$TOGGLE_THEME_TITLE|escape}</button>
            <a class="btn btn-danger ms-2" href="../logout.php">{$LOGOUT_LABEL|escape}</a>
        </div>
    </div>
</nav>
