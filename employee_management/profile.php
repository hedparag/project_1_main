<?php
include("include/config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type_id = $_SESSION['user_type_id'];
$profile_id = $_GET['id'];

if ($user_type_id != 1 && $profile_id != $user_id) {
    header("Location: dashboard.php");
    exit();
}

$query = "SELECT * FROM employees WHERE employee_id = $1";
$result = pg_query_params($conn, $query, array($profile_id));
$row = pg_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2><?php echo $row['employee_name']; ?>'s Profile</h2>
    <p><strong>Email:</strong> <?php echo $row['employee_email']; ?></p>
    <p><strong>Phone:</strong> <?php echo $row['employee_phone']; ?></p>
    <?php if ($user_type_id == 1) { ?>
        <a href="edit_profile.php?id=<?php echo $profile_id; ?>" class="btn btn-warning">Edit Profile</a>
    <?php } ?>
</div>
</body>
</html>
