<?php
session_start();
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

// Redirect to login if not logged in
if (!$is_logged_in) {
    header('Location: login.php?error=Please%20log%20in%20first');
    exit;
}

require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../database/db_helpers.php';

$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

// Initialize defaults
$profile = [
    'full_name' => $_SESSION['user_name'] ?? '',
    'reviews_posted' => 0,
    'likes_received' => 0,
    'bookmarks' => 0,
    'days_active' => 1,
    'bio' => '',
    'notification_email' => true,
    'notification_marketing' => false,
    'public_profile' => true,
    'two_factor_enabled' => false
    ,
    'member_since' => null
];

$activities = [];

// Fetch user profile data if table exists
if (tableExists($conn, 'user_profile')) {
    $profileSql = "SELECT up.*, us.reviews_posted, us.likes_received, us.bookmarks, us.days_active, u.name, u.created_at 
                   FROM user_profile up 
                   LEFT JOIN user_statistics us ON up.ID_user = us.ID_user
                   LEFT JOIN users u ON up.ID_user = u.ID_user
                   WHERE up.ID_user = ? LIMIT 1";
    $profileStmt = mysqli_prepare($conn, $profileSql);
    if ($profileStmt) {
        mysqli_stmt_bind_param($profileStmt, 'i', $user_id);
        mysqli_stmt_execute($profileStmt);
        $profileResult = mysqli_stmt_get_result($profileStmt);
        $fetchedProfile = mysqli_fetch_assoc($profileResult);
        mysqli_stmt_close($profileStmt);

        if ($fetchedProfile) {
            $profile = array_merge($profile, $fetchedProfile);
        }
    }
}

