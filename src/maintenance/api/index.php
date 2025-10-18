<?php
header('Content-Type: application/json');
require_once '../common/common.php';

// --- API Key Authorization ---
$provided_key = $_SERVER['HTTP_X_API_KEY'] ?? ($_GET['api_key'] ?? '');
// Check if provided_key is in $api_keys array
if (!in_array($provided_key, $api_keys ?? [])) {
    echo json_encode(['error' => 'Unauthorized: Invalid or missing API key']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'maintenance_view':
        require 'maintenance_view.php';
        break;
    case 'view':
        require 'view.php';
        break;
    case 'question_answer':
        require 'question_answer.php';
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}
