<?php
session_start();
include("include/config.php");

if (!isset($_GET['id']) && !isset($_SESSION['employee_id'])) {
    $_SESSION['error'] = "Invalid employee ID";
    header("Location: view_profiles.php");
    exit();
}

if (isset($_GET['id'])) {
    $_SESSION['employee_id'] = $_GET['id'];
    header("Location: edit_employee.php"); // Redirect to remove ?id= from URL
    exit();
}

$employee_id = $_SESSION['employee_id'];

$query = "SELECT * FROM employees WHERE employee_id = $1";
$result = pg_query_params($conn, $query, array($employee_id));
$employee = pg_fetch_assoc($result);

if (!$employee) {
    $_SESSION['error'] = "Employee not found.";
    header("Location: view_profiles.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $employee_id = $_SESSION['employee_id'];
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

    $update_employee = "UPDATE employees SET 
    employee_name=$1, employee_email=$2, employee_phone=$3, salary=$4, profile_image=$5, 
    employee_details=$6, employee_skills=$7, dob=$8, user_type_id=$9, department_id=$10, position_id=$11 
    WHERE employee_id=$12";

    pg_prepare($conn, "update_employee", $update_employee);
    $result = pg_execute($conn, "update_employee", array(
        $employee_name, $employee_email, $employee_phone, $salary, 
        $profile_image, $employee_details, $employee_skills, $dob, 
        $user_type_id, $department_id, $position_id, $employee_id
    ));

    if ($result) {
        $_SESSION['success'] = "Employee updated successfully.";
    } else {
        $_SESSION['success'] = "Error updating employee.";
    }
    header("Location: view_profiles.php");
        exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($_SESSION['employee_id']); ?>">

            <div class="mb-3">
                <label class="form-label">Employee Id</label>
                <input type="number" class="form-control" name="employee_id" value="<?php echo htmlspecialchars($employee['employee_id']); ?>" readonly required>
            </div>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="employee_name" value="<?php echo htmlspecialchars($employee['employee_name']); ?>" readonly required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="employee_email" value="<?php echo htmlspecialchars($employee['employee_email']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="employee_phone" value="<?php echo htmlspecialchars($employee['employee_phone']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Salary</label>
                <input type="number" class="form-control" name="salary" value="<?php echo htmlspecialchars($employee['salary']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Image</label>
                <input type="file" class="form-control" name="profile_image" value="<?php echo htmlspecialchars($employee['profile_image']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Skills</label>
                <input type="text" class="form-control" name="employee_skills" value="<?php echo htmlspecialchars($employee['employee_skills']); ?>">
            </div>

            <div class="mb-3">
                <label for="employee_details">Employee Details</label>
                <input type="text" name="employee_details" id="employee_details" class="form-control" value="<?php echo htmlspecialchars($employee['employee_details']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Department</label>
                <select class="form-control" name="department_id">
                    <option value="1" <?php echo $employee['department_id'] == 1 ? 'selected' : ''; ?>>HR</option>
                    <option value="2" <?php echo $employee['department_id'] == 2 ? 'selected' : ''; ?>>IT</option>
                    <option value="3" <?php echo $employee['department_id'] == 3 ? 'selected' : ''; ?>>Finance</option>
                    <option value="4" <?php echo $employee['department_id'] == 4 ? 'selected' : ''; ?>>Marketing</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Position</label>
                <select class="form-control" name="position_id" value="<?php echo htmlspecialchars($employee['position_id']); ?>">
                    <option value="1" <?php echo $employee['position_id'] == 1 ? 'selected' : ''; ?>>Software Engineer</option>
                    <option value="2" <?php echo $employee['position_id'] == 2 ? 'selected' : ''; ?>>Project Manager</option>
                    <option value="3" <?php echo $employee['position_id'] == 3 ? 'selected' : ''; ?>>Data Analyst</option>
                    <option value="4" <?php echo $employee['position_id'] == 4 ? 'selected' : ''; ?>>HR Coordinator</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Date of Birth</label>
                <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($employee['dob']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select class="form-control" name="user_type_id" value="<?php echo htmlspecialchars($employee['user_type_id']); ?>">
                    <option value="1" <?php echo $employee['user_type_id'] == 1 ? 'selected' : ''; ?>>Admin</option>
                    <option value="2" <?php echo $employee['user_type_id'] == 2 ? 'selected' : ''; ?>>Manager</option>
                    <option value="3" <?php echo $employee['user_type_id'] == 3 ? 'selected' : ''; ?>>Employee</option>
                    <option value="4" <?php echo $employee['user_type_id'] == 4 ? 'selected' : ''; ?>>Intern</option>
                </select>
            </div>

            <?php if ($employee['status'] === 't'): ?>
                <a href="approve_employee.php?employee_id=<?php echo htmlspecialchars($employee['employee_id']); ?>&action=reject" class="btn btn-danger">Reject</a>
            <?php else: ?>
                <a href="approve_employee.php?employee_id=<?php echo htmlspecialchars($employee['employee_id']); ?>&action=approve" class="btn btn-success">Approve</a>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Save Edited Details</button>
            <a href="view_profiles.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
