<?php
session_start();

include_once 'inc/db_connect.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

include_once 'inc/functions.php';

// Check if user is logged in and is an employer
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "employer") {
    header("Location: login.php");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if(!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    displayAlert("Invalid Request", "error");
  } else {

    $title = sanitizeInput($_POST["title"]);
    $description = $_POST["description"];
    $location = sanitizeInput($_POST["location"]);
    $salary = sanitizeInput($_POST["salary"]);
    $closing_date = sanitizeInput($_POST["closing_date"]);
    $category = sanitizeInput($_POST["category"]);

    //Get the employer's user id from the session
    $employer_id = $_SESSION["user_id"];

    //Server-side validation
    $errors = [];

    if (empty($title)) {
        $errors['title'] = "Job title is required.";
    }

    if (empty($description)) {
        $errors['description'] = "Job description is required.";
    }

    if (empty($location)) {
        $errors['location'] = "Location is required.";
    }

    if (empty($closing_date)) {
        $errors['closing_date'] = "Closing date is required.";
    }

    if (empty($category)) {
        $errors['category'] = "Category is required.";
    }

    if (empty($errors)) {
        //Use Try Catch Block
        try {
          $sql = "INSERT INTO jobs (employer_id, title, description, location, salary, closing_date, category, date_posted, is_active, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 1, 0)";
          $stmt = $conn->prepare($sql);

          if ($stmt) { // Check if prepare was successful
             $stmt->bind_param("issssss", $employer_id, $title, $description, $location, $salary, $closing_date, $category);
             if ($stmt->execute()) {
                  displayAlert("Job posted successfully!", "success");
              } else {
                   displayAlert("Error posting job: " . $stmt->error, "error");
                   error_log("SQL error posting job: " . $stmt->error);  //Log the error
              }
              $stmt->close();
          } else {
              throw new Exception("Error preparing job posting statement: " . $conn->error);
          }

        } catch (Exception $e) {
          displayAlert("An unexpected error occurred", "error");
          error_log("Exception posting job: " . $e->getMessage()); //Log
        }


    } else {
      displayAlert("Please correct the errors in the form", "error");
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
    <title>Post a Job</title>
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
        <h2>Post a Job</h2>
        <?php if (isset($alertMessage)) : ?>
            <div class="alert <?php echo isset($alertType) ? $alertType : 'info'; ?>"><?php echo $alertMessage; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="title">Job Title:</label>
            <input type="text" id="title" name="title" required value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
             <?php if (isset($errors['title'])): ?>
                 <span class="error-message"><?php echo $errors['title']; ?></span>
             <?php endif; ?>

            <label for="description">Job Description:</label>
            <textarea id="description" name="description" rows="5" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
             <?php if (isset($errors['description'])): ?>
                 <span class="error-message"><?php echo $errors['description']; ?></span>
             <?php endif; ?>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>">
             <?php if (isset($errors['location'])): ?>
                 <span class="error-message"><?php echo $errors['location']; ?></span>
             <?php endif; ?>

            <label for="salary">Salary:</label>
            <input type="text" id="salary" name="salary" value="<?php echo isset($_POST['salary']) ? htmlspecialchars($_POST['salary']) : ''; ?>">

            <label for="closing_date">Closing Date:</label>
            <input type="date" id="closing_date" name="closing_date" required value="<?php echo isset($_POST['closing_date']) ? htmlspecialchars($_POST['closing_date']) : ''; ?>">
             <?php if (isset($errors['closing_date'])): ?>
                 <span class="error-message"><?php echo $errors['closing_date']; ?></span>
             <?php endif; ?>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">Select Category</option>
                <?php
                // Fetch categories from the database
                $sqlCategories = "SELECT category_id, category_name FROM categories";
                try {
                    $resultCategories = $conn->query($sqlCategories);

                    if ($resultCategories) {
                        if ($resultCategories->num_rows > 0) {
                            while($categoryRow = $resultCategories->fetch_assoc()) {
                                $selected = (isset($_POST['category']) && $_POST['category'] == $categoryRow['category_id']) ? 'selected' : '';
                                echo "<option value='" . $categoryRow['category_id'] . "' " . $selected . ">" . htmlspecialchars($categoryRow['category_name']) . "</option>";
                            }
                        } else {
                            echo "<option value=''>No categories found</option>";
                        }
                        $resultCategories->free_result();
                    } else {
                        //Handle the SQL Error
                        echo "<option value=''>Error Fetching Categories</option>";
                         error_log("SQL error fetching categories:" . $conn->error); // Log the error
                    }

                } catch (Exception $e) {
                   echo "<option value=''>Error Fetching Categories</option>";
                   error_log("Exception fetching categories:" . $e->getMessage());
                }
                ?>
            </select>
             <?php if (isset($errors['category'])): ?>
                 <span class="error-message"><?php echo $errors['category']; ?></span>
             <?php endif; ?>

            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
            <button type="submit">Post Job</button>
        </form>
        <p><a href="profile.php">Back to Profile</a></p>
    </div>
    <footer>
        <p>© 2024 My Job Portal</p>
    </footer>
</body>
</html>
<?php

if (isset($conn)) {
  $conn->close();
}
?>