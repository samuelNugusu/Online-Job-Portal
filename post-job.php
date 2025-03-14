<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['employer_id'])) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $employer_id = $_SESSION['employer_id'];

    // Validate input
    if (empty($title) || empty($description) || empty($location)) {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO jobs (employer_id, title, description, location) VALUES (?, ?, ?, ?)");
            $stmt->execute([$employer_id, $title, $description, $location]);
            $success = "Job posted successfully! Redirecting to My Jobs...";
            // Redirect after 2 seconds
            header("Refresh:2;url=employer-dashboard.php");
        } catch (PDOException $e) {
            $error = "Error: An error occurred while posting the job.";
        }
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post a Job - Job Portal</title>
    <link href="css/common.css" rel="stylesheet">
    <link href="css/post-job.css" rel="stylesheet">
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
                <li><a href="post-job.php">Post a Job</a></li>
                <li><a href="contact.php#contact">Contact Us</a></li>
            </ul>
            <ul class="nav-links right">
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="post-job-section">
        <div class="container">
            <h2>Post a Job</h2>
            <p>Fill out the details to list your job opening.</p>
            <?php if (!empty($success)): ?>
                <p class="message success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form class="post-job-form" method="POST">
                <div class="form-group">
                    <label for="job-title">Job Title</label>
                    <input type="text" class="input-field" id="job-title" name="title" placeholder="Enter job title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Job Description</label>
                    <textarea class="input-field" id="description" name="description" placeholder="Describe the job" rows="5" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="input-field" id="location" name="location" placeholder="Enter job location" value="<?php echo isset($location) ? htmlspecialchars($location) : ''; ?>" required>
                </div>
                <button type="submit" class="btn">Post Job</button>
            </form>
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
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>