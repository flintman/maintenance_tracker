<nav class="navbar navbar-expand-lg navbar-main mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">{$APP_TITLE|escape}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                {if isset($session.user_id)}
                    <li class="nav-item"><a class="nav-link" href="units.php">{$primary_label|escape} {$UNITS_TITLE|escape}</a></li>
                    <li class="nav-item"><a class="nav-link" href="units.php?secondary=0">{$secondary_label|escape} {$UNITS_TITLE|escape}</a></li>
                    <li class="nav-item"><a class="nav-link" href="user.php">{$NAV_USER_INFO|escape}</a></li>
                    {if $session.privilege == 'admin'}
                        <li class="nav-item">
                            <a class="nav-link" href="admin/index.php">{$ADMIN_DASHBOARD_TITLE|escape}</a>
                        </li>
                    {/if}
                {/if}
            </ul>
            <button class="btn btn-outline-light ms-2" onclick="toggleTheme()">{$TOGGLE_THEME_TITLE|escape}</button>

            {if isset($session.user_id)}
                <a class="btn btn-danger ms-2" href="logout.php">{$LOGOUT_LABEL|escape}</a>
            {/if}
        </div>
    </div>
</nav>
