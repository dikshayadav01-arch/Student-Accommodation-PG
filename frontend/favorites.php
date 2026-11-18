<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../backend/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$pageTitle = 'My Favorites | StayNest';

$csrfToken = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;

$sql = "SELECT p.property_id, p.property_name, p.description,
               p.property_type, p.address, p.city, p.rent,
               p.gender_preference, p.amenities, p.image_url
        FROM favorites f
        JOIN properties p ON f.property_id = p.property_id
        WHERE f.student_id = :student_id
        ORDER BY f.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['student_id' => $_SESSION['user_id']]);
$properties = $stmt->fetchAll();

require_once __DIR__ . '/../backend/includes/header.php';
?>

<div class="container mt-5 mb-5">

    <h1 class="mb-2">My Favorites</h1>
    <p class="text-muted mb-4">
        Your saved student accommodations.
    </p>

    <?php if (empty($properties)): ?>

        <div class="alert alert-info">
            You haven't saved any accommodations yet.
            <a href="/listings.php">Explore accommodations</a>
        </div>

    <?php else: ?>

        <div class="row">

            <?php foreach ($properties as $property): ?>

                <div class="col-md-6 col-lg-4 mb-4 favorite-card"
                     data-property-id="<?= (int)$property['property_id'] ?>">

                    <div class="card h-100 shadow-sm">

                        <?php if (!empty($property['image_url'])): ?>
                            <img
                                src="<?= htmlspecialchars($property['image_url']) ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($property['property_name']) ?>"
                                style="height:220px; object-fit:cover;"
                            >
                        <?php endif; ?>

                        <div class="card-body">

                            <h5 class="card-title">
                                <?= htmlspecialchars($property['property_name']) ?>
                            </h5>

                            <p class="text-muted">
                                <?= htmlspecialchars($property['property_type']) ?>
                                &bull;
                                <?= htmlspecialchars($property['city']) ?>
                            </p>

                            <p>
                                <?= htmlspecialchars($property['address']) ?>
                            </p>

                            <h5 class="text-primary">
                                ₹<?= number_format((float)$property['rent']) ?>/month
                            </h5>

                            <p class="card-text">
                                <?= htmlspecialchars(
                                    substr($property['description'] ?? '', 0, 120)
                                ) ?>
                            </p>

                            <a
                                href="/property_details.php?id=<?= (int)$property['property_id'] ?>"
                                class="btn btn-primary mb-2"
                            >
                                View Details
                            </a>

                            <button
                                type="button"
                                class="btn btn-outline-danger remove-favorite"
                                data-property-id="<?= (int)$property['property_id'] ?>"
                            >
                                ♥ Remove Favorite
                            </button>

                        </div>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<script>
document.querySelectorAll('.remove-favorite').forEach(button => {

    button.addEventListener('click', async function () {

        const propertyId = this.dataset.propertyId;
        const card = this.closest('.favorite-card');

        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('property_id', propertyId);
        formData.append('csrf_token', <?= json_encode($csrfToken) ?>);

        try {

            const response = await fetch('/favorite_process.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                card.remove();

                if (document.querySelectorAll('.favorite-card').length === 0) {
                    location.reload();
                }
            } else {
                alert(result.message || 'Unable to remove favorite.');
            }

        } catch (error) {
            alert('Something went wrong. Please try again.');
        }

    });

});
</script>

<?php require_once __DIR__ . '/../backend/includes/footer.php'; ?>