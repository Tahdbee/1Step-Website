<?php
session_start();
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About - Fitness App</title>
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

        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .team-member {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px;
            background: #fbfdf9;
        }

        .team-member h3 {
            color: var(--dark);
            margin-bottom: 4px;
            font-size: 1rem;
        }

        .badge {
            display: inline-block;
            background: var(--dark);
            color: var(--lime);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.82rem;
            margin-top: 6px;
        }

        .footer {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 16px 20px;
            color: #5c6a58;
        }

        @media (max-width: 750px) {
            .team-grid {
                grid-template-columns: 1fr;
            }
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
                    <li><a href="main.php#main">Main Page</a></li>
                    <li><a class="active" href="about.php">About Page</a></li>
                    <li><a href="review.php">Review</a></li>
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
            <h1>About Our Team</h1>
            <p>
                We are a small team of developers, designers, and fitness enthusiasts who built this website and APK
                to make a simple and affordable application for everyone.
            </p>
            <p>
                Our goal is to help users install the app quickly, follow their workout plans, and stay motivated daily.
            </p>
            <span class="badge">Website + APK Team</span>
        </section>

        <section class="card">
            <h2>Team Members</h2>
            <div class="team-grid">
                <article class="team-member">
                    <h3>Fayyaz</h3>
                    <p>Project Leader</p>
                </article>
                <article class="team-member">
                    <h3>Raynaldo</h3>
                    <p>UI Designer and Frontend Developer</p>
                </article>
                <article class="team-member">
                    <h3>Mecha</h3>
                    <p>APK Developer and App Tester</p>
                </article>
            </div>
            <div class="team-grid">
                <article class="team-member">
                    <h3>Annisa</h3>
                    <p>Scretary and Admin</p>
                </article>
                <article class="team-member">
                    <h3>Dian</h3>
                    <p>Finance Operator</p>
                </article>
                <article class="team-member">
                    <h3>Dewi</h3>
                    <p>Chief Operating and Marketing Officer</p>
                </article>
            </div>
        </section>

        <section class="card">
            <h2>Why We Built This</h2>
            <p>
                We created this platform so users can discover the app, install it on mobile, and start training with
                less setup and less confusion.
            </p>
        </section>
    </main>

    <footer class="footer">
        <p>Fitness App Team</p>
    </footer>
</body>

</html>