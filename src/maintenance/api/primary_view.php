<?php
if (!isset($provided_key) || !isset($api_keys)) {
    echo json_encode(['error' => 'Unauthorized: missing API key']);
    exit;
} else {
    if (!in_array($provided_key, $api_keys ?? [])) {
        echo json_encode(['error' => 'Unauthorized: Invalid API key']);
        exit;
    }
}
// GET: /api/index.php?action=primary_view&id=123
$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM primary_units WHERE id = ?');
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
} else {
    $stmt = $pdo->query('SELECT * FROM primary_units');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
