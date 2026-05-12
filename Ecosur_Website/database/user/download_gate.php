<?php
session_start();

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: ../../pages/login.php?error=' . rawurlencode('Please log in first to access the download button.'));
    exit;
}

header('Location: ../../pages/main.php?download=1');
exit;
