<?php
$pageTitle = "StayNest | Find Your Student Home";
require_once __DIR__ . '/../backend/includes/header.php';
?>

<!-- Hero section -->
<section class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center py-4">

            <div class="col-lg-6">
                <span class="hero-badge">Your student life starts here</span>

                <h1 class="hero-title mt-3">
                    Find a place to call
                    <span class="brand-color">home.</span>
                </h1>

                <p class="hero-description">
                    Discover affordable PGs, hostels and rooms
                    designed for your student life.
                </p>

                <a href="listings.php" class="btn btn-brand btn-lg mt-3">
                    Explore accommodations
                </a>
            </div>

            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="hero-image">
                    <img
                        src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1000&q=80"
                        alt="Modern student accommodation room"
                        class="img-fluid rounded-4">
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Search section -->
<section class="container search-section">
    <div class="search-box shadow">
        <form action="listings.php" method="GET" class="row g-3">

            <div class="col-md-4">
                <label for="city" class="form-label fw-semibold">City</label>
                <input type="text" id="city" name="city"
                       class="form-control"
                       placeholder="Enter your city">
            </div>

            <div class="col-md-4">
                <label for="property_type" class="form-label fw-semibold">
                    Accommodation type
                </label>
                <select name="property_type" id="property_type"
                        class="form-select">
                    <option value="">All types</option>
                    <option value="PG">PG</option>
                    <option value="Hostel">Hostel</option>
                    <option value="Apartment">Apartment</option>
                    <option value="Room">Room</option>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-brand w-100">
                    Search
                </button>
            </div>

        </form>
    </div>
</section>

<!-- Features -->
<section class="container py-5">
    <div class="text-center mb-5">
        <h2 class="section-title">Why choose StayNest?</h2>
        <p class="text-muted">
            Everything you need to find your next student home.
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="feature-card h-100">
                <div class="feature-icon">🏠</div>
                <h4>Multiple accommodations</h4>
                <p>
                    Explore PGs, hostels, shared rooms
                    and apartments in one place.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-card h-100">
                <div class="feature-icon">💰</div>
                <h4>Budget-friendly options</h4>
                <p>
                    Find accommodations that match
                    your budget and preferences.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="feature-card h-100">
                <div class="feature-icon">❤️</div>
                <h4>Save your favourites</h4>
                <p>
                    Shortlist properties and compare
                    the places you love.
                </p>
            </div>
        </div>

    </div>
</section>

<!-- Featured properties placeholder -->
<section class="container py-5">
    <div class="text-center">
        <h2 class="section-title">Explore student accommodation</h2>
        <p class="text-muted">
            Your next home is waiting to be discovered.
        </p>
        <a href="listings.php" class="btn btn-outline-brand mt-2">
            View properties
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/../backend/includes/footer.php'; ?>