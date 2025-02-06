<?php
session_start();
include("include/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $query = "SELECT * FROM users WHERE employee_email = $1";
    $result = pg_query_params($conn, $query, array($email));

    if (!$result) {
        echo "Error: " . pg_last_error($conn);
        exit();
    }

    if ($row = pg_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['full_name'];

            echo "Login successful! Welcome, " . $_SESSION['user_name'];

            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }

    pg_close($conn);
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container p-3">
    <h2 class='p-3'>User Login</h2>
    <form action="login.php" method="POST">

    <div class="mb-3">
        <label for="employee_email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="employee_email" name="email" placeholder="Enter Your Email" required>
        </div>

    <label for="password" class="form-label">Password</label>
    <input type="password" id="password" name="password" class="form-control" required>
    
    <button type="submit" class="btn btn-primary mt-3">Login</button>
    </form>
</div>
</body>
</html>
