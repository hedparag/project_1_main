<?php
session_start();
include("include/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type_id'] !== '1') {
    header("Location: login.php");
    exit();
}

$query = "SELECT e.employee_id, e.employee_name, e.employee_email, d.department_name, e.status 
          FROM employees e 
          JOIN departments d ON e.department_id = d.department_id 
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
<header>
        <nav class="navbar navbar-expand-lg custom-navbar px-4 border-bottom rounded-bottom fixed-top" style="background-color: #343a40;">
          <div class="container-fluid">
            <a class="navbar-brand fs-6" href="home.html"><h1>Fusion<span class="text-primary">Works</span></h1></a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fs-5 text-center">
                <li class="nav-item px-1">
                  <a class="nav-link" href="home.html">HOME</a>
                </li>
                <li class="nav-item px-1">
                  <a class="nav-link" href="register.php">REGISTER</a>
                </li>
                <li class="nav-item px-1">
                  <a class="nav-link" href="login.php">LOGIN</a>
                </li>
                <li class="nav-item px-1">
                  <a class="nav-link active text-primary" aria-current="page" href="dashboard.php">DASHBOARD</a>
                </li>
                <li class="nav-item px-1">
                  <a class="nav-link" href="logout.php">LOGOUT</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
      </header>
    <div class="container p-5 mt-5">
        <h2 class="mb-4 text-center">Manage Employees</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Status</th>
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
                        <?php echo ($row['status'] == 't') ? '<span class="badge bg-success">Approved</span>' : '<span class="badge bg-danger">Pending</span>'; ?>
                    </td>
                    <td>
                        <a href="edit_employee.php?id=<?php echo htmlspecialchars ($row['employee_id']); ?>" class="btn btn-warning">Edit Details</a>
                        <!-- <p>Debug: edit_employee.php?id=<?php echo htmlspecialchars($row['employee_id']); ?></p> -->

                        <?php if ($row['status'] === 't'): ?>
                          <a href="reset_password.php?id=<?php echo htmlspecialchars ($row['employee_id']); ?>" class="btn btn-primary">Reset Username & Password</a>
                        <?php else: ?>
                          <a href="reset_password.php?id=<?php echo htmlspecialchars ($row['employee_id']); ?>" class="btn btn-secondary disabled">Reset Username & Password</a>
                        <?php endif; ?>

                        <?php if ($row['status'] === 't'): ?>
                            <a href="approve_employee.php?employee_id=<?php echo htmlspecialchars($row['employee_id']); ?>&action=reject" class="btn btn-danger">Reject</a>
                        <?php else: ?>
                            <a href="approve_employee.php?employee_id=<?php echo htmlspecialchars($row['employee_id']); ?>&action=approve" class="btn btn-success">Approve</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
                <!-- <pre>
                    <?php print_r($data); ?>
                </pre> -->
            </tbody>
        </table>
        <a href="add_employee.php" class="btn btn-success m-3">Add New Employee</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
