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
    header('Location: ../../pages/login.php');
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    redirect_with_message('../../pages/login.php', 'error', 'Please enter a valid email and password.');
}

$selectSql = "SELECT ID_user, email, password, name FROM `users` WHERE email = ? LIMIT 1";
$selectStmt = mysqli_prepare($conn, $selectSql);

if (!$selectStmt) {
    redirect_with_message('../../pages/login.php', 'error', 'Failed to prepare login query.');
}

mysqli_stmt_bind_param($selectStmt, 's', $email);
mysqli_stmt_execute($selectStmt);
$result = mysqli_stmt_get_result($selectStmt);
$user = $result ? mysqli_fetch_assoc($result) : null;
mysqli_stmt_close($selectStmt);

if (!$user) {
    redirect_with_message('../../pages/login.php', 'error', 'Email or password is incorrect.');
}

$passwordMatches = $password === $user['password'];

if (!$passwordMatches) {
    redirect_with_message('../../pages/login.php', 'error', 'Email or password is incorrect.');
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['ID_user'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name'] = $user['name'] ?? '';
$_SESSION['is_logged_in'] = true;

// Log activity and update last login (if tables exist)
logActivity($conn, $user['ID_user'], 'login', 'User logged in');
updateLastLogin($conn, $user['ID_user']);

header('Location: ../../pages/main.php?login=1');
exit;
