<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle autocomplete request
if (isset($_GET['autocomplete']) && strlen($_GET['autocomplete']) === 1 && ctype_alpha($_GET['autocomplete'])) {
    try {
        require_once 'includes/db.php'; // Include db.php here for autocomplete
        $letter = $_GET['autocomplete'];
        $stmt = $pdo->prepare("SELECT title FROM jobs WHERE title LIKE ? ORDER BY posted_at DESC LIMIT 10");
        $stmt->execute(["$letter%"]);
        $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($suggestions);
        exit;
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// Start session for main request
session_start();
require_once 'includes/db.php';

// Debug: Check database connection
try {
    $pdo->query("SELECT 1");
    echo "<!-- Database connection successful -->";
} catch (PDOException $e) {
    echo "<!-- Database connection failed: " . $e->getMessage() . " -->";
}

// Initialize variables
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$jobs = [];
$limit = 1000; // Maximum number of jobs to display

try {
    // Debug: Log the query being executed
    if ($keyword !== '') {
        if (strlen($keyword) === 1 && ctype_alpha($keyword)) {
            $query = "SELECT * FROM jobs WHERE title LIKE ? ORDER BY posted_at DESC LIMIT $limit";
            $stmt = $pdo->prepare($query);
            $stmt->execute(["$keyword%"]);
            echo "<!-- Executing query: $query with keyword $keyword -->";
        } else {
            $query = "SELECT * FROM jobs WHERE title LIKE ? OR location LIKE ? ORDER BY posted_at DESC LIMIT $limit";
            $stmt = $pdo->prepare($query);
            $stmt->execute(["%$keyword%", "%$keyword%"]);
            echo "<!-- Executing query: $query with keyword $keyword -->";
        }
    } else {
        $query = "SELECT * FROM jobs ORDER BY posted_at DESC LIMIT $limit";
        $stmt = $pdo->query($query);
        echo "<!-- Executing query: $query -->";
    }
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<!-- Found " . count($jobs) . " jobs -->";
} catch (PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
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
            <form class="search-form" action="search.php" method="GET">
                <div class="search-wrapper">
                    <input type="text" class="input-field" placeholder="Enter a letter or keyword" name="keyword" id="search-input" value="<?php echo htmlspecialchars($keyword); ?>" autocomplete="off">
                    <button type="submit" class="btn search-btn">Search</button>
                    <div id="autocomplete-dropdown" class="autocomplete-dropdown"></div>
                </div>
            </form>
        </div>
    </section>

    <section class="jobs-section">
        <div class="container">
            <div class="grid">
                <?php if (empty($jobs)): ?>
                    <p>No jobs available at the moment.</p>
                <?php else: ?>
                    <?php if ($keyword !== '' && empty($jobs)): ?>
                        <p>No jobs found matching "<?php echo htmlspecialchars($keyword); ?>".</p>
                    <?php endif; ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="grid-item">
                            <div class="card">
                                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                <p><?php echo htmlspecialchars($job['description']); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                                <p><strong>Posted:</strong> <?php echo date('F j, Y', strtotime($job['posted_at'])); ?></p>
                                <?php if (isset($_SESSION['seeker_id'])): ?>
                                    <a href="apply.php?job_id=<?php echo $job['id']; ?>"><button class="btn">Apply Now</button></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($jobs) >= $limit): ?>
                        <p>Showing up to <?php echo $limit; ?> jobs. More may be available.</p>
                    <?php endif; ?>
                <?php endif; ?>
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
            const searchInput = document.getElementById('search-input');
            const autocompleteDropdown = document.getElementById('autocomplete-dropdown');

            searchInput.addEventListener('input', function() {
                const keyword = this.value.trim();
                if (keyword.length === 1 && /[a-zA-Z]/.test(keyword)) {
                    fetch(`search.php?autocomplete=${keyword}`)
                        .then(response => response.json())
                        .then(data => {
                            autocompleteDropdown.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(job => {
                                    const div = document.createElement('div');
                                    div.className = 'autocomplete-item';
                                    div.textContent = job.title;
                                    div.addEventListener('click', function() {
                                        searchInput.value = job.title;
                                        autocompleteDropdown.style.display = 'none';
                                        searchInput.form.submit();
                                    });
                                    autocompleteDropdown.appendChild(div);
                                });
                                autocompleteDropdown.style.display = 'block';
                            } else {
                                autocompleteDropdown.style.display = 'none';
                            }
                        })
                        .catch(error => console.error('Error fetching autocomplete data:', error));
                } else {
                    autocompleteDropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !autocompleteDropdown.contains(e.target)) {
                    autocompleteDropdown.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>