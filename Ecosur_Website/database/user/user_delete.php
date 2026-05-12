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
    header('Location: ../../pages/profile.php');
    exit;
}

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    redirect_with_message('../../pages/login.php', 'error', 'Please log in to delete your account.');
}

$userId = (int) $_SESSION['user_id'];

// Optional: require a confirmation field
if (!isset($_POST['confirm']) || $_POST['confirm'] !== '1') {
    redirect_with_message('../../pages/profile.php', 'error', 'Please confirm account deletion.');
}

// Delete user (cascade should remove related records)
$delSql = "DELETE FROM `users` WHERE `ID_user` = ? LIMIT 1";
$delStmt = mysqli_prepare($conn, $delSql);
if (!$delStmt) {
    redirect_with_message('../../pages/profile.php', 'error', 'Failed to prepare account deletion.');
}

mysqli_stmt_bind_param($delStmt, 'i', $userId);
$ok = mysqli_stmt_execute($delStmt);
mysqli_stmt_close($delStmt);

if (!$ok) {
    redirect_with_message('../../pages/profile.php', 'error', 'Failed to delete account.');
}

// Clear session
$_SESSION = array();
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}
session_destroy();

header('Location: ../../pages/register.php?deleted=1');
exit;

?>