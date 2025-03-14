<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['employer_id'])) {
    header("Location: login.php");
    exit;
}

$employer_id = $_SESSION['employer_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE employer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$employer_id]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error: An error occurred while fetching jobs.";
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Jobs - Job Portal</title>
    <link href="css/common.css" rel="stylesheet">
    <link href="css/employer-dashboard.css" rel="stylesheet">
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

    <section class="dashboard-section">
        <div class="container">
            <h2>My Jobs</h2>
            <p>View and manage your posted jobs.</p>
            <?php if (isset($error)): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (empty($jobs)): ?>
                <p class="message info">You have not posted any jobs yet. <a href="post-job.php">Post a job now</a>.</p>
            <?php else: ?>
                <div class="job-list">
                    <?php foreach ($jobs as $job): ?>
                        <div class="job-item">
                            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                            <p><?php echo htmlspecialchars(substr($job['description'], 0, 100)) . (strlen($job['description']) > 100 ? '...' : ''); ?></p>
                            <p><small>Posted on: <?php echo htmlspecialchars($job['created_at']); ?></small></p>
                            <a href="#" class="btn edit-btn">Edit</a>
                            <a href="#" class="btn delete-btn">Delete</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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