
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['seeker_id'])) {
    header("Location: login.php");
    exit;
}

$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
$seeker_id = $_SESSION['seeker_id'];

if ($job_id <= 0) {
    header("Location: search.php");
    exit;
}

try {
    // Check if the job exists
    $stmt = $pdo->prepare("SELECT id FROM jobs WHERE id = ?");
    $stmt->execute([$job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        header("Location: search.php");
        exit;
    }

    // Check if the user has already applied
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE job_seeker_id = ? AND job_id = ?");
    $stmt->execute([$seeker_id, $job_id]);
    if ($stmt->fetch()) {
        $error = "You have already applied to this job.";
    } else {
        // Insert the application
        $stmt = $pdo->prepare("INSERT INTO applications (job_seeker_id, job_id) VALUES (?, ?)");
        $stmt->execute([$seeker_id, $job_id]);
        $success = "Application submitted successfully! Redirecting...";
        header("Refresh:2;url=jobseeker-dashboard.php");
    }
} catch (PDOException $e) {
    $error = "Error: An error occurred while submitting your application.";
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Apply for Job - Job Portal</title>
    <link href="css/common.css" rel="stylesheet">
    <link href="css/apply.css" rel="stylesheet">
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
                <li><a href="jobseeker-dashboard.php">My Applications</a></li>
                <li><a href="contact.php#contact">Contact Us</a></li>
            </ul>
            <ul class="nav-links right">
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="apply-section">
        <div class="container">
            <h2>Apply for Job</h2>
            <p>Submitting your application...</p>
            <?php if (isset($success)): ?>
                <p class="message success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <p class="message error"><?php echo htmlspecialchars($error); ?></p>
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