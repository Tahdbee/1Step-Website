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
    redirect_with_message('../../pages/review.php', 'error', 'Please log in first before submitting a review.');
}

$userId = (int) $_SESSION['user_id'];
$stars = isset($_POST['stars']) ? (int) $_POST['stars'] : 0;
$reviewText = isset($_POST['review']) ? trim($_POST['review']) : '';

if ($stars < 1 || $stars > 5) {
    redirect_with_message('../../pages/review.php', 'error', 'Please choose a star rating from 1 to 5.');
}

if ($reviewText === '') {
    redirect_with_message('../../pages/review.php', 'error', 'Please write your review before submitting.');
}

if (mb_strlen($reviewText) > 1000) {
    redirect_with_message('../../pages/review.php', 'error', 'Review is too long. Maximum is 1000 characters.');
}

$hasStarsColumn = false;
$columnStmt = mysqli_prepare(
    $conn,
    "SELECT 1 FROM information_schema.columns WHERE table_schema = ? AND table_name = 'review' AND column_name = 'Stars' LIMIT 1"
);

if ($columnStmt) {
    mysqli_stmt_bind_param($columnStmt, 's', $dbname);
    mysqli_stmt_execute($columnStmt);
    $columnResult = mysqli_stmt_get_result($columnStmt);
    $hasStarsColumn = $columnResult && mysqli_fetch_assoc($columnResult);
    mysqli_stmt_close($columnStmt);
}

if ($hasStarsColumn) {
    $sql = "INSERT INTO `review` (`ID_user`, `Review`, `Stars`) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        redirect_with_message('../../pages/review.php', 'error', 'Failed to prepare review query.');
    }

    mysqli_stmt_bind_param($stmt, 'isi', $userId, $reviewText, $stars);
} else {
    $reviewWithRating = '[' . $stars . '/5] ' . $reviewText;
    $sql = "INSERT INTO `review` (`ID_user`, `Review`) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        redirect_with_message('../../pages/review.php', 'error', 'Failed to prepare review query.');
    }

    mysqli_stmt_bind_param($stmt, 'is', $userId, $reviewWithRating);
}

$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$ok) {
    redirect_with_message('../../pages/review.php', 'error', 'Failed to save your review. Please try again.');
}

// Update user statistics and log activity (if tables exist)
// Atomically create-or-increment reviews_posted to ensure the count increments reliably
if (function_exists('tableExists') && tableExists($conn, 'user_statistics')) {
    $incSql = "INSERT INTO `user_statistics` (`ID_user`, `reviews_posted`) VALUES (?, 1)
               ON DUPLICATE KEY UPDATE `reviews_posted` = `reviews_posted` + 1";
    $incStmt = mysqli_prepare($conn, $incSql);
    if ($incStmt) {
        mysqli_stmt_bind_param($incStmt, 'i', $userId);
        mysqli_stmt_execute($incStmt);
        mysqli_stmt_close($incStmt);
    }
} else {
    // Fallback to helper which will try to create the stats row then update
    updateUserStats($conn, $userId, 'reviews_posted', 1);
}

logActivity($conn, $userId, 'review_posted', 'User posted a review');

header('Location: ../../pages/review.php?submitted=1');
exit;
