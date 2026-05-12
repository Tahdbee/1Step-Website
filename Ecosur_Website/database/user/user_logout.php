<?php
session_start();

// Log logout activity if user is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['is_logged_in'])) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../db_helpers.php';

    $userId = $_SESSION['user_id'];
    logActivity($conn, $userId, 'logout', 'User logged out');
}

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

header('Location: ../../pages/login.php?logout=1');
exit;
