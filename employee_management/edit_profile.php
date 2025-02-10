<?php
session_start();
include("include/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type_id'] !== '1') {
    header("Location: login.php");
    exit();
}

$query = "SELECT e.employee_id, e.employee_name, e.employee_email, d.department_name 
          FROM employees e 
          JOIN departments d ON e.department_id = d.department_id 
          WHERE e.status = 'true'
          ORDER BY e.employee_id ASC";

$result = pg_query($conn, $query);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Manage Employees</h2>
        <a href="add_employee.php" class="btn btn-success mb-3">Add Employee</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = pg_fetch_assoc($result)) { $data[] = $row; ?>
                    <tr>
            <td><?php echo htmlspecialchars($row['employee_id']); ?></td>
            <td><?php echo htmlspecialchars($row['employee_name']); ?></td>
            <td><?php echo htmlspecialchars($row['employee_email']); ?></td>
            <td><?php echo htmlspecialchars($row['department_name']); ?></td>
            <td>
                <a href="edit_employee.php?id=<?php echo htmlspecialchars ($row['employee_id']); ?>" class="btn btn-warning">Edit</a>
                <a href="reset_password.php?id=<?php echo htmlspecialchars ($row['employee_id']); ?>" class="btn btn-primary">Reset Password</a>
                <a href="delete_employee.php?id=<?php echo htmlspecialchars ($row['employee_id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
                <?php } ?>
                <pre>
                <!-- <?php print_r($data); ?> -->
                </pre>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
