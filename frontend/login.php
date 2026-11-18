
<?php
session_start();

$pageTitle = 'Login | StayNest';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../backend/includes/header.php';
?>

<section class="auth-section py-5">
    <div class="container">
        <div class="auth-card mx-auto">

            <h2 class="text-center fw-bold">Welcome back</h2>
            <p class="text-center text-muted mb-4">
                Login to your StayNest account.
            </p>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">
                    Account created successfully. Please log in.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['logged_out'])): ?>
                <div class="alert alert-success">
                    You have been logged out successfully.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <form action="/login_process.php" method="POST">

                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                           class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Login
                </button>

            </form>

            <p class="text-center mt-4 mb-0">
                Don't have an account?
                <a href="/register.php">Register</a>
            </p>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../backend/includes/footer.php'; ?>
