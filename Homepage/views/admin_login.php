<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login_styles.css">
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <form action="admin_login.php" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <button type="submit">Login</button>
    <button type="button" class="signup-button" onclick="window.location.href='admin_signup.php';">Signup</button>
</form>
</body>
</html>
<?php
// Include database connection
require_once('DB.php');

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validate admin credentials
    $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $_SESSION["admin_username"] = $username;
        header("Location: admin_panel.php");
        exit();
    } else {
        $error_message = "Invalid username or password";
    }
}
?>
