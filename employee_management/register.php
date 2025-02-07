<?php
include("include/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = trim($_POST['employee_name']);
    $employee_email = trim($_POST['employee_email']);
    $employee_phone = trim($_POST['employee_phone']);
    $salary = trim($_POST['salary']);
    $profile_image = trim($_POST['profile_image']);
    $employee_details = trim($_POST['employee_details']);
    $employee_skills = trim($_POST['employee_skills']);
    $dob = trim($_POST['dob']);
    $user_type_id = trim($_POST['user_type_id']);
    $department_id = trim($_POST['department_id']);
    $position_id = trim($_POST['position_id']);
    
    $query = "INSERT INTO employees (user_type_id, department_id, position_id, employee_name, employee_email, employee_phone, salary, profile_image, employee_details, employee_skills, dob, created_at, updated_at, status) 
              VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, NOW(), NOW(), FALSE) RETURNING employee_id";

    $result = pg_query_params($conn, $query, array($user_type_id, $department_id, $position_id, $employee_name, $employee_email, $employee_phone, $salary, $profile_image, $employee_details, $employee_skills, $dob));

    if ($result) {
        echo "Registration submitted! Waiting for admin approval.";
    } else {
        echo "Error: " . pg_last_error($conn);
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
</head>
<body>
    <div class="container p-3">
        <h2 class='p-3 text-center'>Employee Registration</h2>
        <form action="register.php" method="POST">
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
                    <option value="3">Employee</option>
                    <option value="4">Intern</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Register</button>
        </form>
    </div>
</body>
</html>
