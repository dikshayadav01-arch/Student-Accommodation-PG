
<?php

session_start();

require_once __DIR__ . '/../config/db.php';

function registrationError(string $message): never {
    header('Location: /register.php?error=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /register.php');
    exit;
}

if (
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')
) {
    registrationError('Invalid request. Please try again.');
}

$name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($name === '' || $email === '' || $password === '') {
    registrationError('Please complete all required fields.');
}

if (strlen($name) > 120 || strlen($email) > 255 || strlen($phone) > 30) {
    registrationError('One or more fields are too long.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    registrationError('Please enter a valid email address.');
}

if (strlen($password) < 8) {
    registrationError('Password must be at least 8 characters.');
}

if ($password !== $confirmPassword) {
    registrationError('Passwords do not match.');
}

try {
    $check = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
    $check->execute(['email' => $email]);

    if ($check->fetch()) {
        registrationError('This email is already registered.');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, email, password_hash, phone, role)
        VALUES (:name, :email, :password, :phone, 'student')
    ");

    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password' => $passwordHash,
        'phone' => $phone !== '' ? $phone : null,
    ]);

    unset($_SESSION['csrf_token']);

    header('Location: /login.php?registered=1');
    exit;

} catch (PDOException $e) {
    error_log('Registration error: ' . $e->getMessage());
    registrationError('Registration failed. Please try again.');
}
