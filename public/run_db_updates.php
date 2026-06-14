<?php
header('Content-Type: text/plain');

// Security check: only allow if token matches
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
    
    // Split key/value
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        // Strip quotes
        $value = trim($value, " \t\n\r\0\x0B'\"");
        $env[$key] = $value;
    }
}

$host = $env['database.default.hostname'] ?? 'db';
$dbName = $env['database.default.database'] ?? 'csrm_db';
$user = $env['database.default.username'] ?? 'csrm_user';
$pass = $env['database.default.password'] ?? 'csrm_password';

echo "Connecting to mysql:host=$host;dbname=$dbName as $user...\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully.\n\n";
    
    $executeSqlFile = function($filename) use ($pdo) {
        $filePath = __DIR__ . '/../' . $filename;
        if (!file_exists($filePath)) {
            echo "File $filename not found.\n";
            return;
        }
        echo "Executing $filename...\n";
        $sql = file_get_contents($filePath);
        
        // Remove comments
        $sql = preg_replace('/^\s*--.*\n/m', '', $sql);
        $sql = preg_replace('/^\s*#.*\n/m', '', $sql);
        
        // Split by semicolon
        $queries = explode(';', $sql);
        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;
            
            try {
                $pdo->exec($query);
                $cleanQuery = str_replace(array("\r", "\n"), ' ', substr($query, 0, 70));
                echo "SUCCESS: " . $cleanQuery . "...\n";
            } catch (Exception $e) {
                // If it is a duplicate column or key error, we can display success as it means it's already updated
                if (strpos($e->getMessage(), 'Duplicate column name') !== false || 
                    strpos($e->getMessage(), 'already exists') !== false ||
                    strpos($e->getMessage(), 'Duplicate key name') !== false) {
                    echo "ALREADY APPLIED: " . $e->getMessage() . "\n";
                } else {
                    echo "ERROR executing query: " . $query . "\n";
                    echo "Reason: " . $e->getMessage() . "\n";
                }
            }
        }
        echo "Finished $filename.\n\n";
    };

    $executeSqlFile('update_schema.sql');
    $executeSqlFile('update_workflow.sql');

} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
