<?php
session_start();
require_once 'includes/db.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM jobs ORDER BY created_at DESC");
    $stmt->execute();
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
    <title>Search Jobs - Job Portal</title>
    <link href="css/common.css" rel="stylesheet">
    <link href="css/search.css" rel="stylesheet">
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

    <section class="search-section">
        <div class="container">
            <h2>Search Jobs</h2>
            <p>Find your dream job below.</p>
            <?php if (isset($error)): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (empty($jobs)): ?>
                <p class="message info">No jobs available at the moment.</p>
            <?php else: ?>
                <div class="job-list">
                    <?php foreach ($jobs as $job): ?>
                        <div class="job-item">
                            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                            <p><?php echo htmlspecialchars(substr($job['description'], 0, 100)) . (strlen($job['description']) > 100 ? '...' : ''); ?></p>
                            <p><small>Posted on: <?php echo htmlspecialchars($job['created_at']); ?></small></p>
                            <?php if (isset($_SESSION['seeker_id'])): ?>
                                <a href="apply.php?job_id=<?php echo $job['id']; ?>" class="btn apply-btn">Apply</a>
                            <?php else: ?>
                                <p><small><a href="login.php">Login as a Job Seeker</a> to apply.</small></p>
                            <?php endif; ?>
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