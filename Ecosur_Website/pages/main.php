<?php
session_start();
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fitness App</title>
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

        .button {
            display: inline-block;
            text-decoration: none;
            background: var(--dark);
            color: var(--lime);
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: bold;
            border: 1px solid var(--dark);
        }

        .button:hover {
            background: var(--lime);
            color: var(--dark);
        }

        .message {
            padding: 10px 12px;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 4px;
            display: none;
            background: #edf7ed;
            border: 1px solid #b8dfba;
            color: #1f5f27;
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
                    <li><a class="active" href="#main">Main Page</a></li>
                    <li><a href="about.php">About Page</a></li>
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
        <section id="main" class="card">
            <h1>Reach Your Fitness Goals With 1Step</h1>
            <p>
                Start your workout journey with guided plans, daily reminders, and progress tracking in one simple app.
                Login/Create a profile and Download the APK to begin training today.
            </p>
            <div id="download-message" class="message" role="status" aria-live="polite"></div>
            <a class="button" href="../database/user/download_gate.php">Install App</a>
        </section>

        <section class="card">
            <h2>What You Get</h2>
            <p>1. Daily workout plans for college students and people with a busy schedule.</p>
            <p>2. Meal suggestions to support your calorie goals.</p>
            <p>3. Progress chart to track your weekly improvements.</p>
        </section>

        <section class="card">
            <h2>How To Start</h2>
            <p>Step 1: Tap the Install App button.</p>
            <p>Step 2: Get your key from your account.</p>
            <p>Step 3: Choose a plan and start your first workout.</p>
        </section>

        <!-- <section id="login" class="card">
      <h2>Login</h2>
      <p>Use your app account to sign in and keep track of your workouts, meals, and progress.</p>
    </section>

    <section id="register" class="card">
      <h2>Register</h2>
      <p>Create your account after installing the app so you can save your plan and start training.</p>
    </section> -->
    </main>

    <footer class="footer">
        <p>1Step - Everything starts with the first step.</p>
    </footer>

    <script>
        (function () {
            const params = new URLSearchParams(window.location.search);
            const download = params.get("download");
            const messageBox = document.getElementById("download-message");

            if (download === "1" && messageBox) {
                messageBox.textContent = "Access granted. You can now continue to your APK download link.";
                messageBox.style.display = "block";
            }
        })();
    </script>
</body>

</html>