<nav class="navbar navbar-expand-lg navbar-main mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Maintenance Tracker</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                {if isset($session.user_id)}
                    <li class="nav-item"><a class="nav-link" href="refrigeration.php">Refrigeration</a></li>
                    <li class="nav-item"><a class="nav-link" href="trailer.php">Trailer</a></li>
                    <li class="nav-item"><a class="nav-link" href="user.php">Users</a></li>
                {/if}
                {if $session.privilege == 'admin'}
                    <li class="nav-item">
                        <a class="nav-link" href="admin/index.php">Admin Dashboard</a>
                    </li>
                {/if}
            </ul>
            <button class="btn btn-outline-light ms-2" onclick="toggleTheme()">Toggle Theme</button>
            <a class="btn btn-danger ms-2" href="logout.php">Logout</a>
        </div>
    </div>
</nav>
