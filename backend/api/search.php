<?php

declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

try {

    $city = trim($_GET['city'] ?? '');
    $budget = trim($_GET['budget'] ?? '');

    if ($budget !== '' && (!is_numeric($budget) || (float)$budget < 0)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid budget value.'
        ]);
        exit;
    }

    $sql = "SELECT p.property_id, p.property_name, p.description,
                   p.property_type, p.address, p.city, p.rent,
                   p.gender_preference, p.amenities, p.image_url,
                   CASE WHEN f.property_id IS NOT NULL THEN TRUE
                        ELSE FALSE END AS is_favorite
            FROM properties p
            LEFT JOIN favorites f
                ON f.property_id = p.property_id
                AND f.student_id = :student_id
            WHERE 1=1";

    $params = [
        'student_id' => $_SESSION['user_id'] ?? 0
    ];

    if ($city !== '') {
        $sql .= " AND p.city ILIKE :city";
        $params['city'] = '%' . $city . '%';
    }

    if ($budget !== '') {
        $sql .= " AND p.rent <= :budget";
        $params['budget'] = (float)$budget;
    }

    $sql .= " ORDER BY p.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $properties = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'properties' => $properties
    ]);

} catch (Throwable $e) {

    error_log('Accommodation search API error: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to retrieve accommodations.'
    ]);
}