<?php
session_start();
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;

require_once __DIR__ . '/../database/config.php';

$reviews = array();
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
    $sql = "SELECT ID, ID_user, Review, Stars FROM `review` ORDER BY ID DESC LIMIT 50";
} else {
    $sql = "SELECT ID, ID_user, Review FROM `review` ORDER BY ID DESC LIMIT 50";
}

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (!isset($row['Stars'])) {
            $row['Stars'] = null;
            if (preg_match('/^\[(\d)\/5\]\s*(.*)$/s', $row['Review'], $m)) {
                $row['Stars'] = (int) $m[1];
                $row['Review'] = $m[2];
            }
        } else {
            $row['Stars'] = (int) $row['Stars'];
        }

        $reviews[] = $row;
    }
    mysqli_free_result($result);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reviews - Fitness App</title>
    <style>
        :root {
            --bg: #f4f8f2;
            --card: #ffffff;
            --text: #1f2a1f;
            --muted: #4f5f4f;
            --dark: #1f2a1f;
            --lime: #8ccf3f;
            --border: #d7e5cf;
            --star: #f2b01e;
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
            max-width: 900px;
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
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            color: #e9f3e4;
            padding: 6px 10px;
            border-radius: 6px;
        }

        .nav-links a.active,
        .nav-links a:hover {
            background: var(--lime);
            color: var(--dark);
        }

        main {
            max-width: 900px;
            margin: 20px auto;
            padding: 0 16px 20px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 14px;
        }

        h1,
        h2 {
            margin-bottom: 8px;
            color: #355320;
        }

        p {
            color: var(--muted);
            margin-bottom: 10px;
        }

        .message {
            padding: 10px 12px;
            border-radius: 8px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .message.error {
            background: #fdecea;
            border: 1px solid #f5c2bd;
            color: #842029;
        }

        .message.success {
            background: #edf7ed;
            border: 1px solid #b8dfba;
            color: #1f5f27;
        }

        .form-grid {
            display: grid;
            gap: 10px;
            margin-top: 12px;
        }

        .form-row {
            display: grid;
            gap: 6px;
        }

        .form-row label {
            font-weight: bold;
            color: #355320;
        }

        .form-row textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font: inherit;
            background: #fbfdf9;
            color: var(--text);
            min-height: 120px;
            resize: vertical;
        }

        .star-row {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }

        .star-row input[type="radio"] {
            accent-color: var(--star);
            transform: scale(1.15);
        }

        .star-label {
            color: #7c5a00;
            font-weight: bold;
            margin-right: 6px;
        }

        .button {
            display: inline-block;
            text-decoration: none;
            background: var(--dark);
            color: var(--lime);
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: bold;
            border: 1px solid var(--dark);
            cursor: pointer;
        }

        .button:hover {
            background: var(--lime);
            color: var(--dark);
        }

        .review-list {
            display: grid;
            gap: 10px;
        }

        .review-item {
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fbfdf9;
            padding: 12px;
        }

        .review-meta {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 6px;
            color: #51624a;
            font-size: 0.94rem;
        }

        .review-stars {
            color: var(--star);
            letter-spacing: 1px;
            font-weight: bold;
        }

        .empty-text {
            color: #5c6a58;
        }

        .footer {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 16px 20px;
            color: #5c6a58;
        }

        @media (max-width: 650px) {
            .nav-wrap {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links a {
                display: block;
            }
        }
    </style>
</head>

<body>
    <header class="site-header">
        <div class="nav-wrap">
            <div class="brand">1Step</div>
            <nav aria-label="Main navigation">
                <ul class="nav-links">
                    <li><a href="main.php">Main Page</a></li>
                    <li><a href="about.php">About Page</a></li>
                    <li><a class="active" href="review.php">Review</a></li>
                    <?php if (!$is_logged_in): ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php else: ?>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="../database/user/user_logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="card">
            <h1>User Reviews</h1>
            <p>Everyone can read reviews. Only logged-in users can submit a review tied to their account.</p>
            <?php if (isset($_GET['error'])): ?>
                <div class="message error"><?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['submitted']) && $_GET['submitted'] === '1'): ?>
                <div class="message success">Thanks, your review has been posted.</div>
            <?php endif; ?>
        </section>

        <section class="card">
            <h2>Write a Review</h2>
            <?php if ($is_logged_in): ?>
                <form class="form-grid" action="../database/user/review_create.php" method="post">
                    <div class="form-row">
                        <label>Star Rating</label>
                        <div class="star-row">
                            <span class="star-label">Rate:</span>
                            <label><input type="radio" name="stars" value="1" required /> 1</label>
                            <label><input type="radio" name="stars" value="2" /> 2</label>
                            <label><input type="radio" name="stars" value="3" /> 3</label>
                            <label><input type="radio" name="stars" value="4" /> 4</label>
                            <label><input type="radio" name="stars" value="5" /> 5</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="review">Review</label>
                        <textarea id="review" name="review" maxlength="1000" placeholder="Share your experience..."
                            required></textarea>
                    </div>
                    <button class="button" type="submit">Submit Review</button>
                </form>
            <?php else: ?>
                <p class="empty-text">Log in first to submit a review.</p>
                <a class="button" href="login.php">Go to Login</a>
            <?php endif; ?>
        </section>

        <section class="card">
            <h2>Recent Reviews</h2>
            <?php if (empty($reviews)): ?>
                <p class="empty-text">No reviews yet. Be the first to post one.</p>
            <?php else: ?>
                <div class="review-list">
                    <?php foreach ($reviews as $entry): ?>
                        <article class="review-item">
                            <div class="review-meta">
                                <span>User ID: <?php echo (int) $entry['ID_user']; ?></span>
                                <?php if (!is_null($entry['Stars'])): ?>
                                    <span
                                        class="review-stars"><?php echo str_repeat('★', (int) $entry['Stars']) . str_repeat('☆', 5 - (int) $entry['Stars']); ?></span>
                                <?php endif; ?>
                                <?php if ($is_logged_in && isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $entry['ID_user']): ?>
                                    <form method="post" action="../database/user/review_delete.php" style="margin:0;">
                                        <input type="hidden" name="review_id" value="<?php echo (int) $entry['ID']; ?>">
                                        <button type="submit" class="button" onclick="return confirm('Delete this review?');"
                                            style="background:#c82333;color:#fff;border-color:#c82333;">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($entry['Review'], ENT_QUOTES, 'UTF-8')); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>1Step - Everything starts with the first step.</p>
    </footer>
</body>

</html>