<?php
session_start();
include("include/config.php");

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if(!hash_equals($SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF token validation failed.")
  }

  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $query = "SELECT user_id, username, password, user_type_id FROM users WHERE username = $1";
  $result = pg_query_params($conn, $query, array($username));

  if ($result && pg_num_rows($result) > 0) {
    $user = pg_fetch_assoc($result);

      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_type_id'] = $user['user_type_id']; 
        header("Location: dashboard.php");
        exit();
      } else {
        $error = "Invalid password.";
      }
  } else {
        $error = "User not found.";
  }
}
pg_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    </style>
</head>
<body>
<header>
        <nav class="navbar navbar-expand-lg custom-navbar px-4 border-bottom rounded-bottom fixed-top" style="background-color: #343a40;">
          <div class="container-fluid">
            <a class="navbar-brand fs-6" href="home.html"><h1>Fusion<span class="text-primary">Works</span></h1></a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fs-5 text-center">
                <li class="nav-item px-1">
                  <a class="nav-link" href="home.html">HOME</a>
                </li>
                <li class="nav-item px-1">
                  <a class="nav-link" href="register.php">REGISTER</a>
                </li>
                <li class="nav-item px-1">
                  <a class="nav-link active text-primary" aria-current="page" href="login.php">LOGIN</a>
                </li>
                <li class="nav-item px-1">
                  <a class="nav-link" href="dashboard.php">DASHBOARD</a>
                </li>
                <li class="nav-item px-1">
                  <a class="nav-link" href="logout.php">LOGOUT</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
      </header>
<div class="container p-5 mt-5">
    <h2 class="text-center">Login</h2>
    <form method="POST" action="login.php">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Enter your username" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <div class="container p5 text-center ">
      Fresh Face? <a href="register.php">Register Here!</a>
    </div>
</div>
</body>
</html>
