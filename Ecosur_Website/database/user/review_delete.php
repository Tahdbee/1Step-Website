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
    header('Location: ../../pages/review.php');
    exit;
}

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    redirect_with_message('../../pages/review.php', 'error', 'Please log in to delete your review.');
}

$userId = (int) $_SESSION['user_id'];
$reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;

if ($reviewId <= 0) {
    redirect_with_message('../../pages/review.php', 'error', 'Invalid review id.');
}

// Verify ownership and delete
$checkSql = "SELECT ID_user FROM `review` WHERE `ID` = ? LIMIT 1";
$checkStmt = mysqli_prepare($conn, $checkSql);
if (!$checkStmt) {
    redirect_with_message('../../pages/review.php', 'error', 'Failed to prepare delete query.');
}

mysqli_stmt_bind_param($checkStmt, 'i', $reviewId);
mysqli_stmt_execute($checkStmt);
$res = mysqli_stmt_get_result($checkStmt);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($checkStmt);

if (!$row) {
    redirect_with_message('../../pages/review.php', 'error', 'Review not found.');
}

if ((int) $row['ID_user'] !== $userId) {
    redirect_with_message('../../pages/review.php', 'error', 'You are not authorized to delete this review.');
}

// Perform delete
$delSql = "DELETE FROM `review` WHERE `ID` = ? AND `ID_user` = ?";
$delStmt = mysqli_prepare($conn, $delSql);
if (!$delStmt) {
    redirect_with_message('../../pages/review.php', 'error', 'Failed to prepare delete statement.');
}

mysqli_stmt_bind_param($delStmt, 'ii', $reviewId, $userId);
$ok = mysqli_stmt_execute($delStmt);
mysqli_stmt_close($delStmt);

if (!$ok) {
    redirect_with_message('../../pages/review.php', 'error', 'Failed to delete review.');
}

// Decrement reviews_posted safely
if (function_exists('tableExists') && tableExists($conn, 'user_statistics')) {
    // Use updateUserStats with -1 to decrement; function will create stats row if missing
    updateUserStats($conn, $userId, 'reviews_posted', -1);
}

// Log activity
logActivity($conn, $userId, 'review_deleted', 'User deleted a review');

header('Location: ../../pages/review.php?deleted=1');
exit;

?>