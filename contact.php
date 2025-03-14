<?php
session_start();
require_once 'includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $form_message = trim($_POST['message']);

    // Validate input
    if (empty($name) || empty($email) || empty($form_message)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $form_message]);
            $success = "Thank you! Your message has been sent.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry (if email is UNIQUE)
                $error = "This email has already submitted a message.";
            } else {
                $error = "Error: An error occurred while sending your message.";
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
    <title>Contact Us - Job Portal</title>
    <link href="css/common.css" rel="stylesheet">
    <link href="css/contact.css" rel="stylesheet">
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

    <section id="contact" class="contact-section">
        <div class="container">
            <h2>Contact Us</h2>
            <p>We’d love to hear from you! Get in touch with us.</p>
            <?php if (!empty($success)): ?>
                <p class="message success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <div class="grid">
                <div class="grid-item contact-info">
                    <p><strong>Address:</strong> Addis Ababa, Ethiopia</p>
                    <p><strong>Phone:</strong> +251 953 420 346</p>
                    <p><strong>Email:</strong> <a href="mailto:EthioJobportal@gmail.com">saminugus126@gmail.com</a></p>
                </div>
                <div class="grid-item contact-form">
                    <form class="contact-form" method="POST">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="input-field" id="name" name="name" placeholder="Enter your name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="input-field" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="input-field" id="message" name="message" placeholder="Your message" rows="5" required><?php echo isset($form_message) ? htmlspecialchars($form_message) : ''; ?></textarea>
                        </div>
                        <button type="submit" class="btn">Send Message</button>
                    </form>
                </div>
            </div>
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
            const navLinks = document.querySelectorAll('.nav-links');

            navToggle.addEventListener('click', function() {
                navLinks.forEach(links => links.classList.toggle('active'));
            });

            document.querySelector('.footer a').addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>