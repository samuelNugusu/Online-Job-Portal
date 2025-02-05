<?php
session_start();
include_once 'inc/db_connect.php';
include_once 'inc/functions.php';

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$job_id = (int)$_GET["id"];

$sql = "SELECT jobs.*, users.username AS employer_name, categories.category_name
        FROM jobs
        INNER JOIN users ON jobs.employer_id = users.user_id
        INNER JOIN categories ON jobs.category = categories.category_id
        WHERE jobs.job_id = ? AND jobs.is_active = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: index.php");
    exit();
}

$job = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_job'])) {
  if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    displayAlert("Invalid Request", "error");
  }
  if(!isset($_SESSION['user_id'])) {
    displayAlert("You must be logged in to apply for this job.", "error");
  } else {

    $cover_letter = sanitizeInput($_POST['cover_letter']);
    $user_id = $_SESSION['user_id'];
    $apply_date = date("Y-m-d H:i:s");

    $sqlInsertApplication = "INSERT INTO applications (job_id, user_id, application_date, cover_letter) VALUES (?, ?, ?, ?)";
    $stmtInsertApplication = $conn->prepare($sqlInsertApplication);
    $stmtInsertApplication->bind_param("iiss", $job_id, $user_id, $apply_date, $cover_letter);

    if($stmtInsertApplication->execute()) {
        displayAlert("Application submitted successfully!", "success");
    } else {
        displayAlert("Error submitting application. Please try again.", "error");
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
    <title><?php echo htmlspecialchars($job["title"]); ?> - Job Details</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="js/script.js"></script>
    <script src="js/functions.js"></script>
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
        <div class="job-details">
            <h2><?php echo htmlspecialchars($job["title"]); ?></h2>
            <p><strong>Employer:</strong> <?php echo htmlspecialchars($job["employer_name"]); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($job["location"]); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($job["category_name"]); ?></p>
            <p><strong>Salary:</strong> <?php echo htmlspecialchars($job["salary"]); ?></p>
            <p><strong>Date Posted:</strong> <?php echo formatDate($job["date_posted"]); ?></p>

            <h3>Job Description</h3>
            <p><?php echo nl2br(htmlspecialchars($job["description"])); ?></p>

            <!-- Application Form (Conditionally displayed if logged in) -->
            <?php if (isset($_SESSION["user_id"])): ?>
                <h3>Apply for this Job</h3>
                <?php displayAlert($alertMessage ?? "", $alertType ?? "info") ?>
                <form method="POST" action="">
                    <label for="cover_letter">Cover Letter:</label>
                    <textarea id="cover_letter" name="cover_letter" rows="5" required></textarea>
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
                    <button type="submit" name="apply_job">Submit Application</button>
                </form>

            <?php else: ?>
                <p>You must be <a href="login.php">logged in</a> to apply for this job.</p>
            <?php endif; ?>

            <a href="index.php">Back to Job Listings</a>
        </div>

        <!-- Related Jobs (Example - You'll need to adapt this based on your data) -->
        <section class="related-jobs">
            <h3>Related Jobs</h3>
            <p>(This section is just a placeholder)</p>
        </section>
    </div>
    <footer>
        <p>© 2024 My Job Portal</p>
    </footer>
</body>
</html>

<?php
if (isset($stmt)) {
    $stmt->close();
}
if (isset($stmtInsertApplication)) {
    $stmtInsertApplication->close();
}
if (isset($conn)) {
    $conn->close();
}
?>