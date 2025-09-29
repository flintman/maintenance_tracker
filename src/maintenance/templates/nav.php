<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Maintenance Tracker</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="equipment.php">Trailers</a></li>
          <li class="nav-item"><a class="nav-link" href="refrigeration.php">Refrigeration</a></li>
          <li class="nav-item"><a class="nav-link" href="user.php">User Info</a></li>
        <?php endif; ?>
      </ul>
      <button class="btn btn-outline-light" onclick="toggleTheme()">Toggle Theme</button>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a class="btn btn-danger ms-2" href="logout.php">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
