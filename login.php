<?php
session_start();
include_once 'inc/db_connect.php';
include_once 'inc/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
      displayAlert("Invalid Request", "error");
    } else {
        $username = sanitizeInput($_POST["username"]);
        $password = $_POST["password"];

        // Validate input
        $errors = [];

        if (empty($username)) {
            $errors['username'] = "Username is required.";
        }

        if (empty($password)) {
            $errors['password'] = "Password is required.";
        }

        if (empty($errors)) {
            $sql = "SELECT user_id, username, password, user_type FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();

                if (password_verify($password, $row["password"])) {
                    $_SESSION["user_id"] = $row["user_id"];
                    $_SESSION["username"] = $row["username"];
                    $_SESSION["user_type"] = $row["user_type"];

                    if ($row["user_type"] === "admin") {
                        header("Location: admin.php");
                        exit();
                    } elseif ($row["user_type"] === "employer") {
                        header("Location: profile.php");
                        exit();
                    } else {
                        header("Location: index.php");
                        exit();
                    }
                } else {
                    displayAlert("Invalid username or password.", "error");
                }
            } else {
                displayAlert("Invalid username or password.", "error");
            }
            $stmt->close();
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
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="js/script.js"></script>
</head>
<body>
    <header>
        <h1>My Job Portal</h1>
    </header>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($alertMessage)) : ?>
            <div class="alert <?php echo $alertType; ?>"><?php echo $alertMessage; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
             <?php if (isset($errors['username'])): ?>
                 <span class="error-message"><?php echo $errors['username']; ?></span>
             <?php endif; ?>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
             <?php if (isset($errors['password'])): ?>
                 <span class="error-message"><?php echo $errors['password']; ?></span>
             <?php endif; ?>

            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
    <footer>
        <p>© 2024 My Job Portal</p>
    </footer>
</body>
</html>