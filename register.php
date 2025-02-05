<?php
session_start();
include_once 'inc/db_connect.php';
include_once 'inc/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    displayAlert("Invalid Request", "error");
  } else {
    $username = sanitizeInput($_POST["username"]);
    $email = sanitizeInput($_POST["email"]);
    $password = $_POST["password"];
    $user_type = sanitizeInput($_POST["user_type"]);

    // Validate input
    $errors = [];

    if (empty($username)) {
        $errors['username'] = "Username is required.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    }

    if (empty($user_type)) {
        $errors['user_type'] = "User type is required.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username or email already exists
        $check_sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            displayAlert("Username or email already exists.", "error");
        } else {
            $insert_sql = "INSERT INTO users (username, email, password, user_type, registration_date) VALUES (?, ?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $username, $email, $hashed_password, $user_type);

            if ($insert_stmt->execute()) {
                displayAlert("Registration successful! Please log in.", "success");
                header("Location: login.php");
                exit();
            } else {
                displayAlert("Registration failed: " . $insert_stmt->error, "error");
            }

            $insert_stmt->close();
        }
        $check_stmt->close();
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
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="js/script.js"></script>
</head>
<body>
    <header>
        <h1>My Job Portal</h1>
    </header>
    <div class="container">
        <h2>Register</h2>
        <?php if (isset($alertMessage)) : ?>
            <div class="alert <?php echo $alertType; ?>"><?php echo $alertMessage; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
             <?php if (isset($errors['username'])): ?>
                 <span class="error-message"><?php echo $errors['username']; ?></span>
             <?php endif; ?>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
             <?php if (isset($errors['email'])): ?>
                 <span class="error-message"><?php echo $errors['email']; ?></span>
             <?php endif; ?>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
             <?php if (isset($errors['password'])): ?>
                 <span class="error-message"><?php echo $errors['password']; ?></span>
             <?php endif; ?>

            <label for="user_type">User Type:</label>
            <select id="user_type" name="user_type" required>
                <option value="">Select User Type</option>
                <option value="jobseeker" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'jobseeker') ? 'selected' : ''; ?>>Job Seeker</option>
                <option value="employer" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'employer') ? 'selected' : ''; ?>>Employer</option>
            </select>
             <?php if (isset($errors['user_type'])): ?>
                 <span class="error-message"><?php echo $errors['user_type']; ?></span>
             <?php endif; ?>

            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
    <footer>
        <p>© 2024 My Job Portal</p>
    </footer>
</body>
</html>