// Fetch recent activity if table exists
if (tableExists($conn, 'user_activity')) {
    $activitySql = "SELECT action, description, activity_date FROM user_activity 
                    WHERE ID_user = ? ORDER BY activity_date DESC LIMIT 10";
    $activityStmt = mysqli_prepare($conn, $activitySql);
    if ($activityStmt) {
        mysqli_stmt_bind_param($activityStmt, 'i', $user_id);
        mysqli_stmt_execute($activityStmt);
        $activityResult = mysqli_stmt_get_result($activityStmt);
        $activities = mysqli_fetch_all($activityResult, MYSQLI_ASSOC);
        mysqli_stmt_close($activityStmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Profile</title>
    <style>
        :root {
            --bg: #f4f8f2;
            --card: #ffffff;
            --text: #1f2a1f;
            --muted: #4f5f4f;
            --dark: #1f2a1f;
            --lime: #8ccf3f;
            --lime-dark: #6eab2f;
            --border: #d7e5cf;
            --error: #dc3545;
            --success: #28a745;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        .site-header {
            background: var(--dark);
            border-bottom: 2px solid var(--lime);
        }

        .nav-wrap {
            max-width: 1000px;
            margin: 0 auto;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .brand {
            font-weight: bold;
            color: var(--lime);
            text-decoration: none;
            font-size: 18px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #e9f3e4;
            padding: 6px 10px;
            border-radius: 6px;
            transition: background 0.3s ease;
        }

        .nav-links a.active,
        .nav-links a:hover {
            background: var(--lime);
            color: var(--dark);
        }

        main {
            max-width: 1000px;
            margin: 20px auto;
            padding: 0 16px 40px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        h1,
        h2 {
            margin-bottom: 16px;
            color: #355320;
        }

        h1 {
            font-size: 28px;
        }

        h2 {
            font-size: 20px;
            border-bottom: 2px solid var(--lime);
            padding-bottom: 10px;
        }

        p {
            color: var(--muted);
            margin-bottom: 12px;
        }

        .profile-header {
            display: flex;
            gap: 24px;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--lime), var(--lime-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: var(--dark);
            font-weight: bold;
            flex-shrink: 0;
            border: 3px solid var(--lime);
        }

        .profile-info {
            flex: 1;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: var(--dark);
            min-width: 140px;
        }

        .info-value {
            color: var(--muted);
            flex: 1;
            text-align: right;
        }

        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: var(--lime);
            color: var(--dark);
        }

        .btn-primary:hover {
            background: var(--lime-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(140, 207, 63, 0.3);
        }

        .btn-secondary {
            background: var(--border);
            color: var(--dark);
        }

        .btn-secondary:hover {
            background: #c0d1b8;
        }

        .btn-danger {
            background: var(--error);
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .settings-section {
            background: #f9faf9;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .settings-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .settings-item:last-child {
            border-bottom: none;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .stat-box {
            background: linear-gradient(135deg, var(--lime), var(--lime-dark));
            color: var(--dark);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        /* Security Tips removed */

        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-value {
                text-align: left;
                margin-top: 8px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="site-header">
        <nav class="nav-wrap">
            <a href="main.php" class="brand">Ecosur</a>
            <ul class="nav-links">
                <li><a href="main.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="review.php">Reviews</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
                <li><a href="../database/user/user_logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Welcome Section -->
        <div class="card">
            <h1>Welcome back, <?php echo htmlspecialchars(explode('@', $user_email)[0]); ?>!</h1>
            <p>Manage your account settings and personal information from this profile page.</p>
        </div>

        <!-- Profile Header with Avatar -->
        <div class="card">
            <div class="profile-header">
                <div class="avatar"><?php echo strtoupper(substr(explode('@', $user_email)[0], 0, 2)); ?></div>
                <div class="profile-info">
                    <h2>Account Information</h2>
                    <div class="info-row">
                        <span class="info-label">User ID:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user_id); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email Address:</span>
                        <span class="info-value"><?php echo htmlspecialchars($user_email); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Account Status:</span>
                        <span class="info-value" style="color: var(--success); font-weight: bold;">Active</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Member Since:</span>
                        <span class="info-value">
                            <?php
                            $created = $profile['created_at'] ?? $profile['member_since'] ?? null;
                            if (!empty($created)) {
                                echo htmlspecialchars(date('M j, Y', strtotime($created)));
                            } else {
                                echo 'Unknown';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="button-group">
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                <a href="change_password.php" class="btn btn-secondary">Change Password</a>
            </div>
        </div>

        <!-- Account Statistics -->
        <div class="card">
            <h2>Account Statistics</h2>
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-number"><?php echo $profile['reviews_posted'] ?? 0; ?></div>
                    <div class="stat-label">Reviews Posted</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $profile['likes_received'] ?? 0; ?></div>
                    <div class="stat-label">Likes Received</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $profile['bookmarks'] ?? 0; ?></div>
                    <div class="stat-label">Bookmarks</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $profile['days_active'] ?? 0; ?></div>
                    <div class="stat-label">Days Active</div>
                </div>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="card">
            <h2>Account Settings</h2>
            <div class="settings-section">
                <div class="settings-item">
                    <div>
                        <strong>Email Notifications</strong>
                        <p style="margin-bottom: 0; font-size: 14px; color: var(--muted);">Receive updates about your
                            account</p>
                    </div>
                    <input type="checkbox" <?php echo ($profile['notification_email'] ?? true) ? 'checked' : ''; ?>
                        style="width: 20px; height: 20px; cursor: pointer;">
                </div>
                <div class="settings-item">
                    <div>
                        <strong>Marketing Emails</strong>
                        <p style="margin-bottom: 0; font-size: 14px; color: var(--muted);">Receive promotional content
                        </p>
                    </div>
                    <input type="checkbox" <?php echo ($profile['notification_marketing'] ?? false) ? 'checked' : ''; ?>
                        style="width: 20px; height: 20px; cursor: pointer;">
                </div>
                <div class="settings-item">
                    <div>
                        <strong>Public Profile</strong>
                        <p style="margin-bottom: 0; font-size: 14px; color: var(--muted);">Allow others to view your
                            profile</p>
                    </div>
                    <input type="checkbox" <?php echo ($profile['public_profile'] ?? true) ? 'checked' : ''; ?>
                        style="width: 20px; height: 20px; cursor: pointer;">
                </div>
                <div class="settings-item">
                    <div>
                        <strong>Two-Factor Authentication</strong>
                        <p style="margin-bottom: 0; font-size: 14px; color: var(--muted);">Enhanced security for your
                            account</p>
                    </div>
                    <a href="#" class="btn btn-secondary"
                        style="padding: 6px 12px; font-size: 12px;"><?php echo ($profile['two_factor_enabled'] ?? false) ? 'Disable' : 'Enable'; ?></a>
                </div>
            </div>
        </div>

        <!-- Security Tips removed per request -->

        <!-- Danger Zone -->
        <div class="card">
            <h2>Danger Zone</h2>
            <div class="alert alert-warning">
                <strong>⚠️ Warning:</strong> These actions cannot be undone. Please proceed with caution.
            </div>
            <div class="button-group">
                <form method="post" action="../database/user/user_logout_all.php" style="margin:0;">
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Logout from all devices? This will sign you out everywhere.');">Logout
                        from All Devices</button>
                </form>

                <form method="post" action="../database/user/user_delete.php" style="margin:0;">
                    <input type="hidden" name="confirm" value="1">
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">Delete
                        Account</button>
                </form>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <h2>Recent Activity</h2>
            <div class="settings-section">
                <?php if (empty($activities)): ?>
                    <p style="text-align: center; color: var(--muted);">No activity yet</p>
                <?php else: ?>
                    <?php foreach ($activities as $activity): ?>
                        <div class="settings-item">
                            <div>
                                <strong><?php echo htmlspecialchars(ucfirst($activity['action'])); ?></strong>
                                <p style="margin-bottom: 0; font-size: 14px; color: var(--muted);">
                                    <?php echo htmlspecialchars($activity['description']); ?>
                                </p>
                            </div>
                            <span
                                style="font-size: 12px; color: var(--muted);"><?php echo date('M j, Y g:i A', strtotime($activity['activity_date'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>