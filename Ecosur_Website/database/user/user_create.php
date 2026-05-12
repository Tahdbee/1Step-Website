<?php
session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db_helpers.php';

function redirect_with_message($path, $key, $message)
{
    header('Location: ' . $path . '?' . $key . '=' . rawurlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/register.php');
    exit;
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect_with_message('../../pages/register.php', 'error', 'Invalid email format.');
}

if (strlen($password) < 6) {
    redirect_with_message('../../pages/register.php', 'error', 'Password must be at least 6 characters.');
}

if ($password !== $confirmPassword) {
    redirect_with_message('../../pages/register.php', 'error', 'Passwords do not match.');
}

$checkSql = "SELECT ID_user FROM `users` WHERE email = ? LIMIT 1";
$checkStmt = mysqli_prepare($conn, $checkSql);

if (!$checkStmt) {
    redirect_with_message('../../pages/register.php', 'error', 'Failed to prepare user check query.');
}

mysqli_stmt_bind_param($checkStmt, 's', $email);
mysqli_stmt_execute($checkStmt);
$existingResult = mysqli_stmt_get_result($checkStmt);
$existingUser = $existingResult ? mysqli_fetch_assoc($existingResult) : null;
mysqli_stmt_close($checkStmt);

if ($existingUser) {
    redirect_with_message('../../pages/register.php', 'error', 'Email is already registered.');
}

$hashedPassword = $password;
$insertSql = "INSERT INTO `users` (email, password, name) VALUES (?, ?, ?)";
$insertStmt = mysqli_prepare($conn, $insertSql);

if (!$insertStmt) {
    redirect_with_message('../../pages/register.php', 'error', 'Failed to prepare insert query.');
}

mysqli_stmt_bind_param($insertStmt, 'sss', $email, $hashedPassword, $name);
$insertOk = mysqli_stmt_execute($insertStmt);

if (!$insertOk) {
    mysqli_stmt_close($insertStmt);
    redirect_with_message('../../pages/register.php', 'error', 'Registration failed. Please try again.');
}

$userId = mysqli_insert_id($conn);
mysqli_stmt_close($insertStmt);

// Create user profile and stats (if tables exist)
createUserProfile($conn, $userId, $name);
createUserStats($conn, $userId);
logActivity($conn, $userId, 'registration', 'User registered');

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $userId;
$_SESSION['user_email'] = $email;
$_SESSION['user_name'] = $name;
$_SESSION['is_logged_in'] = true;

header('Location: ../../pages/main.php?registered=1');
exit;

