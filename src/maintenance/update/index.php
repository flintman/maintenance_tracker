<?php
if (!isset($_SESSION['user_id']) || ($_SESSION['privilege'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get the current version from the query parameter
$fromVersion = isset($_GET['from_version']) ? $_GET['from_version'] : null;
if (!$fromVersion) {
    die('No version specified.');
}

$updateDir = __DIR__; // Current directory: update/
$folders = array_filter(glob($updateDir . '/*', GLOB_ONLYDIR), function($dir) {
    // Only keep folders with version-like names (e.g., 1.0.1)
    return preg_match('/\d+\.\d+\.\d+$/', basename($dir));
});

// Sort folders by version
usort($folders, function($a, $b) {
    return version_compare(basename($a), basename($b));
});

$updatedVersion = $fromVersion;
$startUpdating = false;

foreach ($folders as $folder) {
    $version = basename($folder);

    // Start updating from the next version after $fromVersion
    if (!$startUpdating && version_compare($version, $fromVersion, '>')) {
        $startUpdating = true;
    }

    if ($startUpdating) {
        $indexFile = $folder . '/index.php';
        if (file_exists($indexFile)) {
            include $indexFile;
            $updatedVersion = $version;
        }
    }
}

?>