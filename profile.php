<?php
session_start();
include_once 'inc/db_connect.php';
include_once 'inc/functions.php';

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$user_type = $_SESSION["user_type"];

// Fetch user profile information from the database
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Error: User not found.";
    exit();
}

$user = $result->fetch_assoc();

//Handle Profile Update
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
  if(!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    displayAlert("Invalid Request", "error");
  } else {
    $new_email = sanitizeInput($_POST['email']);
    $errors = [];

    if (empty($new_email)) {
      $errors['email'] = "Email is required";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($errors)) {
      $updateSql = "UPDATE users SET email = ? WHERE user_id = ?";
      $updateStmt = $conn->prepare($updateSql);
      $updateStmt->bind_param("si", $new_email, $user_id);

      if($updateStmt->execute()) {
        displayAlert("Profile updated successfully!", "success");
        $user['email'] = $new_email; //Update in local
        $_SESSION['email'] = $new_email;  //Update in Session
      } else {
        displayAlert("Error updating profile: " . $updateStmt->error, "error");
      }
    }
  }
}

$csrf_token = generateCSRFToken();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>My Profile</h2>
        <?php if (isset($alertMessage)) : ?>
            <div class="alert <?php echo $alertType; ?>"><?php echo $alertMessage; ?></div>
        <?php endif; ?>

        <p>Username: <?php echo htmlspecialchars($user["username"]); ?></p>
        <p>Email: <?php echo htmlspecialchars($user["email"]); ?></p>
        <p>User Type: <?php echo htmlspecialchars(ucfirst($user["user_type"])); ?></p>

        <h3>Update Profile</h3>
        <form method="POST" action="">
            <label for="email">New Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" required>
             <?php if (isset($errors['email'])): ?>
                 <span class="error-message"><?php echo $errors['email']; ?></span>
             <?php endif; ?>

            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <?php if ($user_type === "employer"): ?>
            <h3>My Job Postings</h3>
            <?php
                $employer_id = $_SESSION['user_id'];
                $sqlJobs = "SELECT * FROM jobs WHERE employer_id = ?";
                $stmtJobs = $conn->prepare($sqlJobs);
                $stmtJobs->bind_param("i", $employer_id);
                $stmtJobs->execute();
                $resultJobs = $stmtJobs->get_result();

                if($resultJobs->num_rows > 0) {
                  echo "<div class='job-list'>";
                  while($job = $resultJobs->fetch_assoc()) {
                    echo "<div class='job-item'>";
                    echo "<h4>" . htmlspecialchars($job["title"]) . "</h4>";
                    echo "<p>Location: " . htmlspecialchars($job["location"]) . "</p>";
                    echo "<p><a href='job_details.php?id=" . $job["job_id"] . "'>View Details</a> | <a href='edit_job.php?id=" . $job["job_id"] . "'>Edit</a></p>";  //Add the edit function
                    echo "</div>";
                  }
                  echo "</div>";

                } else {
                  echo "<p>No jobs posted yet.</p>";
                }
                $stmtJobs->close();
            ?>
            <p><a href="post_job.php">Post a New Job</a></p>

        <?php elseif ($user_type === "jobseeker"): ?>
            <h3>Saved Jobs (Example)</h3>
            <p> (Feature Not Fully Implemented)</p>

        <?php endif; ?>
    </div>
    <footer>
        <p>© 2024 My Job Portal</p>
    </footer>
</body>
</html>