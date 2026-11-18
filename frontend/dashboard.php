<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../backend/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$pageTitle = 'Student Dashboard | StayNest';

$userId = (int) $_SESSION['user_id'];
$fullName = $_SESSION['full_name'] ?? 'Student';

// Count bookings
$bookingStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM bookings WHERE student_id = :student_id"
);
$bookingStmt->execute(['student_id' => $userId]);
$bookingCount = (int) $bookingStmt->fetchColumn();

// Count favorites
$favoriteStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM favorites WHERE student_id = :student_id"
);
$favoriteStmt->execute(['student_id' => $userId]);
$favoriteCount = (int) $favoriteStmt->fetchColumn();

// Fetch recent bookings
$recentStmt = $pdo->prepare(
    "SELECT b.booking_id, b.check_in, b.check_out,
            b.booking_status, p.property_name, p.city,
            r.room_type
     FROM bookings b
     JOIN rooms r ON b.room_id = r.room_id
     JOIN properties p ON r.property_id = p.property_id
     WHERE b.student_id = :student_id
     ORDER BY b.created_at DESC
     LIMIT 5"
);

$recentStmt->execute(['student_id' => $userId]);
$recentBookings = $recentStmt->fetchAll();

require_once __DIR__ . '/../backend/includes/header.php';
?>

<div class="container mt-5 mb-5">

    <div class="mb-4">
        <h1>Welcome, <?= htmlspecialchars($fullName) ?>!</h1>
        <p class="text-muted">
            Manage your accommodation bookings and saved properties.
        </p>
    </div>

    <?php if (isset($_GET['booking_success'])): ?>
        <div class="alert alert-success">
            Your booking request has been submitted successfully!
        </div>
    <?php endif; ?>

    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Total Bookings</h5>
                    <h2 class="text-primary">
                        <?= $bookingCount ?>
                    </h2>
                    <a href="#recent-bookings" class="btn btn-outline-primary">
                        View Bookings
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Saved Favorites</h5>
                    <h2 class="text-danger">
                        <?= $favoriteCount ?>
                    </h2>
                    <a href="/favorites.php" class="btn btn-outline-danger">
                        My Favorites
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>Explore</h5>
                    <p>Find your next student accommodation.</p>
                    <a href="/listings.php" class="btn btn-primary">
                        Browse Properties
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm mb-4" id="recent-bookings">

        <div class="card-header">
            <h4 class="mb-0">Recent Bookings</h4>
        </div>

        <div class="card-body">

            <?php if (empty($recentBookings)): ?>

                <p class="text-muted">
                    You haven't made any bookings yet.
                </p>

                <a href="/listings.php" class="btn btn-primary">
                    Explore Accommodations
                </a>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-striped table-hover">

                        <thead>
                            <tr>
                                <th>Property</th>
                                <th>Room</th>
                                <th>City</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($recentBookings as $booking): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($booking['property_name']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($booking['room_type']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($booking['city']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($booking['check_in']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($booking['check_out']) ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            <?= htmlspecialchars($booking['booking_status']) ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <div class="mt-4">

        <a href="/favorites.php" class="btn btn-outline-danger me-2">
            My Favorites
        </a>

        <a href="/listings.php" class="btn btn-outline-primary me-2">
            Explore Accommodations
        </a>

        <a href="/logout.php" class="btn btn-outline-secondary">
            Logout
        </a>

    </div>

</div>

<?php require_once __DIR__ . '/../backend/includes/footer.php'; ?>