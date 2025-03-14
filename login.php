<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? '';

    if (empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($role === 'jobseeker') {
        $stmt = $pdo->prepare("SELECT * FROM job_seekers WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['seeker_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password for Job Seeker.";
        }
    } elseif ($role === 'employer') {
        $stmt = $pdo->prepare("SELECT * FROM employers WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['employer_id'] = $user['id'];
            $_SESSION['company_name'] = $user['company_name'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password for Employer.";
        }
    } else {
        $error = "Please select a role.";
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Job Portal</title>
    <link href="css/common.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar" id="insidenav">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">Job Portal</a>
            <button class="nav-toggle">☰</button>
            <ul class="nav-links">
                <li><a href="index.php#main">Home</a></li>
                <li><a href="search.php">Search Jobs</a></li>
                <?php if (isset($_SESSION['employer_id'])): ?>
                    <li><a href="post-job.php">Post a Job</a></li>
                    <li><a href="employer-dashboard.php">My Jobs</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['seeker_id'])): ?>
                    <li><a href="jobseeker-dashboard.php">My Applications</a></li>
                <?php endif; ?>
                <li><a href="contact.php#contact">Contact Us</a></li>
            </ul>
            <ul class="nav-links right">
                <?php if (!isset($_SESSION['seeker_id']) && !isset($_SESSION['employer_id'])): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">Register ▼</a>
                        <ul class="dropdown-menu">
                            <li><a href="jobseeker-register.php">Job Seeker</a></li>
                            <li class="divider"></li>
                            <li><a href="employer-register.php">Employer</a></li>
                        </ul>
                    </li>
                    <li><a href="login.php">Login</a></li>
                <?php else: ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <section class="login-section">
        <div class="container">
            <h2>Login</h2>
            <p>Sign in to your account.</p>
            <?php if (isset($error)): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form class="login-form" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="input-field" id="email" name="email" placeholder="Enter email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="input-field" id="password" name="password" placeholder="Enter password" required>
                </div>
                <div class="form-group role-group">
                    <label>Role</label>
                    <div class="radio-group">
                        <input type="radio" id="jobseeker" name="role" value="jobseeker" <?php echo (isset($role) && $role === 'jobseeker') ? 'checked' : ''; ?> required>
                        <label for="jobseeker">Job Seeker</label>
                        <input type="radio" id="employer" name="role" value="employer" <?php echo (isset($role) && $role === 'employer') ? 'checked' : ''; ?> required>
                        <label for="employer">Employer</label>
                    </div>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            <p>Don’t have an account? Register as a <a href="jobseeker-register.php">Job Seeker</a> or <a href="employer-register.php">Employer</a>.</p>
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
