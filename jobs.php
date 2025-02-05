<?php
include_once 'inc/db_connect.php';

// Pagination (adjust as needed)
$results_per_page = 10;
$query = "SELECT COUNT(*) AS total FROM jobs";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_pages = ceil($row["total"] / $results_per_page);

if (!isset($_GET['page'])) {
  $page = 1;
} else {
  $page = $_GET['page'];
}
$start_from = ($page - 1) * $results_per_page;


$sql = "SELECT * FROM jobs ORDER BY date_posted DESC LIMIT $start_from, $results_per_page";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Listings</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Job Listings</h1>

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='job-listing'>";
                echo "<h2>" . htmlspecialchars($row["title"]) . "</h2>";
                echo "<p>" . htmlspecialchars($row["location"]) . "</p>";
                echo "<p>" . substr(htmlspecialchars($row["description"]), 0, 200) . "...</p>"; //Short description
                echo "<a href='job_details.php?id=" . $row["job_id"] . "'>View Details</a>";
                echo "</div>";
            }
        } else {
            echo "<p>No jobs found.</p>";
        }
        ?>

        <!-- Pagination -->
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<a href='jobs.php?page=" . $i . "'";
                if ($page == $i) echo " class='active'";
                echo ">" . $i . "</a> ";
            }
            ?>
        </div>

    </div>
</body>
</html>