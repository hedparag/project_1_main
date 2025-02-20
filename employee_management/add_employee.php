<?php
session_start();
include("include/config.php");

if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type_id'] !== '1') {
    header("Location: login.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $employee_name = trim($_POST['employee_name']);
    $employee_email = trim($_POST['employee_email']);
    $employee_phone = trim($_POST['employee_phone']);
    $salary = trim($_POST['salary']);
    $profile_image = '';
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

    if (!empty($_FILES['profile_image']['name'])) {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);

        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed.";
        }

        if ($_FILES['profile_image']['error'] != UPLOAD_ERR_OK) {
            $errors[] = "Error uploading file.";
        }

        $file_name = basename($_FILES['profile_image']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . uniqid() . '_' . $file_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            $profile_image = $target_file;
        } else {
            $errors[] = "Failed to upload image.";
        }
    }

    if (empty($errors)) {
        $query = "INSERT INTO employees (user_type_id, department_id, position_id, employee_name, employee_email, employee_phone, salary, profile_image, employee_details, employee_skills, dob, status, created_at, updated_at) 
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, FALSE, NOW(), NOW()) RETURNING employee_id";
        
        $result = pg_query_params($conn, $query, array($user_type_id, $department_id, $position_id, $employee_name, $employee_email, $employee_phone, $salary, $profile_image, $employee_details, $employee_skills, $dob));        

        if ($result) {
            $_SESSION['success'] = "Employee added successfully!";
            header("Location: view_profiles.php");
            exit();
        } else {
            $errors[] = "Database error: " . pg_last_error($conn);
        }
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
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
    <div class="container p-5">
        <h2>Add Employee</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

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

            <button type="submit" class="btn btn-primary">Add Employee</button>
            <a href="edit_profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
