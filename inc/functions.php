<?php
// Sanitize user input (prevent XSS)
function sanitizeInput($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Format a date
function formatDate($date) {
    return date("F j, Y", strtotime($date));
}

function generateCSRFToken() {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // 64 character hex string
  }
  return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
  if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    return false;
  }
  unset($_SESSION['csrf_token']); // Use token once
  return true;
}

function truncateText($text, $length = 100, $ending = '...', $htmlOK = false) {
  if (strlen($text) > $length) {
    if ($htmlOK) {
      $text = preg_replace('/(<[^>]*)$/', " ...", substr($text, 0, $length));
    } else {
      $text = substr($text, 0, $length);
    }
    $text = trim($text);
    $lastSpace = strrpos($text, ' ');
    $text = substr($text, 0, $lastSpace);
    $text .= $ending;
  }
  return $text;
}

function displayAlert($message, $type = 'info') {
    $class = '';
    switch ($type) {
        case 'success':
            $class = 'alert-success';
            break;
        case 'error':
            $class = 'alert-danger';
            break;
        case 'warning':
            $class = 'alert-warning';
            break;
        default:
            $class = 'alert-info';
    }
    echo '<div class="alert ' . $class . '">' . $message . '</div>';
}

//Get Category Name from Id
function getCategoryName($category_id, $conn) {
  $sqlCategory = "SELECT category_name FROM categories WHERE category_id = ?";
  $stmtCategory = $conn->prepare($sqlCategory);
  $stmtCategory->bind_param("i", $category_id);
  $stmtCategory->execute();
  $resultCategory = $stmtCategory->get_result();

  if($resultCategory->num_rows === 1) {
    $category = $resultCategory->fetch_assoc();
    return htmlspecialchars($category['category_name']);
  } else {
    return "Uncategorized"; // Or handle differently
  }
}
?>