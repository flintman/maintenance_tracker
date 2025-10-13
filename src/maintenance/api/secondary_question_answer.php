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
// GET: /api/index.php?action=secondary_question_answer&id=123
$id = $_GET['id'] ?? null;

if ($id) {
    // Prepare statement to fetch all questions and their answers for the given secondary_id
    $stmt = $pdo->prepare('
        SELECT q.id as question_id, q.label as question, a.value as answer
        FROM secondary_questions q
        LEFT JOIN secondary_answers a ON q.id = a.question_id AND a.secondary_id = ?
        ORDER BY q.position ASC
    ');
    $stmt->execute([$id]);
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
