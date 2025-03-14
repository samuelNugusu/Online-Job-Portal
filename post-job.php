<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['employer_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $employer_id = $_SESSION['employer_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO jobs (employer_id, title, description, location) VALUES (?, ?, ?, ?)");
        $stmt->execute([$employer_id, $title, $description, $location]);
        $message = "Job posted successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post a Job - Job Portal</title>
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
                <li><a href="contact.html#contact">Contact Us</a></li>
            </ul>
            <ul class="nav-links right">
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="register-section">
        <div class="container">
            <h2>Post a Job</h2>
            <p>Fill out the details to list your job opening.</p>
            <?php if (isset($message)): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <form class="register-form" method="POST">
                <div class="form-group">
                    <label for="job-title">Job Title</label>
                    <input type="text" class="input-field" id="job-title" name="title" placeholder="Enter job title" required>
                </div>
                <div class="form-group">
                    <label for="description">Job Description</label>
                    <textarea class="input-field" id="description" name="description" placeholder="Describe the job" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="input-field" id="location" name="location" placeholder="Enter job location" required>
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
</body>
</html>