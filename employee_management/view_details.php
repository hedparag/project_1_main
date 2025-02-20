<?php
session_start();
include("include/config.php");

$employee_id = $_GET['id'];

$query = "SELECT * FROM employees WHERE employee_id = $1";
$result = pg_query_params($conn, $query, array($employee_id));
$employee = pg_fetch_assoc($result);

if (!$employee) {
    header("Location: view_profiles.php");
    exit();
}

$departments = [1 => "HR", 2 => "IT", 3 => "Finance", 4 => "Marketing"];
$positions = [1 => "Software Engineer", 2 => "Project Manager", 3 => "Data Analyst", 4 => "HR Coordinator"];
$user_types = [1 => "Admin", 2 => "Manager", 3 => "Employee", 4 => "Intern"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container p-5">
        <h2 class="text-center mb-4">Employee Details</h2>

        <div class="card mx-auto shadow-lg" style="max-width: 40rem;">
            <?php if (!empty($employee['profile_image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($employee['profile_image']); ?>" class="card-img-top" alt="Profile Image">
            <?php endif; ?>

            <div class="card-body">
                <h4 class="card-title text-center mb-3"><?php echo htmlspecialchars($employee['employee_name']); ?></h4>
                
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th scope="row">Email</th>
                            <td><?php echo htmlspecialchars($employee['employee_email']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Phone</th>
                            <td><?php echo htmlspecialchars($employee['employee_phone']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Salary</th>
                            <td>₹<?php echo htmlspecialchars($employee['salary']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Skills</th>
                            <td><?php echo htmlspecialchars($employee['employee_skills']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Details</th>
                            <td><?php echo htmlspecialchars($employee['employee_details']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Date of Birth</th>
                            <td><?php echo htmlspecialchars($employee['dob']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Department</th>
                            <td><?php echo $departments[$employee['department_id']] ?? "Unknown"; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Position</th>
                            <td><?php echo $positions[$employee['position_id']] ?? "Unknown"; ?></td>
                        </tr>
                        <tr>
                            <th scope="row">User Type</th>
                            <td><?php echo $user_types[$employee['user_type_id']] ?? "Unknown"; ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="d-flex justify-content-between">
                    <a href="edit_employee.php?id=<?php echo $employee['employee_id']; ?>&hash=<?php echo md5($employee['employee_id'].'abcd'); ?>" class="btn btn-primary">Edit Employee Details</a>
                    <a href="view_profiles.php" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
