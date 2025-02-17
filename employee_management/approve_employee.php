<?php
session_start();
include("include/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type_id'] != 1) {
    die("Unauthorized access.");
}

if (isset($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];

    $query = "SELECT * FROM employees WHERE employee_id = $1";
    $result = pg_query_params($conn, $query, array($employee_id));
    $employee = pg_fetch_assoc($result);

    if ($employee) {    
        $insert_query = "INSERT INTO users (employee_id, user_type_id, full_name, username, password, status)
                         VALUES ($1, 3, $2, $3, crypt($4, gen_salt('bf')), TRUE)";
        $params = array($employee['employee_id'], $employee['user_type_id'], $employee['employee_name'], $employee['employee_email'], "defaultPassword123");
        pg_query_params($conn, $insert_query, $params);

        pg_query_params($conn, "UPDATE employees SET status = TRUE WHERE employee_id = $1", array($employee_id));

        echo "User approved!";
    } else {
        echo "Employee not found!";
    }
}

pg_close($conn);
?>
