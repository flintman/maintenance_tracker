<?php
define('MAINTENANCE_TRACKER_INIT', true);
require_once __DIR__ . '/../../common/db.php';
if (!file_exists(__DIR__ . '/../../common/db.php')) {
    die("Database configuration file not found: " . __DIR__ . '/../../common/db.php');
}

if (!isset($_SESSION['user_id']) || ($_SESSION['privilege'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$updatedVersion = '1.0.1';
// 1. Create new tables if not exist
$pdo->exec("
CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pmy_id INT,
    equipment_level INT DEFAULT 1,
    archived TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_level INT DEFAULT 1,
    label VARCHAR(100) NOT NULL,
    type ENUM('string','text','number','date','multi_choice') NOT NULL,
    options VARCHAR(255) DEFAULT NULL,
    position INT NOT NULL DEFAULT 0
);
CREATE TABLE IF NOT EXISTS answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    question_id INT NOT NULL,
    value TEXT,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);
");

// 2. Migrate units
// Map old unit IDs to new equipment IDs
$unit_id_map = [];

// Primary units
$stmt = $pdo->query("SELECT * FROM primary_units");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $insert = $pdo->prepare("INSERT INTO equipment (pmy_id, equipment_level, archived, created_at) VALUES (?, 1, ?, ?)");
    $insert->execute([$row['pmy_id'], $row['archived'], $row['created_at']]);
    $new_id = $pdo->lastInsertId();
    $unit_id_map['primary'][$row['id']] = $new_id;
}

// Secondary units
$stmt = $pdo->query("SELECT * FROM secondary_units");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $insert = $pdo->prepare("INSERT INTO equipment (pmy_id, equipment_level, archived, created_at) VALUES (?, 2, ?, ?)");
    $insert->execute([$row['pmy_id'], $row['archived'], $row['created_at']]);
    $new_id = $pdo->lastInsertId();
    $unit_id_map['secondary'][$row['id']] = $new_id;
}

// 3. Migrate questions
$question_id_map = [];

// Primary questions
$stmt = $pdo->query("SELECT * FROM primary_questions");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $insert = $pdo->prepare("INSERT INTO questions (equipment_level, label, type, options, position) VALUES (1, ?, ?, ?, ?)");
    $insert->execute([$row['label'], $row['type'], $row['options'], $row['position']]);
    $new_id = $pdo->lastInsertId();
    $question_id_map['primary'][$row['id']] = $new_id;
}

// Secondary questions
$stmt = $pdo->query("SELECT * FROM secondary_questions");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $insert = $pdo->prepare("INSERT INTO questions (equipment_level, label, type, options, position) VALUES (2, ?, ?, ?, ?)");
    $insert->execute([$row['label'], $row['type'], $row['options'], $row['position']]);
    $new_id = $pdo->lastInsertId();
    $question_id_map['secondary'][$row['id']] = $new_id;
}

// 4. Migrate answers
// Primary answers
$stmt = $pdo->query("SELECT * FROM primary_answers");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $new_unit_id = $unit_id_map['primary'][$row['primary_id']] ?? null;
    $new_question_id = $question_id_map['primary'][$row['question_id']] ?? null;
    if ($new_unit_id && $new_question_id) {
        $insert = $pdo->prepare("INSERT INTO answers (equipment_id, question_id, value) VALUES (?, ?, ?)");
        $insert->execute([$new_unit_id, $new_question_id, $row['value']]);
    }
}

// Secondary answers
$stmt = $pdo->query("SELECT * FROM secondary_answers");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $new_unit_id = $unit_id_map['secondary'][$row['secondary_id']] ?? null;
    $new_question_id = $question_id_map['secondary'][$row['question_id']] ?? null;
    if ($new_unit_id && $new_question_id) {
        $insert = $pdo->prepare("INSERT INTO answers (equipment_id, question_id, value) VALUES (?, ?, ?)");
        $insert->execute([$new_unit_id, $new_question_id, $row['value']]);
    }
}

// 5. Drop old tables
$pdo->exec("DROP TABLE IF EXISTS primary_answers");
$pdo->exec("DROP TABLE IF EXISTS secondary_answers");
$pdo->exec("DROP TABLE IF EXISTS primary_questions");
$pdo->exec("DROP TABLE IF EXISTS secondary_questions");
$pdo->exec("DROP TABLE IF EXISTS primary_units");
$pdo->exec("DROP TABLE IF EXISTS secondary_units");

// Update the version in admin_config table
// Check if the version row exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_config WHERE config_name = 'version'");
$stmt->execute();
$exists = $stmt->fetchColumn();

if ($exists) {
    // Update if exists
    $stmt = $pdo->prepare("UPDATE admin_config SET config_value = ? WHERE config_name = 'version'");
    $stmt->execute([$updatedVersion]);
} else {
    // Insert if not exists
    $stmt = $pdo->prepare("INSERT INTO admin_config (config_name, config_value) VALUES ('version', ?)");
    $stmt->execute([$updatedVersion]);
}

echo "Migration complete. All data has been merged into equipment, questions, and answers tables.\\n";
echo "Click here to <a href='../../index.php'>return to the application</a>.";

?>