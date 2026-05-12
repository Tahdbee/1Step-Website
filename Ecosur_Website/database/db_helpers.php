<?php
// Helper functions for database operations

function tableExists($conn, $tableName)
{
    $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }

    $dbname = '1Step';
    mysqli_stmt_bind_param($stmt, 'ss', $dbname, $tableName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_fetch_assoc($result) !== null;
    mysqli_stmt_close($stmt);

    return $exists;
}

function logActivity($conn, $userId, $action, $description)
{
    if (!tableExists($conn, 'user_activity')) {
        return false;
    }

    // Ensure user exists to satisfy foreign key constraint
    $userCheck = mysqli_prepare($conn, "SELECT 1 FROM `users` WHERE `ID_user` = ? LIMIT 1");
    if ($userCheck) {
        mysqli_stmt_bind_param($userCheck, 'i', $userId);
        mysqli_stmt_execute($userCheck);
        $userRes = mysqli_stmt_get_result($userCheck);
        $userExists = mysqli_fetch_assoc($userRes) !== null;
        mysqli_stmt_close($userCheck);
        if (!$userExists) {
            return false;
        }
    } else {
        return false;
    }

    $sql = "INSERT INTO `user_activity` (`ID_user`, `action`, `description`) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'iss', $userId, $action, $description);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function updateUserStats($conn, $userId, $field, $increment = 1)
{
    if (!tableExists($conn, 'user_statistics')) {
        return false;
    }

    // First check if user stats record exists
    $checkSql = "SELECT ID FROM `user_statistics` WHERE `ID_user` = ? LIMIT 1";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    if (!$checkStmt) {
        return false;
    }

    mysqli_stmt_bind_param($checkStmt, 'i', $userId);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    $statsExists = mysqli_fetch_assoc($checkResult) !== null;
    mysqli_stmt_close($checkStmt);

    if (!$statsExists) {
        // Create stats record if it doesn't exist
        $insertSql = "INSERT INTO `user_statistics` (`ID_user`) VALUES (?)";
        $insertStmt = mysqli_prepare($conn, $insertSql);
        if (!$insertStmt) {
            return false;
        }

        mysqli_stmt_bind_param($insertStmt, 'i', $userId);
        mysqli_stmt_execute($insertStmt);
        mysqli_stmt_close($insertStmt);
    }

    // Update the field
    $sql = "UPDATE `user_statistics` SET `$field` = `$field` + ? WHERE `ID_user` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'ii', $increment, $userId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function updateLastLogin($conn, $userId)
{
    if (!tableExists($conn, 'user_statistics')) {
        return false;
    }

    $sql = "UPDATE `user_statistics` SET `last_login` = CURRENT_TIMESTAMP WHERE `ID_user` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'i', $userId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function createUserProfile($conn, $userId, $name = '')
{
    if (!tableExists($conn, 'user_profile')) {
        return false;
    }

    // Ensure user exists
    $userCheck = mysqli_prepare($conn, "SELECT 1 FROM `users` WHERE `ID_user` = ? LIMIT 1");
    if ($userCheck) {
        mysqli_stmt_bind_param($userCheck, 'i', $userId);
        mysqli_stmt_execute($userCheck);
        $userRes = mysqli_stmt_get_result($userCheck);
        $userExists = mysqli_fetch_assoc($userRes) !== null;
        mysqli_stmt_close($userCheck);
        if (!$userExists) {
            return false;
        }
    } else {
        return false;
    }

    $sql = "INSERT INTO `user_profile` (`ID_user`, `full_name`) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'is', $userId, $name);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

function createUserStats($conn, $userId)
{
    if (!tableExists($conn, 'user_statistics')) {
        return false;
    }

    // Ensure user exists
    $userCheck = mysqli_prepare($conn, "SELECT 1 FROM `users` WHERE `ID_user` = ? LIMIT 1");
    if ($userCheck) {
        mysqli_stmt_bind_param($userCheck, 'i', $userId);
        mysqli_stmt_execute($userCheck);
        $userRes = mysqli_stmt_get_result($userCheck);
        $userExists = mysqli_fetch_assoc($userRes) !== null;
        mysqli_stmt_close($userCheck);
        if (!$userExists) {
            return false;
        }
    } else {
        return false;
    }

    $sql = "INSERT INTO `user_statistics` (`ID_user`, `days_active`) VALUES (?, 1)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'i', $userId);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}
