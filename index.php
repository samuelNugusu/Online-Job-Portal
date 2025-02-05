<?php
session_start();
include_once 'inc/db_connect.php';
include_once 'inc/functions.php';

// Check the database connection
if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error()); //Fatal Error
}

//Pagination Settings
$results_per_page = 10;
$sqlTotalJobs = "SELECT COUNT(*) AS total FROM jobs WHERE is_active = 1";
$resultTotalJobs = $conn->query($sqlTotalJobs);

$rowTotalJobs = $resultTotalJobs->fetch_assoc();
$total_jobs = $rowTotalJobs['total'];

$total_pages = ceil($total_jobs / $results_per_page);

if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = (int)$_GET['page']; //Sanitize the page number
    $page = max(1, min($page, $total_pages)); //Ensuring the page number is within the valid range
}

$start_from = ($page - 1) * $results_per_page;

//SQL query for listing jobs

$sql = "SELECT jobs.*, categories.category_name
FROM jobs
INNER JOIN categories ON jobs.category = categories.category_id
WHERE jobs.is_active = 1
ORDER BY jobs.date_posted DESC
LIMIT ?, ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $start_from, $results_per_page); //Integer params
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Find Your Dream Job</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/components.css">
    <script src="js/script.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/ui.js"></script>
    <style>
    /* Basic CSS for homepage layout */
    .featured-jobs {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-around;
      margin-top: 20px;
    }

    .job-card {
      width: 300px;
      border: 1px solid #ccc;
      padding: 15px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }

    .job-card h3 {
      margin-bottom: 10px;
    }

    .job-card p {
      margin-bottom: 5px;
    }

    .job-card a {
      display: inline-block;
      padding: 8px 15px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
  </style>
</head>
<body>
    <header>
        <h1>My Job Portal</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if (isset($_SESSION["user_id"])): ?>
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <section id="featured-jobs">
            <h2>Featured Jobs</h2>
            <div class="featured-jobs">
                <?php
                //Fetch Featured Jobs From DB (Example: Jobs with highlight=true)
                $sqlFeatured = "SELECT jobs.*, categories.category_name FROM jobs INNER JOIN categories ON jobs.category = categories.category_id WHERE jobs.is_active=1 AND jobs.is_featured = 1 ORDER BY jobs.date_posted DESC LIMIT 5";
                $resultFeatured = $conn->query($sqlFeatured);

                if ($resultFeatured->num_rows > 0) {
                    while($row = $resultFeatured->fetch_assoc()) {
                        echo "<div class='job-card'>";
                        echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                        echo "<p><strong>Location:</strong> " . htmlspecialchars($row["location"]) . "</p>";
                        echo "<p><strong>Category:</strong> " . htmlspecialchars($row["category_name"]) . "</p>";
                        echo "<p>" . truncateText(htmlspecialchars($row["description"]), 100) . "...</p>";
                        echo "<a href='job_details.php?id=" . $row["job_id"] . "'>View Details</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No featured jobs found.</p>";
                }
                ?>
            </div>
        </section>

        <section id="search">
            <h2>Search for Jobs</h2>
            <form action="search_results.php" method="GET">
                <input type="text" name="keyword" placeholder="Enter keyword...">
                <input type="text" name="location" placeholder="Enter location...">
                <button type="submit">Search</button>
            </form>
        </section>

        <section id="recent-jobs">
            <h2>Recent Jobs</h2>
            <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='job-listing'>";
                        echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                        echo "<p>" . htmlspecialchars($row["location"]) . "</p>";
                        echo "<p>Category: " . htmlspecialchars($row["category_name"]) . "</p>";
                        echo "<p>" . truncateText(htmlspecialchars($row["description"]), 200) . "</p>";
                        echo "<a href='job_details.php?id=" . $row["job_id"] . "'>View Details</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No jobs found.</p>";
                }
            ?>
        </section>

        <!-- Pagination -->
        <div class="pagination">
            <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<a href='index.php?page=" . $i . "'";
                    if ($page == $i) echo " class='active'";
                    echo ">" . $i . "</a> ";
                }
            ?>
        </div>
         <button id="showAlertButton">Show Sample Alert</button>
    </div>

    <footer>
        <p>© 2025 My Job Portal</p>
    </footer>
</body>
</html>

<?php
if (isset($stmt)) {
  $stmt->close();
}
if (isset($conn)) {
  $conn->close();
}
?>