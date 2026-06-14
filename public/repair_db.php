<?php
header('Content-Type: text/plain');

if (($_GET['token'] ?? '') !== 'csrm_deploy_2026') {
    die("Access denied");
}

$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die("env file not found");
}

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $env[trim($parts[0])] = trim(trim($parts[1]), " \t\n\r\0\x0B'\"");
    }
}

$host = $env['database.default.hostname'] ?? 'db';
$dbName = $env['database.default.database'] ?? 'csrm_db';
$user = $env['database.default.username'] ?? 'csrm_user';
$pass = $env['database.default.password'] ?? 'csrm_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully to database.\n\n";

    // Check columns on papers table
    $stmt = $pdo->query("DESCRIBE `papers`");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Current papers columns: " . implode(', ', $columns) . "\n\n";

    $addColIfMissing = function($colName, $definition) use ($pdo, $columns) {
        if (!in_array($colName, $columns)) {
            echo "Column '$colName' is missing. Adding it...\n";
            try {
                $pdo->exec("ALTER TABLE `papers` ADD COLUMN `$colName` $definition");
                echo "SUCCESS: Added column '$colName'.\n";
            } catch (Exception $e) {
                echo "ERROR adding column '$colName': " . $e->getMessage() . "\n";
            }
        } else {
            echo "Column '$colName' already exists.\n";
        }
    };

    $addColIfMissing('plagiarism_status', "ENUM('pending', 'checked', 'failed') NOT NULL DEFAULT 'pending'");
    $addColIfMissing('similarity_percent', "INT DEFAULT NULL");
    $addColIfMissing('plagiarism_report_url', "VARCHAR(255) DEFAULT NULL");
    $addColIfMissing('presentation_score', "DECIMAL(5,2) DEFAULT NULL");
    $addColIfMissing('revision_deadline', "DATETIME DEFAULT NULL");

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
