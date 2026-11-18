<?php

session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

function respond(int $status, array $data): never {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['success' => false, 'message' => 'POST method required.']);
}

if (!isset($_SESSION['user_id'])) {
    respond(401, ['success' => false, 'message' => 'Please log in first.']);
}

$csrfToken = $_POST['csrf_token'] ?? '';

if (
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $csrfToken)
) {
    respond(403, ['success' => false, 'message' => 'Invalid request.']);
}

$action = $_POST['action'] ?? '';
$propertyId = filter_var($_POST['property_id'] ?? null, FILTER_VALIDATE_INT);

if (!$propertyId || $propertyId < 1) {
    respond(400, ['success' => false, 'message' => 'Invalid property ID.']);
}

if (!in_array($action, ['add', 'remove'], true)) {
    respond(400, ['success' => false, 'message' => 'Invalid action.']);
}

$studentId = (int) $_SESSION['user_id'];

try {

    // Confirm the property exists.
    $check = $pdo->prepare("
        SELECT property_id
        FROM properties
        WHERE property_id = :property_id
    ");

    $check->execute(['property_id' => $propertyId]);

    if (!$check->fetch()) {
        respond(404, ['success' => false, 'message' => 'Property not found.']);
    }

    if ($action === 'add') {

        $stmt = $pdo->prepare("
            INSERT INTO favorites (student_id, property_id)
            VALUES (:student_id, :property_id)
            ON CONFLICT (student_id, property_id) DO NOTHING
        ");

        $stmt->execute([
            'student_id' => $studentId,
            'property_id' => $propertyId
        ]);

        respond(200, [
            'success' => true,
            'message' => 'Property saved to favorites.'
        ]);

    }

    if ($action === 'remove') {

        $stmt = $pdo->prepare("
            DELETE FROM favorites
            WHERE student_id = :student_id
              AND property_id = :property_id
        ");

        $stmt->execute([
            'student_id' => $studentId,
            'property_id' => $propertyId
        ]);

        respond(200, [
            'success' => true,
            'message' => 'Property removed from favorites.'
        ]);
    }

} catch (PDOException $e) {

    error_log('Favorite API error: ' . $e->getMessage());

    respond(500, [
        'success' => false,
        'message' => 'Something went wrong. Please try again.'
    ]);
}