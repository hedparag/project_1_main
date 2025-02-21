<?php
session_start();
include("include/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type_id'] != 1) {
    die("Unauthorized access.");
}

if (!isset($_GET['employee_id']) || !isset($_GET['action'])) {
    die("Missing parameters.");
}

$employee_id = $_GET['employee_id'];
$action = $_GET['action'];

if ($action === "approve" && $_SERVER["REQUEST_METHOD"] !== "POST") {

    $query = "SELECT employee_id, employee_name, employee_email, user_type_id FROM employees WHERE employee_id = $1";
    $result = pg_query_params($conn, $query, array($employee_id));
    $employee = pg_fetch_assoc($result);

    if (!$employee) {
        die("Employee not found!");
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Set Password</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="container mt-5">
        <h2>Set Password for <?php echo htmlspecialchars($employee['employee_name']); ?></h2>
        <form method="POST" action="approve_employee.php?employee_id=<?php echo htmlspecialchars($employee_id); ?>&action=approve">
            <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee_id); ?>">
            <input type="hidden" name="action" value="approve">

            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-success">Approve</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </body>
    </html>
    <?php
    exit();
} elseif ($action === "approve" && $_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['new_password']) || empty($_POST['new_password']) || !isset($_POST['confirm_password']) || empty($_POST['confirm_password'])) {
        die("Password fields are required.");
    }

    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        die("Passwords do not match.");
    }

    $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    $employee_id = $_POST['employee_id'];

    $query = "SELECT employee_id, employee_name, employee_email, user_type_id FROM employees WHERE employee_id = $1";
    $result = pg_query_params($conn, $query, array($employee_id));
    $employee = pg_fetch_assoc($result);

    if ($employee) {
        $insert_query = "INSERT INTO users (employee_id, full_name, username, user_type_id, password, status)
                         VALUES ($1, $2, $3, $4, $5, TRUE)";
        $params = array(
            $employee['employee_id'], 
            $employee['employee_name'],  
            $employee['employee_email'],
            $employee['user_type_id'],
            $password
        );
        $insert_result = pg_query_params($conn, $insert_query, $params);
    
        if ($insert_result) {
            $update_query = "UPDATE employees SET status = TRUE WHERE employee_id = $1";
            $update_result = pg_query_params($conn, $update_query, array($employee['employee_id']));
    
            if ($update_result) {
                // echo "User approved and moved to users table successfully, and employee status updated!";
                // echo "<br><a href=\"dashboard.php\"> Go to your Dashboard.</a>";
                header("Location: view_profiles.php?message=Employee%20approved%20successfully&type=success");
                exit();
            } else {
                //die("Error updating employee status: " . pg_last_error($conn));
                header("Location: view_profiles.php?message=Error%20updating%20status&type=error");
                exit();
            }
        } else {
            die("Error inserting user: " . pg_last_error($conn));
        }
    } else {
        die("Employee not found!");
    }
} elseif ($action === "reject") {
    $delete_user_result = pg_query_params($conn, "DELETE FROM users WHERE employee_id = $1", array($employee_id));
    $update_employee_status = pg_query_params($conn, "UPDATE employees SET status = FALSE WHERE employee_id = $1", array($employee_id));
    if ($delete_user_result) {
        if ($_SESSION['user_id'] == $employee_id) {
            session_destroy();  
            header("Location: logout.php");  
            exit();
        }

        header("Location: view_profiles.php?message=Employee%20rejected%20successfully&type=error");
        exit();
    } else {
            // die("Error rejecting employee from employees table: " . pg_last_error($conn));
            header("Location: view_profiles.php?message=Error%20rejecting%20employee&type=success");
            exit();
        }
} else {
    die("Invalid action.");
}

pg_close($conn);
?>
