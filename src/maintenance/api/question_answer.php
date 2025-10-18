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
// GET: /api/index.php?action=question_answer&id=123
$id = $_GET['id'] ?? null;

if ($id) {
    // Prepare statement to fetch all questions and their answers for the given primary_id
    // determine equipment level so we can return only relevant questions
    $levelStmt = $pdo->prepare('SELECT equipment_level FROM equipment WHERE id = ? LIMIT 1');
    $levelStmt->execute([$id]);
    $levelRow = $levelStmt->fetch(PDO::FETCH_ASSOC);
    $equipment_level = $levelRow ? (int)$levelRow['equipment_level'] : 1;

    // Fetch questions for this equipment level (or level 0 as wildcard) and left-join answers for this equipment id
    $stmt = $pdo->prepare(
        'SELECT q.id as question_id, q.label as question, a.value as answer
         FROM questions q
         LEFT JOIN answers a ON q.id = a.question_id AND a.equipment_id = ?
         WHERE (q.equipment_level = ? OR q.equipment_level = 0)
         ORDER BY q.position ASC'
    );
    $stmt->execute([$id, $equipment_level]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        $response = [];
        foreach ($results as $row) {
            $response[$row['question']] = $row['answer'];
        }
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID parameter is required']);
}
