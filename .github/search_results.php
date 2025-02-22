<?php
session_start();
include_once 'inc/db_connect.php';
include_once 'inc/functions.php';

// Get search keywords and location from the GET request
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';

// Sanitize the input
$keyword = sanitizeInput($keyword);
$location = sanitizeInput($location);

// Build the SQL query
$sql = "SELECT jobs.*, categories.category_name
        FROM jobs
        INNER JOIN categories ON jobs.category = categories.category_id
        WHERE jobs.is_active = 1";

$params = [];
$types = "";

if (!empty($keyword)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $keywordParam = "%" . $keyword . "%";
    $params[] = $keywordParam;
    $params[] = $keywordParam;
    $types .= "ss";
}

if (!empty($location)) {
    $sql .= " AND location LIKE ?";
    $locationParam = "%" . $location . "%";
    $params[] = $locationParam;
    $types .= "s";
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    // Use reflection to dynamically bind parameters
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="js/script.js"></script>
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
        <h2>Search Results</h2>

        <?php
        if ($result->num_rows > 0) {
            echo "<div class='job-list'>";
            while($row = $result->fetch_assoc()) {
                echo "<div class='job-listing'>";
                echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                echo "<p>" . htmlspecialchars($row["location"]) . "</p>";
                echo "<p>Category: " . htmlspecialchars($row["category_name"]) . "</p>";
                echo "<p>" . truncateText(htmlspecialchars($row["description"]), 200) . "</p>";
                echo "<a href='job_details.php?id=" . $row["job_id"] . "'>View Details</a>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<p>No jobs found matching your search criteria.</p>";
        }
        ?>

        <a href="index.php">Back to Home</a>
    </div>
    <footer>
        <p>© 2024 My Job Portal</p>
    </footer>
</body>
</html>