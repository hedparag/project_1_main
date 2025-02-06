<?php
include("include/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['user_name']);
    $username = trim($_POST['username']);
    $employee_email = trim($_POST['employee_email']);
    $employee_phone = trim($_POST['employee_phone']);
    $salary = trim($_POST['salary']);
    $profile_image = trim($_POST['profile_image']);
    $employee_details = trim($_POST['employee_details']);
    $dob = trim($_POST['dob']);
    $status = trim($_POST['status']);
    $password = trim($_POST['password']);

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO users (full_name, username, employee_email, employee_phone, salary, profile_image, employee_details, dob, status, password) 
              VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)";

    $result = pg_query_params($conn, $query, array($full_name, $username, $employee_email, $employee_phone, $salary, $profile_image, $employee_details, $dob, $status, $hashed_password));

    if ($result) {
        echo "Registration successful! <a href='login.php'>Login here</a>";
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
    <!-- <link rel="stylesheet" href="css/styles.css"> -->
</head>
<body>
    <div class="container p-3">
        <h2 class='p-3 text-center'>User Registration</h2>
        <form action="register.php" method="POST">
    <div class="mb-3">
        <label for="full_name" class="form-label">Full Name </label>
        <input type="text" class="form-control" id="full_name" name="user_name" placeholder="Enter Your Name" required>
    </div>

    <div class="mb-3">
        <label for="username" class="form-label">Username </label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Enter Your Username" required>
    </div>

    <div class="mb-3">
        <label for="employee_email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="employee_email" name="employee_email" placeholder="Enter Your Email" required>
    </div>

    <div class="mb-3">
        <label for="employee_phone" class="form-label">Phone Number</label>
        <input type="number" class="form-control" id="employee_phone" name="employee_phone" placeholder="Enter Your Phone Number" required>
    </div>

    <div class="mb-3">
        <label for="salary" class="form-label">Salary</label>
        <input type="number" class="form-control" id="salary" name="salary" placeholder="Enter Your Salary" required>
    </div>

    <div class="input-group mb-3">
        <input type="file" class="form-control" id="profile_image" name="profile_image">
        <label class="input-group-text" for="profile_image">Upload Profile Image</label>
    </div>

    <div class="mb-3">
        <label for="employee_details" class="form-label">Employee Details</label>
        <textarea class="form-control" id="employee_details" name="employee_details" rows="3"></textarea>
    </div>

    <div class="mb-3">
        <label for="dob" class="form-label">Date of Birth</label>
        <input type="date" class="form-control" id="dob" name="dob" required>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-control" id="status" name="status" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <label for="password" class="form-label">Password</label>
    <input type="password" id="password" name="password" class="form-control" required>
    
    <button type="submit" class="btn btn-primary mt-3">Register</button>
</form>

    </div>
</body>
</html>
