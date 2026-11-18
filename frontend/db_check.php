<?php
require_once __DIR__ . '/../backend/config/db.php';

echo "Connected database: " . $pdo->query("SELECT current_database()")->fetchColumn();
echo "<hr>";

$stmt = $pdo->query("SELECT user_id, full_name, email, role FROM users ORDER BY user_id DESC");

while ($row = $stmt->fetch()) {
    echo htmlspecialchars($row['user_id']) . " | ";
    echo htmlspecialchars($row['full_name']) . " | ";
    echo htmlspecialchars($row['email']) . " | ";
    echo htmlspecialchars($row['role']) . "<br>";
}