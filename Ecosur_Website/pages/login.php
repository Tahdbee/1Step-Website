<?php
session_start();
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Fitness App</title>
    <style>
        :root {
            --bg: #f4f8f2;
            --card: #ffffff;
            --text: #1f2a1f;
            --muted: #4f5f4f;
            --dark: #1f2a1f;
            --lime: #8ccf3f;
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

        .form-row input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font: inherit;
            background: #fbfdf9;
            color: var(--text);
        }

        .form-row input:focus {
            outline: 2px solid var(--lime);
            outline-offset: 2px;
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
                    <li><a href="review.php">Review</a></li>
                    <?php if (!$is_logged_in): ?>
                        <li><a class="active" href="login.php">Login</a></li>
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
            <h1>Log In</h1>
            <p>Sign in to access your workout plans, meal suggestions, and progress tracking.</p>
            <div id="form-message" class="message" role="status" aria-live="polite"></div>
            <form class="form-grid" action="../database/user/user_login.php" method="post">
                <div class="form-row">
                    <label for="login-email">Email</label>
                    <input id="login-email" name="email" type="email" placeholder="you@example.com" required />
                </div>
                <div class="form-row">
                    <label for="login-password">Password</label>
                    <input id="login-password" name="password" type="password" placeholder="Enter your password"
                        required />
                </div>
                <button class="button" type="submit">Log In</button>
            </form>
        </section>
    </main>

    <footer class="footer">
        <p>1Step - Everything starts with the first step.</p>
    </footer>

    <script>
        (function () {
            const params = new URLSearchParams(window.location.search);
            const messageBox = document.getElementById("form-message");

            if (!messageBox) {
                return;
            }

            const error = params.get("error");
            const logout = params.get("logout");

            if (error) {
                messageBox.textContent = decodeURIComponent(error);
                messageBox.classList.add("error");
                messageBox.style.display = "block";
                return;
            }

            if (logout === "1") {
                messageBox.textContent = "You have been logged out.";
                messageBox.classList.add("success");
                messageBox.style.display = "block";
            }
        })();
    </script>
</body>

</html>