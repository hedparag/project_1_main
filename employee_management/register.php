<?php

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); 
    exit();
}

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
    $allowed_extensions = ['jpg', 'jpeg', 'png'];

    if (!empty($_FILES['profile_image']['name'])) {
        $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed.";
        } elseif (!in_array(mime_content_type($_FILES['profile_image']['tmp_name']), ['image/jpeg', 'image/png'])) {
            $errors[] = "Invalid image file type.";
        } elseif ($_FILES['profile_image']['size'] > 2 * 1024 * 1024) {
            $errors[] = "Image size should not exceed 2MB.";
        } else {
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $unique_name = uniqid() . "_" . basename($_FILES["profile_image"]["name"]);
            $profile_image = $upload_dir . $unique_name;

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
    
    </header>
    <div class="container p-3">
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
        <div class="container p5 text-center ">
            Already registered? <a href="login.php">Go to Login page.</a>
        </div>
    </div>
</body>
</html>
