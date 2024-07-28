<?php

header('Content-Type: application/json');

// Get database connection parameters from environment variables
$dbHost = getenv('DB_HOST');
$dbPort = getenv('DB_PORT');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Execute a simple query to check connection
    $query = $pdo->query("SELECT 'Database connection is working!' AS message");
    $result = $query->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['message' => $result['message']]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
