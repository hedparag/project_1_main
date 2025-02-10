<?php
session_start();
include("include/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type_id'] !== '1') {
    header("Location: login.php");
    exit();
}

$employee_id = $_GET['id'];
$error = "";
$success = "";

$query = "SELECT * FROM employees WHERE employee_id = $1";
$result = pg_query_params($conn, $query, array($employee_id));

if (!$result || pg_num_rows($result) == 0) {
    $error = "Employee not found.";
} else {
    $employee = pg_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $department_id = $_POST['department_id'];
    $position_id = $_POST['position_id'];

    $update_query = "UPDATE employees SET full_name = $1, email = $2, department_id = $3, position_id = $4 WHERE employee_id = $5";
    $update_result = pg_query_params($conn, $update_query, array($full_name, $email, $department_id, $position_id, $employee_id));

    if ($update_result) {
        $success = "Employee details updated successfully!";
    } else {
        $error = "Failed to update employee details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Employee</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($employee['employee_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($employee['employee_email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Department</label>
            <input type="number" name="department_id" class="form-control" value="<?php echo htmlspecialchars($employee['department_id']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="number" name="position_id" class="form-control" value="<?php echo htmlspecialchars($employee['position_id']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="edit_profile.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
