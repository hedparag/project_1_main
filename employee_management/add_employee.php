<?php
session_start();
include("include/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type_id'] !== '1') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = $_POST['employee_name'];
    $employee_email = $_POST['employee_email'];
    $employee_phone = $_POST['employee_phone'];
    $department_id = $_POST['department_id'];
    $status = isset($_POST['status']) ? 'true' : 'false';

    if (empty($employee_name) || empty($employee_email) || empty($employee_phone) || empty($department_id)) {
        $_SESSION['error'] = "All fields are required!";
    } else {
        $query = "INSERT INTO employees (employee_name, employee_email, employee_phone, department_id, status) VALUES ($1, $2, $3, $4, $5) RETURNING employee_id";
        $result = pg_query_params($conn, $query, array($employee_name, $employee_email, $employee_phone, $department_id, $status));

        if ($result) {
            $row = pg_fetch_assoc($result);
            $employee_id = $row['employee_id'];

            if ($status === 'true') {
                $default_password = password_hash("123456", PASSWORD_DEFAULT);
                $user_query = "INSERT INTO users (employee_id, username, email, password, user_type_id) VALUES ($1, $2, $3, $4, 2)";
                pg_query_params($conn, $user_query, array($employee_id, $employee_name, $employee_email, $default_password));
            }

            $_SESSION['success'] = "Employee added successfully!";
            header("Location: edit_profile.php");
            exit();
        } else {
            $_SESSION['error'] = "Failed to add employee.";
        }
    }
}

$dept_query = "SELECT * FROM departments";
$dept_result = pg_query($conn, $dept_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Employee</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Noumber</label>
                <input type="text" class="form-control" name="phone" required>
            </div>

            <div class="mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-control" name="department_id" required>
                    <option value="">Select Department</option>
                    <?php while ($dept = pg_fetch_assoc($dept_result)) { ?>
                        <option value="<?php echo $dept['department_id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="status" value="true">
                <label class="form-check-label">Approve this Employee</label>
            </div>

            <button type="submit" class="btn btn-primary">Add Employee</button>
            <a href="edit_profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
