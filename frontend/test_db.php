<?php

require_once __DIR__ . '/../backend/config/db.php';

$stmt = $pdo->query("SELECT current_database()");

$result = $stmt->fetch();

echo "<h1>Database connection successful!</h1>";
echo "<p>Database: " . htmlspecialchars($result['current_database']) . "</p>";