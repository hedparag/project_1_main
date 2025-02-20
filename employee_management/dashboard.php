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
    $query = "SELECT employee_id, employee_name, employee_email, employee_phone, salary, employee_details, employee_skills FROM employees WHERE employee_id = $1";
    $result = pg_query_params($conn, $query, array($employee_id));
    
    if ($result) {
        $userData = pg_fetch_assoc($result);
        $employee_name = $userData['employee_name'] ?? 'Not Available';
        $employee_email = $userData['employee_email'] ?? 'Not Available';
        $employee_phone = $userData['employee_phone'] ?? 'Not Available';
        $salary = $userData['salary'] ?? 'Not Available';
        $employee_details = $userData['employee_details'] ?? 'Not Available';
        $employee_skills = $userData['employee_skills'] ?? 'Not Available';
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
    <link rel="stylesheet" href="css/style.css">
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
        <h2 class="text-center">Welcome, <?php echo htmlspecialchars($employee_name); ?>!</h2>
        <div class="row">
        <div class="col-12 col-md-6 py-3 px-3 mt-5 m-auto">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h4 class="card-title text-center mb-3">Your Details</h4>
                    <hr>
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Employee ID:</th>
                                <td><?php echo htmlspecialchars($employee_id); ?></td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td><?php echo htmlspecialchars($employee_name); ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo htmlspecialchars($employee_email); ?></td>
                            </tr>
                            <tr>
                                <th>Phone Number:</th>
                                <td><?php echo htmlspecialchars($employee_phone); ?></td>
                            </tr>
                            <tr>
                                <th>Salary:</th>
                                <td>₹<?php echo htmlspecialchars($salary); ?></td>
                            </tr>
                            <tr>
                                <th>Details:</th>
                                <td><?php echo htmlspecialchars($employee_details); ?></td>
                            </tr>
                            <tr>
                                <th>Skills:</th>
                                <td><?php echo htmlspecialchars($employee_skills); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="text-center mt-3">
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>

            <div class="col-12 col-md-6 p-3 mt-5 text-center">
                <?php if ($user_type_id == 1): ?>
                <h3>Admin Panel</h3>
                <ul class="list-unstyled nav flex-column">
                    <li class="nav-item">
                        <a href="view_profiles.php" class="nav-link text-dark">View Profiles</a>
                    </li>
                    <li class="nav-item">
                        <a href="view_reports.php" class="nav-link text-dark">View Reports</a>
                    </li>
                    <li class="nav-item">
                        <a href="settings.php" class="nav-link text-dark">Settings</a>
                    </li>
                </ul>

                
                <h3 class="mt-4">Pending Employee Approvals</h3>
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
                                    <td><a href="approve_employee.php?employee_id=<?php echo htmlspecialchars($row['employee_id']); ?>&action=approve" class="btn btn-success">Approve</a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No employees pending approval.</p>
                <?php endif; ?>
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