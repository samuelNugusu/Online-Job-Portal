<?php
session_start();
require_once 'includes/db.php';
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job Portal - Find Your Dream Job</title>
    <link href="css/common.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/future.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar" id="insidenav">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">Job Portal</a>
            <button class="nav-toggle">☰</button>
            <ul class="nav-links">
                <li><a href="#main">Home</a></li>
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
                        <a href="#" class="dropdown-toggle">Register</a>
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

    <!-- Hero/Search Section -->
    <section id="main" class="search-section">
        <div class="search-jumbotron">
            <h1>Find Your Dream Job</h1>
            <p>Explore thousands of job opportunities from top companies worldwide.</p>
            <form class="search-form" action="search.php" method="GET">
                <input type="text" class="input-field" placeholder="Enter job title or location" name="keyword">
                <button type="submit" class="btn search-btn">Search Jobs</button>
            </form>
        </div>
    </section>

    <!-- Secondary Call-to-Action -->
    <section class="cta-section">
        <p>Are you an employer looking to hire top talent?</p>
        <a href="post-job.php" class="cta-btn">Post a Job Now</a>
    </section>

    <!-- Include Future Jobs Section -->
    <section class="future-jobs-section">
        <?php include 'future-jobs.php'; ?>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <a href="#insidenav" title="To Top">↑</a>
        <p>Job Portal © 2025</p>
    </footer>

    <script src="js/scripts.js"></script>
    <script>
        // Toggle mobile menu
        document.addEventListener('DOMContentLoaded', function() {
            const navToggle = document.querySelector('.nav-toggle');
            const navLinks = document.querySelector('.nav-links');

            navToggle.addEventListener('click', function() {
                navLinks.classList.toggle('active');
            });

            // Smooth scroll for "To Top" link
            document.querySelector('.footer a').addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Debug: Confirm search button click
            document.querySelector('.search-btn').addEventListener('click', function(e) {
                console.log('Search button clicked');
            });
        });
    </script>
</body>
</html>