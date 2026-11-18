<?php

session_start();

require_once __DIR__ . '/../backend/config/db.php';

$pageTitle = 'Explore Accommodation | StayNest';

$city = trim($_GET['city'] ?? '');
$budget = trim($_GET['budget'] ?? '');

$isLoggedIn = isset($_SESSION['user_id']);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$favoritedIds = [];

if ($isLoggedIn) {
    $favoriteStmt = $pdo->prepare("
        SELECT property_id
        FROM favorites
        WHERE student_id = :student_id
    ");

    $favoriteStmt->execute([
        'student_id' => $_SESSION['user_id']
    ]);

    $favoritedIds = array_map(
        'intval',
        $favoriteStmt->fetchAll(PDO::FETCH_COLUMN)
    );
}

$sql = "SELECT property_id, property_name, description,
               property_type, address, city, rent,
               gender_preference, amenities, image_url
        FROM properties
        WHERE 1=1";

$params = [];

if ($city !== '') {
    $sql .= " AND city ILIKE :city";
    $params['city'] = '%' . $city . '%';
}

if ($budget !== '' && is_numeric($budget) && (float)$budget >= 0) {
    $sql .= " AND rent <= :budget";
    $params['budget'] = (float)$budget;
}

$sql .= " ORDER BY created_at DESC, property_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$properties = $stmt->fetchAll();

require_once __DIR__ . '/../backend/includes/header.php';
?>

<section class="listing-page py-5">
    <div class="container">

        <div class="mb-4">
            <h1 class="fw-bold">Explore Student Accommodation</h1>
            <p class="text-muted">
                Find the right place to live during your studies.
            </p>
        </div>

        <!-- Search and Filters -->
        <div class="listing-filter-box mb-4">

            <form method="GET" action="/listings.php">
                <div class="row g-3">

                    <div class="col-md-5">
                        <label class="form-label fw-semibold">
                            City
                        </label>

                        <input type="text"
                               name="city"
                               class="form-control"
                               placeholder="Search city"
                               value="<?= htmlspecialchars($city) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Maximum Monthly Rent
                        </label>

                        <select name="budget" class="form-select">
                            <option value="">Any budget</option>

                            <?php foreach ([5000, 8000, 10000, 12000, 15000] as $amount): ?>
                                <option value="<?= $amount ?>"
                                    <?= ($budget === (string)$amount) ? 'selected' : '' ?>>
                                    ₹<?= number_format($amount) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit"
                                class="btn btn-primary flex-grow-1">
                            Search
                        </button>

                        <a href="/listings.php"
                           class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

        </div>

        <!-- Results -->
        <div class="mb-3">
            <h5>
                <?= count($properties) ?> accommodation
                <?= count($properties) === 1 ? 'found' : 'options found' ?>
            </h5>
        </div>

        <div class="row g-4">

            <?php if (empty($properties)): ?>

                <div class="col-12">
                    <div class="alert alert-info">
                        No accommodation found. Try a different city or budget.
                    </div>
                </div>

            <?php else: ?>

                <?php foreach ($properties as $property): ?>

                    <?php
                    $isFavorite = in_array(
                        (int)$property['property_id'],
                        $favoritedIds,
                        true
                    );
                    ?>

                    <div class="col-md-6 col-lg-4">
                        <div class="property-card h-100">

                            <?php if (!empty($property['image_url'])): ?>
                                <img src="<?= htmlspecialchars($property['image_url']) ?>"
                                     alt="<?= htmlspecialchars($property['property_name']) ?>"
                                     class="property-image"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="property-image-placeholder">
                                    No image available
                                </div>
                            <?php endif; ?>

                            <div class="p-3">

                                <span class="property-tag">
                                    <?= htmlspecialchars($property['property_type']) ?>
                                </span>

                                <h5 class="mt-3">
                                    <?= htmlspecialchars($property['property_name']) ?>
                                </h5>

                                <p class="text-muted mb-2">
                                    📍 <?= htmlspecialchars($property['address']) ?>,
                                    <?= htmlspecialchars($property['city']) ?>
                                </p>

                                <p>
                                    <?= htmlspecialchars($property['description']) ?>
                                </p>

                                <p class="small text-muted">
                                    Gender preference:
                                    <?= htmlspecialchars($property['gender_preference']) ?>
                                </p>

                                <p class="small text-muted">
                                    Amenities:
                                    <?= htmlspecialchars($property['amenities'] ?? 'Not specified') ?>
                                </p>

                                <h5 class="price">
                                    ₹<?= number_format((float)$property['rent']) ?>
                                    <small>/ month</small>
                                </h5>

                                <?php if ($isLoggedIn): ?>

                                    <button type="button"
                                            class="btn <?= $isFavorite ? 'btn-danger' : 'btn-outline-danger' ?> w-100 mt-2 favorite-btn"
                                            data-property-id="<?= (int)$property['property_id'] ?>"
                                            data-favorite="<?= $isFavorite ? '1' : '0' ?>">

                                        <?= $isFavorite ? '♥ Remove Favorite' : '♡ Add to Favorites' ?>

                                    </button>

                                <?php else: ?>

                                    <a href="/login.php"
                                       class="btn btn-outline-danger w-100 mt-2">
                                        ♡ Login to Save
                                    </a>

                                <?php endif; ?>

                                <a href="/property_details.php?id=<?= (int)$property['property_id'] ?>"
                                   class="btn btn-outline-primary w-100 mt-2">
                                    View Details
                                </a>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </div>
</section>

<?php if ($isLoggedIn): ?>

<script>
document.querySelectorAll('.favorite-btn').forEach(button => {

    button.addEventListener('click', async function () {

        const propertyId = this.dataset.propertyId;
        const isFavorite = this.dataset.favorite === '1';

        const formData = new FormData();

        formData.append('action', isFavorite ? 'remove' : 'add');
        formData.append('property_id', propertyId);
        formData.append(
            'csrf_token',
            <?= json_encode($_SESSION['csrf_token']) ?>
        );

        this.disabled = true;

        try {

            const response = await fetch('/favorite_process.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                alert(result.message || 'Favorite operation failed.');
                return;
            }

            const newFavorite = !isFavorite;

            this.dataset.favorite = newFavorite ? '1' : '0';

            this.classList.toggle('btn-danger', newFavorite);
            this.classList.toggle('btn-outline-danger', !newFavorite);

            this.textContent = newFavorite
                ? '♥ Remove Favorite'
                : '♡ Add to Favorites';

        } catch (error) {

            alert('Unable to update favorites. Please try again.');

        } finally {

            this.disabled = false;

        }

    });

});
</script>

<?php endif; ?>

<?php require_once __DIR__ . '/../backend/includes/footer.php'; ?>