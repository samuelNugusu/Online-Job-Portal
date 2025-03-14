<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO job_seekers (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);
            $success = "Registration successful! Redirecting to login...";
            // Redirect after 2 seconds
            header("Refresh:2;url=login.php");
            $_SESSION['username'] = $username; // Optional: Set session for welcome message
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry (email uniqueness)
                $error = "Email already registered.";
            } else {
                $error = "Registration failed: An error occurred.";
            }
        }
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Job Seeker | Job Portal</title>
    <link href="css/common.css" rel="stylesheet">
    <link href="css/register.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar" id="insidenav">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">Job Portal</a>
            <button class="nav-toggle">☰</button>
            <ul class="nav-links">
                <li><a href="index.php#main">Home</a></li>
                <li><a href="search.php">Search Jobs</a></li>
                <li><a href="contact.php#contact">Contact Us</a></li>
            </ul>
            <ul class="nav-links right">
                <?php if (!isset($_SESSION['seeker_id']) && !isset($_SESSION['employer_id'])): ?>
                    <li><a href="login.php">Login</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">Register ▼</a>
                        <ul class="dropdown-menu">
                            <li><a href="jobseeker-register.php">Job Seeker</a></li>
                            <li class="divider"></li>
                            <li><a href="employer-register.php">Employer</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <section class="register-section">
        <div class="container">
            <h2>Register as Job Seeker</h2>
            <p>Create your account to start applying for jobs.</p>
            <?php if (!empty($error)): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <p class="message success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form class="register-form" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="input-field" id="username" name="username" placeholder="Choose a username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="input-field" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="input-field" id="password" name="password" placeholder="Enter password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" class="input-field" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </section>

    <footer class="footer">
        <a href="#insidenav" title="To Top">↑</a>
        <p>Job Portal © 2025</p>
    </footer>

    <script src="js/scripts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navToggle = document.querySelector('.nav-toggle');
            const navLinks = document.querySelector('.nav-links');

            navToggle.addEventListener('click', function() {
                navLinks.classList.toggle('active');
            });

            document.querySelector('.footer a').addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>
