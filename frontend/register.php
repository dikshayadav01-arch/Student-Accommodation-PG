
<?php
session_start();

$pageTitle = 'Create Account | StayNest';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../backend/includes/header.php';
?>

<section class="auth-section py-5">
    <div class="container">
        <div class="auth-card mx-auto">

            <h2 class="text-center fw-bold">Create your account</h2>
            <p class="text-center text-muted mb-4">
                Join StayNest and find your student home.
            </p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <form action="/register_process.php" method="POST">

                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name"
                           class="form-control" required maxlength="120">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email"
                           class="form-control" required maxlength="255">
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone"
                           class="form-control" maxlength="30">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                           class="form-control" required minlength="8">
                    <small class="text-muted">
                        At least 8 characters.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password"
                           class="form-control" required minlength="8">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Create Account
                </button>

            </form>

            <p class="text-center mt-4 mb-0">
                Already have an account?
                <a href="/login.php">Login</a>
            </p>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../backend/includes/footer.php'; ?>
