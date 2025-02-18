<?php
session_start();
include("include/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type_id'] !== '1') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Employee ID is required.";
    header("Location: edit_profile.php");
    exit();
}

$employee_id = $_GET['id'];

$query = "SELECT * FROM employees WHERE employee_id = $1";
$result = pg_query_params($conn, $query, array($employee_id));

if (pg_num_rows($result) == 0) {
    $_SESSION['error'] = "Employee not found.";
    header("Location: edit_profile.php");
    exit();
}

$employee = pg_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'], $_POST['confirm_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "Password fields cannot be empty.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
    } else {
        $password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_query = "UPDATE users SET password = $1 WHERE employee_id = $2";
        $update_result = pg_query_params($conn, $update_query, array($password, $employee_id));

        if ($update_result) {
            $_SESSION['success'] = "Password reset successfully!";
            header("Location: edit_profile.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to reset password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container p-5">
        <h2>Reset Password for <?php echo htmlspecialchars($employee['employee_name']); ?></h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
            <a href="edit_profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
