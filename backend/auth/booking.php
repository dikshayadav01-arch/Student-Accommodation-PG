<?php

session_start();

require_once __DIR__ . '/../config/db.php';

function bookingError(string $message, int $propertyId): never {
    header('Location: /property_details.php?id=' . $propertyId . '&booking_error=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /listings.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$propertyId = filter_var($_POST['property_id'] ?? null, FILTER_VALIDATE_INT);
$roomId = filter_var($_POST['room_id'] ?? null, FILTER_VALIDATE_INT);
$checkIn = $_POST['check_in'] ?? '';
$checkOut = $_POST['check_out'] ?? '';
$csrfToken = $_POST['csrf_token'] ?? '';

if (!$propertyId || !$roomId) {
    bookingError('Invalid property or room.', (int) ($propertyId ?: 0));
}

if (
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $csrfToken)
) {
    bookingError('Invalid request. Please try again.', $propertyId);
}

$datePattern = '/^\d{4}-\d{2}-\d{2}$/';

if (!preg_match($datePattern, $checkIn) || !preg_match($datePattern, $checkOut)) {
    bookingError('Please enter valid dates.', $propertyId);
}

$checkInDate = DateTime::createFromFormat('Y-m-d', $checkIn);
$checkOutDate = DateTime::createFromFormat('Y-m-d', $checkOut);

if (
    !$checkInDate ||
    !$checkOutDate ||
    $checkInDate->format('Y-m-d') !== $checkIn ||
    $checkOutDate->format('Y-m-d') !== $checkOut ||
    $checkOutDate <= $checkInDate
) {
    bookingError('Check-out must be later than check-in.', $propertyId);
}

try {
    $pdo->beginTransaction();

    // Lock the room while checking capacity.
    $stmt = $pdo->prepare("
        SELECT room_id, property_id, capacity, occupied, availability
        FROM rooms
        WHERE room_id = :room_id
          AND property_id = :property_id
        FOR UPDATE
    ");

    $stmt->execute([
        'room_id' => $roomId,
        'property_id' => $propertyId
    ]);

    $room = $stmt->fetch();

    if (!$room) {
        $pdo->rollBack();
        bookingError('Room not found.', $propertyId);
    }

    if (!$room['availability'] || $room['occupied'] >= $room['capacity']) {
        $pdo->rollBack();
        bookingError('This room is currently full or unavailable.', $propertyId);
    }

    // Save booking.
    $stmt = $pdo->prepare("
        INSERT INTO bookings (
            student_id,
            room_id,
            check_in,
            check_out,
            booking_status
        )
        VALUES (
            :student_id,
            :room_id,
            :check_in,
            :check_out,
            'Pending'
        )
    ");

    $stmt->execute([
        'student_id' => $_SESSION['user_id'],
        'room_id' => $roomId,
        'check_in' => $checkIn,
        'check_out' => $checkOut
    ]);

    // Reserve one bed for this pending request.
    $newOccupied = (int) $room['occupied'] + 1;
    $newAvailability = $newOccupied < (int) $room['capacity'];

    $stmt = $pdo->prepare("
        UPDATE rooms
        SET occupied = :occupied,
            availability = :availability
        WHERE room_id = :room_id
    ");

    $stmt->execute([
        'occupied' => $newOccupied,
        'availability' => $newAvailability,
        'room_id' => $roomId
    ]);

    $pdo->commit();

    header('Location: /dashboard.php?booking_success=1');
    exit;

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Booking error: ' . $e->getMessage());
    bookingError('Booking could not be completed. Please try again.', $propertyId);
}