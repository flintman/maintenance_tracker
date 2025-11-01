<?php
if (!defined('MAINTENANCE_TRACKER_INIT')) {
    http_response_code(403);
    exit('Direct access not permitted.');
}

// Helper function to sanitize and validate inputs
function cleanInput($data, $type = 'string')
{
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'string':
        default:
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
