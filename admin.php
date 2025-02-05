<?php
session_start();
include_once 'inc/db_connect.php';
include_once 'inc/functions.php';

// Check if the admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
    </header>
    <div class="container">
        <h2>Welcome, Administrator</h2>
         <div class="admin-summary">
            <div class="admin-summary-item">
              <?php
              // Count Total Users
              $sqlTotalUsers = "SELECT COUNT(*) as total FROM users";
              $resultTotalUsers = $conn->query($sqlTotalUsers);
              $totalUsers = $resultTotalUsers->fetch_assoc()['total'] ?? 0; //Handle if query fails

               ?>
              <h3>Total Users</h3>
              <p><?php echo htmlspecialchars($totalUsers); ?></p>
            </div>
             <div class="admin-summary-item">
              <?php
                //Count Total Jobs
                $sqlTotalJobs = "SELECT COUNT(*) as total FROM jobs";
                $resultTotalJobs = $conn->query($sqlTotalJobs);
                $totalJobs = $resultTotalJobs->fetch_assoc()['total'] ?? 0; //Handle if query fails
               ?>
              <h3>Total Jobs</h3>
              <p><?php echo htmlspecialchars($totalJobs); ?></p>
            </div>
          </div>

        <ul>
            <li><a href="admin_manage_jobs.php">Manage Job Postings</a></li>
            <li><a href="admin_manage_users.php">Manage Users</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <footer>
        <p>© 2024 My Job Portal</p>
    </footer>
</body>
</html>