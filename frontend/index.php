
<?php
$pageTitle = 'StayNest | Find Student Accommodation';
require_once __DIR__ . '/../backend/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center min-vh-50">

            <div class="col-lg-7">
                <span class="hero-badge">YOUR STUDENT HOME STARTS HERE</span>

                <h1 class="hero-title mt-3">
                    Find your perfect
                    <span>student stay.</span>
                </h1>

                <p class="hero-description">
                    Discover comfortable, affordable and verified student
                    accommodation near your college or university.
                </p>

                <!-- Search Form -->
                <form action="/listings.php" method="GET"
                      class="search-box mt-4">

                    <div class="row g-3">

                        <div class="col-md-5">
                            <label for="city" class="form-label">
                                Location
                            </label>

                            <input type="text"
                                   name="city"
                                   id="city"
                                   class="form-control"
                                   placeholder="Enter city"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label for="budget" class="form-label">
                                Max Budget
                            </label>

                            <select name="budget" id="budget"
                                    class="form-select">
                                <option value="">Any budget</option>
                                <option value="5000">₹5,000</option>
                                <option value="8000">₹8,000</option>
                                <option value="12000">₹12,000</option>
                                <option value="15000">₹15,000</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit"
                                    class="btn btn-primary w-100 search-btn">
                                Search
                            </button>
                        </div>

                    </div>
                </form>

            </div>

            <div class="col-lg-5 mt-4 mt-lg-0">
                <img src="https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=900&q=80"
                     class="hero-image img-fluid"
                     alt="Student accommodation bedroom">
            </div>

        </div>
    </div>
</section>

<!-- Features -->
<section class="features-section py-5">
    <div class="container">

        <div class="text-center mb-5">
            <h2>Why choose StayNest?</h2>
            <p class="text-muted">
                Finding your student home should be simple.
            </p>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">🏠</div>
                    <h5>Comfortable Stays</h5>
                    <p>
                        Explore PGs, hostels and apartments
                        designed for student living.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h5>Budget Friendly</h5>
                    <p>
                        Find accommodation that matches
                        your monthly budget.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">📍</div>
                    <h5>Great Locations</h5>
                    <p>
                        Search accommodation near your
                        college and preferred areas.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Featured Accommodations -->
<section class="featured-section py-5">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Explore accommodation</h2>
                <p class="text-muted">
                    Discover places designed for student life.
                </p>
            </div>

            <a href="/listings.php" class="btn btn-outline-primary">
                View All
            </a>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="property-card">
                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80"
                         alt="Modern apartment"
                         class="property-image">

                    <div class="p-3">
                        <span class="property-tag">Apartment</span>
                        <h5 class="mt-2">Modern Student Apartment</h5>
                        <p class="text-muted">📍 Jaipur</p>
                        <h5 class="price">₹8,000 <small>/ month</small></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="property-card">
                    <img src="https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80"
                         alt="Student hostel"
                         class="property-image">

                    <div class="p-3">
                        <span class="property-tag">Hostel</span>
                        <h5 class="mt-2">Student Hostel Room</h5>
                        <p class="text-muted">📍 Delhi</p>
                        <h5 class="price">₹5,500 <small>/ month</small></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="property-card">
                    <img src="https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=800&q=80"
                         alt="PG room"
                         class="property-image">

                    <div class="p-3">
                        <span class="property-tag">PG</span>
                        <h5 class="mt-2">Premium Girls PG</h5>
                        <p class="text-muted">📍 Pune</p>
                        <h5 class="price">₹7,500 <small>/ month</small></h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Cities -->
<section class="cities-section py-5">
    <div class="container">

        <div class="text-center mb-4">
            <h2>Popular student cities</h2>
            <p class="text-muted">
                Find your next stay in a student-friendly city.
            </p>
        </div>

        <div class="row g-3">

            <?php
            $cities = ['Jaipur', 'Delhi', 'Pune', 'Bangalore', 'Mumbai', 'Hyderabad'];
            ?>

            <?php foreach ($cities as $city): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="/listings.php?city=<?= urlencode($city) ?>"
                       class="city-card">
                        <h6><?= htmlspecialchars($city) ?></h6>
                        <span>Explore stays →</span>
                    </a>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../backend/includes/footer.php'; ?>
