<?php
$host = "localhost";
$port = "5432";     
$dbname = "employee_db";
$user = "postgres";  
$password = "admin";  

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
} 
// else {
//     echo "Connected successfully to PostgreSQL!";
// }

// function checkAdmin() {
//     if (!isset($_SESSION['user_id']) || $_SESSION['user_type_id'] != 1) {
//         header("Location: dashboard.php");
//         exit();
//     }
// }
// ?>
