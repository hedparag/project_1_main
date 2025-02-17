<?php
include("include/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $employee_name = $_POST['employee_name'];
    $employee_email = $_POST['employee_email'];
    $employee_phone = $_POST['employee_phone'];
    $salary = $_POST['salary'];
    $profile_image = $_POST['profile_image'];
    $employee_details = $_POST['employee_details'];
    $employee_skills = $_POST['employee_skills'];
    $dob = $_POST['dob'];
    $user_type_id = $_POST['user_type_id'];
    $department_id = $_POST['department_id'];
    $position_id = $_POST['position_id'];
    $username = $_POST['username'];
    $password = $_POST['password']; 

    $update_employee = "UPDATE employees SET employee_name=?, employee_email=?, employee_phone=?, salary=?, profile_image=?, employee_details=?, employee_skills=?, dob=?, user_type_id=?, department_id=?, position_id=? WHERE employee_id=?";
    $stmt = $conn->prepare($update_employee);
    $stmt->bind_param("sssssi", $employee_name, $employee_email, $employee_phone, $salary, $profile_image, $employee_details, $employee_skills, $dob, $user_type_id, $department_id, $position_id, $employee_id);
    $stmt->execute();

    $check_user = "SELECT user_id FROM users WHERE employee_id=?";
    $stmt = $conn->prepare($check_user);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $update_user = "UPDATE users SET username=?, password=? WHERE employee_id=?";
            $stmt = $conn->prepare($update_user);
            $stmt->bind_param("ssi", $username, $hashed_password, $user_id);
        } else {
            $update_user = "UPDATE users SET username=? WHERE employee_id=?";
            $stmt = $conn->prepare($update_user);
            $stmt->bind_param("si", $username, $user_id);
        }
        $stmt->execute();
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert_user = "INSERT INTO users (employee_id, username, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_user);
            $stmt->bind_param("iss", $id, $username, $hashed_password);
            $stmt->execute();
        }
    }

    echo "Employee updated successfully.";
    header("Location: view_profiles.php");
    exit();
}

if (isset($_GET['employee_id'])) {
    $id = $_GET['employee_id'];
    $query = "SELECT * FROM employees WHERE employee_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Edit Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Employee</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($employee['employee_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($employee['employee_email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Department</label>
            <input type="number" name="department_id" class="form-control" value="<?php echo htmlspecialchars($employee['department_id']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="number" name="position_id" class="form-control" value="<?php echo htmlspecialchars($employee['position_id']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="edit_profile.php" class="btn btn-secondary">Back</a>
    </form>
</div>
=======
    <title>Edit Employee Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container p-5">
        <h2 class="text-center">Edit Employee Details</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="mt-4">
        <div class="mb-3">
                <label class="form-label">Employee Id</label>
                <input type="number" class="form-control" name="employee_id" required>
            </div>
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
                <label class="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-label" required>
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

            <div class="mb-3">
                <label for="password" class="form-label">Set Password</label>
                <input type="password" name="password" id="password" class="form-label" required>
            </div>

            <!-- <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="status" value="true">
                <label class="form-check-label">Approve this Employee</label>
            </div> -->

            <button type="submit" class="btn btn-primary">Save Edited Details</button>
            <a href="edit_profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
>>>>>>> origin/feature/dashboard
</body>
</html>
