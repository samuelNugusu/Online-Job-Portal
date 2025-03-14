<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['employer_id'])) {
    header("Location: login.php");
    exit;
}

$employer_id = $_SESSION['employer_id'];
$stmt = $pdo->prepare("SELECT j.*, COUNT(a.id) as app_count FROM jobs j LEFT JOIN applications a ON j.id = a.job_id WHERE j.employer_id = ? GROUP BY j.id ORDER BY j.posted_at DESC");
$stmt->execute([$employer_id]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employer Dashboard - Job Portal</title>
    <link href="css/styles.css" rel="stylesheet">
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
                <li><a href="employer-dashboard.php">My Jobs</a></li>
                <li><a href="contact.php#contact">Contact Us</a></li>
            </ul>
            <ul class="nav-links right">
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="jobs-section">
        <div class="container">
            <h2>My Jobs</h2>
            <p><a href="post-job.php">Post a new job</a></p>
            <?php if (empty($jobs)): ?>
                <p>You haven’t posted any jobs yet.</p>
            <?php else: ?>
                <div class="grid">
                    <?php foreach ($jobs as $job): ?>
                        <div class="grid-item">
                            <div class="card">
                                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                <p><?php echo htmlspecialchars($job['description']); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                                <p><strong>Posted:</strong> <?php echo date('F j, Y', strtotime($job['posted_at'])); ?></p>
                                <p><strong>Applications:</strong> <?php echo $job['app_count']; ?></p>
                            </div>
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
</body>
</html>