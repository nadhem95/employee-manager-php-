<?php
// Include database connection
require_once(__DIR__ . '/../db/DB.php');

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form data
    $email = $_POST["email"];
    $password = $_POST["password"];

    // SQL query to select employee with the given email
    $sql = "SELECT * FROM employee WHERE email = ?";

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if employee exists and verify password
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION["employee_id"] = $row['employee_id']; // Assuming employee_id is unique
            header("Location: codetest.php");
            exit();
        } else {
            $error_message = "Invalid password";
        }
    } else {
        $error_message = "Invalid email or employee ID";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login_styles.css">
    <title>Employee Login</title>
</head>
<body>
<div class="center">
    <h2>Employee Login</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
        <button type="button" class="signup-button" onclick="window.location.href='em_signup.php';">Signup</button>
    </form>
</div>
</body>
</html>
