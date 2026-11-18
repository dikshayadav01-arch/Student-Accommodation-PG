
<?php

require_once __DIR__ . '/../backend/config/db.php';

$pageTitle = 'Property Details | StayNest';

$propertyId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$propertyId || $propertyId < 1) {
    http_response_code(400);
    exit('Invalid property ID.');
}

// Fetch property
$stmt = $pdo->prepare("
    SELECT *
    FROM properties
    WHERE property_id = :id
");

$stmt->execute(['id' => $propertyId]);
$property = $stmt->fetch();

if (!$property) {
    http_response_code(404);
    exit('Property not found.');
}

// Fetch rooms for this property
$roomStmt = $pdo->prepare("
    SELECT *
    FROM rooms
    WHERE property_id = :id
    ORDER BY room_id
");

$roomStmt->execute(['id' => $propertyId]);
$rooms = $roomStmt->fetchAll();

require_once __DIR__ . '/../backend/includes/header.php';
?>

<section class="property-details-page py-5">
    <div class="container">

        <a href="/listings.php" class="back-link">
            ← Back to listings
        </a>

        <div class="row g-4 mt-2">

            <!-- Property image -->
            <div class="col-lg-7">

                <?php if (!empty($property['image_url'])): ?>
                    <img src="<?= htmlspecialchars($property['image_url']) ?>"
                         class="details-main-image"
                         alt="<?= htmlspecialchars($property['property_name']) ?>">
                <?php else: ?>
                    <div class="property-image-placeholder details-main-image">
                        No image available
                    </div>
                <?php endif; ?>

            </div>

            <!-- Property information -->
            <div class="col-lg-5">

                <span class="property-tag">
                    <?= htmlspecialchars($property['property_type']) ?>
                </span>

                <h1 class="details-title mt-3">
                    <?= htmlspecialchars($property['property_name']) ?>
                </h1>

                <p class="text-muted">
                    📍 <?= htmlspecialchars($property['address']) ?>,
                    <?= htmlspecialchars($property['city']) ?>
                </p>

                <h2 class="details-price">
                    ₹<?= number_format((float)$property['rent']) ?>
                    <span>/ month</span>
                </h2>

                <div class="details-info-card mt-4">

                    <h5>Accommodation Information</h5>

                    <p>
                        <strong>Type:</strong>
                        <?= htmlspecialchars($property['property_type']) ?>
                    </p>

                    <p>
                        <strong>Gender preference:</strong>
                        <?= htmlspecialchars($property['gender_preference']) ?>
                    </p>

                    <p class="mb-0">
                        <strong>Amenities:</strong>
                        <?= htmlspecialchars($property['amenities'] ?? 'Not specified') ?>
                    </p>

                </div>

                <a href="/login.php"
                   class="btn btn-primary btn-lg w-100 mt-4">
                    Book This Accommodation
                </a>

                <p class="small text-muted mt-2">
                    Login will be required before booking.
                </p>

            </div>

        </div>

        <!-- Description -->
        <section class="mt-5">
            <h3>About this accommodation</h3>

            <p class="details-description">
                <?= nl2br(htmlspecialchars($property['description'] ?? 'No description available.')) ?>
            </p>
        </section>

        <!-- Rooms -->
        <section class="mt-5">

            <h3>Room Information</h3>

            <?php if (empty($rooms)): ?>

                <div class="alert alert-info mt-3">
                    Room information has not been added yet.
                </div>

            <?php else: ?>

                <div class="row g-4 mt-1">

                    <?php foreach ($rooms as $room): ?>

                        <div class="col-md-6 col-lg-4">

                            <div class="room-card p-4">

                                <h5>
                                    <?= htmlspecialchars($room['room_type']) ?>
                                </h5>

                                <p>
                                    <strong>Capacity:</strong>
                                    <?= (int)$room['capacity'] ?> people
                                </p>

                                <p>
                                    <strong>Occupied:</strong>
                                    <?= (int)$room['occupied'] ?>
                                </p>

                                <p>
                                    <strong>Available beds:</strong>
                                    <?= max(0, (int)$room['capacity'] - (int)$room['occupied']) ?>
                                </p>

                                <h5 class="price">
                                    ₹<?= number_format((float)$room['price_per_bed']) ?>
                                    <small>/ bed per month</small>
                                </h5>

                                <p class="mb-0">
                                    <strong>Status:</strong>
                                    <?= $room['availability'] ? 'Available' : 'Unavailable' ?>
                                </p>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </div>
</section>

<?php require_once __DIR__ . '/../backend/includes/footer.php'; ?>
