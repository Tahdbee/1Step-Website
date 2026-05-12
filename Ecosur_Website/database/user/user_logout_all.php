<?php
session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db_helpers.php';

// Only allow POST for this action to avoid accidental GET triggers
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/profile.php');
    exit;
}

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

// Log the request
logActivity($conn, $userId, 'logout_all_requested', 'User requested logout from all devices');

// Attempt to remove session files that belong to this user (file-based sessions)
$savePath = ini_get('session.save_path');
if (!$savePath) {
    $savePath = sys_get_temp_dir();
}

// session.save_path may contain a prefix like "N;/path" on some setups; extract the path
if (preg_match('#;\s*path=(.*)#', $savePath, $m)) {
    $savePath = $m[1];
}

$removed = 0;
foreach (glob(rtrim($savePath, '\\/') . DIRECTORY_SEPARATOR . 'sess_*') as $file) {
    if (!is_file($file) || !is_readable($file)) {
        continue;
    }

    $contents = file_get_contents($file);
    if ($contents === false) {
        continue;
    }

    // Look for serialized pattern for user_id (int) or string
    $intPattern = "user_id|i:" . $userId . ";";
    $strPattern = 'user_id|s:'; // fallback: search for user id as string inside

    if (strpos($contents, $intPattern) !== false || (strpos($contents, $strPattern) !== false && strpos($contents, (string) $userId) !== false)) {
        @unlink($file);
        $removed++;
    }
}

// Clear current session
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

header('Location: ../../pages/login.php?logout_all=1');
exit;

?>