
<?php

session_start();

require_once __DIR__ . '/../config/db.php';

function loginError(string $message): never {
    header('Location: /login.php?error=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}

if (
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')
) {
    loginError('Invalid request. Please try again.');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    loginError('Please enter your email and password.');
}

try {
    $stmt = $pdo->prepare("
        SELECT user_id, full_name, email, password_hash, role
        FROM users
        WHERE email = :email
    ");

    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        loginError('Invalid email or password.');
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    unset($_SESSION['csrf_token']);

    header('Location: /dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    loginError('Login failed. Please try again.');
}
