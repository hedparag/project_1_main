<?php
session_start();

$invalid = 0;
if(md5($_GET['id'].'abcd') != $_GET['hash']){
    $invalid = 1;
}

include("include/config.php");

$employee_id = $_GET['id'];

$query = "SELECT * FROM employees WHERE employee_id = $1";
$result = pg_query_params($conn, $query, array($employee_id));
$employee = pg_fetch_assoc($result);

if (!$employee) {
    // $_SESSION['error'] = "Employee not found.";
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

    $employee_name = $_POST['employee_name'];
    $employee_email = $_POST['employee_email'];
    $employee_phone = $_POST['employee_phone'];
    $salary = $_POST['salary'];
    // $profile_image = $_POST['profile_image'];
    $employee_details = $_POST['employee_details'];
    $employee_skills = $_POST['employee_skills'];
    $dob = $_POST['dob'];
    $user_type_id = $_POST['user_type_id'];
    $department_id = $_POST['department_id'];
    $position_id = $_POST['position_id'];

    $errors = [];

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

    $profile_image = $employee['profile_image'];;
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
            $unique_name = $_FILES["profile_image"]["name"];
            $profile_image = $upload_dir . $unique_name;

            if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profile_image)) {
                $errors[] = "Failed to upload profile image.";
            } 
        }
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: view_details.php?id=$employee_id");
        exit();
    }

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
        // $_SESSION['success'] = "Employee updated successfully.";
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } else {
        $_SESSION['success'] = "Error updating employee.";
    }
        header("Location: view_details.php");
        exit();
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
        <?php if($invalid == 0) { ?>
        <h2 class="text-center">Edit Employee Details</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="mt-4">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

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
        <?php } else { ?>
            <div class="alert alert-danger" role="alert">
                INVALID USER ID.
            </div>
        <?php } ?>
    </div>
>>>>>>> origin/feature/dashboard
</body>
</html>
