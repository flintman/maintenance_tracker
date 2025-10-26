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

// GET: /api/index.php?action=maintenance_view&id=123 || /api/index.php?action=maintenance_view&unit_id=456
$id = $_GET['id'] ?? null;
$pmy_id = $_GET['pmy_id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM maintenance WHERE id = ?');
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
} elseif ($pmy_id) {
    $stmt = $pdo->prepare('SELECT * FROM maintenance WHERE pmy_id = ?');
    $stmt->execute([$pmy_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($result) {
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
} else {
    $stmt = $pdo->query('SELECT * FROM maintenance');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
