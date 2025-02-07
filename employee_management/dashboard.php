<?php
session_start();
include("include/config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';
$user_type_id = $_SESSION['user_type_id'] ?? 4;

$query = "SELECT employee_id, user_type_id FROM users WHERE user_id = $1";
$result = pg_query_params($conn, $query, array($user_id));

if (!$result) {
    die("Query failed: " . pg_last_error($conn)); 
}

$row = pg_fetch_assoc($result);
$employee_id = $row['employee_id'] ?? null;
$user_type_id = $row['user_type_id'] ?? $user_type_id;

$employee_skills = 'Not Available';
$salary = 'Not Available';
if ($employee_id) {
    $query = "SELECT employee_name, employee_skills, salary FROM employees WHERE employee_id = $1";
    $result = pg_query_params($conn, $query, array($employee_id));
    
    if ($result) {
        $userData = pg_fetch_assoc($result);
        $employee_skills = $userData['employee_skills'] ?? 'Not Available';
        $salary = $userData['salary'] ?? 'Not Available';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container p-3">
        <h2 class="text-center mt-3">Welcome, <?php echo htmlspecialchars($user_name); ?></h2>
        <div class="row">
            <div class="col-12 col-md-6 py-3 px-3 mt-5 m-auto text-center">
                <p><strong>Your Skills:</strong> <?php echo htmlspecialchars($employee_skills); ?></p>
                <p><strong>Your Salary:</strong> <?php echo htmlspecialchars($salary); ?></p>
                <br>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
            <div class="col-12 col-md-6 py-3 px-3 mt-5 m-auto text-center">
                <?php if ($user_type_id == 1): ?>
                <h3>Admin Panel</h3>
                <ul>
                    <li><a href="approve_users.php">Approve Users</a></li>
                    <li><a href="edit_profile.php">Edit Profiles</a></li>
                    <li><a href="view_reports.php">View Reports</a></li>
                    <li><a href="settings.php">Settings</a></li>
                </ul>
                <div class="container mt-4">
                    <h2>Pending Employee Approvals</h2>
                    <?php
                    $query = "SELECT * FROM employees WHERE status = FALSE";
                    $result = pg_query($conn, $query);
                    if (pg_num_rows($result) > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = pg_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['employee_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['employee_email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['employee_phone']); ?></td>
                                        <td>
                                            <a href="approve_employee.php?employee_id=<?php echo $row['employee_id']; ?>" class="btn btn-success">Approve</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">No employees pending approval.</p>
                    <?php endif; ?>
                </div>
                <?php elseif ($user_type_id == 2): ?>
                <h3>Manager Panel</h3>
                <ul>
                    <li><a href="manage_employees.php">Manage Employees</a></li>
                    <li><a href="generate_reports.php">Generate Reports</a></li>
                </ul>
                <?php elseif ($user_type_id == 3): ?>
                <h3>Employee Dashboard</h3>
                <ul>
                    <li><a href="view_tasks.php">View Tasks</a></li>
                    <li><a href="submit_reports.php">Submit Reports</a></li>
                </ul>
                <?php else: ?>
                <h3>Intern Dashboard</h3>
                <ul>
                    <li><a href="learning_materials.php">Learning Materials</a></li>
                    <li><a href="submit_progress.php">Submit Progress</a></li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php pg_close($conn); ?>