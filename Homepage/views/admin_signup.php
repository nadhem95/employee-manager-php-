<?php
// Include database connection
require_once(__DIR__ . '/../db/DB.php');

// Define the special admin ID
$special_admin_id = "2030"; // Replace "YourSpecialAdminID" with your actual special admin ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form data
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $admin_id = $_POST["admin_id"];

    // Set status to 'pending'
    $status = 'pending';

    // Check if the provided admin ID matches the special admin ID
    if ($admin_id !== $special_admin_id) {
        $error_message = "Invalid admin ID";
    } else {
        // Insert admin credentials into database
        $sql = "INSERT INTO admin (email, username, password, status) VALUES ('$email', '$username', '$password', '$status')";
        if (mysqli_query($conn, $sql)) {
            // Signup successful, redirect to login page
            echo "<script>alert('Your account is pending approval. You will be notified once it is approved.');</script>";
            header("Location: admin_login.php");
            exit();
        } else {
            // Signup failed
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="signup_styles.css">
    <title>Admin Signup</title>
</head>
<body>
    <h2>Admin Signup</h2>
    <?php if(isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form action="admin_signup.php" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="admin_id">Special Admin ID:</label>
        <input type="text" id="admin_id" name="admin_id" required>
        <br>
        <button type="submit" onclick="showPopup()">Signup</button>
    </form>

    <!-- JavaScript function to show the popup -->
    <script>
        function showPopup() {
            alert('Your account is pending approval. You will be notified once it is approved.');
        }
    </script>
</body>
</html>
