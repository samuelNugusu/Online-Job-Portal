<?php
session_start();
include_once 'inc/db_connect.php';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

include_once 'inc/functions.php';

// Admin access control
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: login.php");
    exit();
}

//Fetch Users, Paginated
$results_per_page = 10;

try {

    $sqlTotalUsers = "SELECT COUNT(*) AS total FROM users";
    $resultTotalUsers = $conn->query($sqlTotalUsers);

    if ($resultTotalUsers) {
        $rowTotalUsers = $resultTotalUsers->fetch_assoc();
        $total_users = $rowTotalUsers['total'];
        $total_pages = ceil($total_users / $results_per_page);
    } else {
        throw new Exception("Error fetching total user count: " . $conn->error);
    }

    if (!isset($_GET['page'])) {
        $page = 1;
    } else {
        $page = (int)$_GET['page'];
        $page = max(1, min($page, $total_pages));
    }

    $start_from = ($page - 1) * $results_per_page;

    $sql = "SELECT * FROM users ORDER BY registration_date DESC LIMIT ?, ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ii", $start_from, $results_per_page);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        throw new Exception("Error preparing user selection statement: " . $conn->error);
    }

    //Handle User Deletion
    if (isset($_GET['delete_user']) && is_numeric($_GET['delete_user'])) {
        $user_id_to_delete = (int)$_GET['delete_user'];

        if ($user_id_to_delete === (int)$_SESSION['user_id']) {
            displayAlert("You cannot delete your own account.", "error");
        } else {
            $sqlDeleteUser = "DELETE FROM users WHERE user_id = ?";
            $stmtDeleteUser = $conn->prepare($sqlDeleteUser);

            if ($stmtDeleteUser) {
                $stmtDeleteUser->bind_param("i", $user_id_to_delete);
                if ($stmtDeleteUser->execute()) {
                    displayAlert("User deleted successfully.", "success");
                } else {
                    displayAlert("Error deleting user: " . $stmtDeleteUser->error, "error");
                }
                $stmtDeleteUser->close();
            }  else {
              throw new Exception("Error preparing user deletion statement: " . $conn->error);
            }
        }
    }

} catch (Exception $e) {
    error_log("Exception in admin_manage_users.php: " . $e->getMessage());
    displayAlert("An unexpected error occurred. Check the server logs.", "error");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
    <header>
        <h1>Admin - Manage Users</h1>
    </header>
    <div class="container">
        <h2>Manage Users</h2>
        <?php if (isset($alertMessage)) : ?>
            <div class="alert <?php echo $alertType; ?>"><?php echo $alertMessage; ?></div>
        <?php endif; ?>
        <p><a href="admin.php">Back to Admin Dashboard</a></p>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Registration Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($result) && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                            <td><?php echo formatDate($row['registration_date']); ?></td>
                            <td>
                                <a href="admin_manage_users.php?delete_user=<?php echo $row['user_id']; ?>"
                                   onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No users found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
      </div> <!-- End table-responsive -->
         <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<a href='admin_manage_users.php?page=" . $i . "'";
                if ($page == $i) echo " class='active'";
                echo ">" . $i . "</a> ";
            }
            ?>
        </div>
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

if (isset($conn)) {
  $conn->close();
}
?>