<?php
include("include/config.php");

$errors = [];
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = trim($_POST['employee_name']);
    $employee_email = trim($_POST['employee_email']);
    $employee_phone = trim($_POST['employee_phone']);
    $salary = trim($_POST['salary']);
    $employee_details = trim($_POST['employee_details']);
    $employee_skills = trim($_POST['employee_skills']);
    $dob = trim($_POST['dob']);
    $user_type_id = trim($_POST['user_type_id']);
    $department_id = trim($_POST['department_id']);
    $position_id = trim($_POST['position_id']);

    if (empty($employee_name)) $errors[] = "Employee name is required.";
    if (empty($employee_email)) $errors[] = "Employee email is required.";
    if (empty($employee_phone)) $errors[] = "Phone number is required.";
    if (empty($dob)) $errors[] = "Date of Birth is required.";
    if (empty($department_id)) $errors[] = "Department selection is required.";
    if (empty($position_id)) $errors[] = "Position selection is required.";

    if (!filter_var($employee_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!preg_match("/^[0-9]{10,15}$/", $employee_phone)) {
        $errors[] = "Invalid phone number format.";
    }

    if (!empty($salary) && !is_numeric($salary)) {
        $errors[] = "Salary must be a numeric value.";
    }

    $profile_image = null; 
    if (!empty($_FILES['profile_image']['name'])) {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed.";
        } else {
            $upload_dir = "uploads/";
            $profile_image = $upload_dir . time() . "_" . basename($_FILES["profile_image"]["name"]);

            if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profile_image)) {
                $errors[] = "Failed to upload profile image.";
            }
        }
    }

    if (empty($errors)) {
        $query = "INSERT INTO employees (user_type_id, department_id, position_id, employee_name, employee_email, employee_phone, salary, profile_image, employee_details, employee_skills, dob, created_at, updated_at, status) 
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, NOW(), NOW(), FALSE) RETURNING employee_id";

        $params = array($user_type_id, $department_id, $position_id, $employee_name, $employee_email, $employee_phone, $salary, $profile_image, $employee_details, $employee_skills, $dob);
        $result = pg_query_params($conn, $query, $params);

        if ($result) {
            echo "<div class='alert alert-success'>Registration submitted! Waiting for admin approval.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . pg_last_error($conn) . "</div>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }

    pg_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
                  <a class="nav-link active text-primary" aria-current="page" href="register.php">REGISTER</a>
                </li>
                <li class="nav-item px-1">
                  <a class="nav-link" href="login.php">LOGIN</a>
                </li>
                <li class="nav-item px-1">
                  <a class="nav-link" href="dashboard.php">DASHBOARD</a>
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
        <h2 class='p-3 text-center'>Employee Registration</h2>
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="employee_name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="employee_email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="employee_phone" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Salary</label>
                <input type="number" class="form-control" name="salary">
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Image</label>
                <input type="file" class="form-control" name="profile_image">
            </div>

            <div class="mb-3">
                <label class="form-label">Skills</label>
                <input type="text" class="form-control" name="employee_skills">
            </div>

            <div class="mb-3">
                <label for="employee_details">Employee Details</label>
                <input type="text" name="employee_details" id="employee_details" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Department</label>
                <select class="form-control" name="department_id">
                    <option value="1">HR</option>
                    <option value="2">IT</option>
                    <option value="3">Finance</option>
                    <option value="4">Marketing</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Position</label>
                <select class="form-control" name="position_id">
                    <option value="1">Software Engineer</option>
                    <option value="2">Project Manager</option>
                    <option value="3">Data Analyst</option>
                    <option value="4">HR Coordinator</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="dob">
            </div>

            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select class="form-control" name="user_type_id">
                    <option value="1">Admin</option>
                    <option value="2">Manager</option>
                    <option value="3" selected>Employee</option>
                    <option value="4">Intern</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Register</button>
        </form>
    </div>
</body>
</html>
