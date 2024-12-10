<?php
// Include database connection
require_once(__DIR__ . '/../db/DB.php');
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $employee_id = $_POST["employee_id"];
    $designation = $_POST["designation"];
    $password = $_POST["password"];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the provided employee ID exists in valid_employee_ids table
    $check_employee_sql = "SELECT * FROM valid_employee_ids WHERE employee_id='$employee_id'";
    $check_employee_result = mysqli_query($conn, $check_employee_sql);
    if (mysqli_num_rows($check_employee_result) == 1) {
        // Employee ID exists, check if department matches
        $employee_data = mysqli_fetch_assoc($check_employee_result);
        $department = $employee_data['department'];

        // Check if the department matches
        if ($department != $_POST['department']) {
            $message = "Employee ID and department do not match";
        } else {
            // Insert new employee data into the database
            $insert_sql = "INSERT INTO employee (name, email, employee_id, designation, department, password) VALUES ('$name', '$email', '$employee_id', '$designation', '$department', '$hashed_password')";
            if (mysqli_query($conn, $insert_sql)) {
                // Set a flag to redirect to login page after successful signup
                $redirect = true;
                $message = "Signup successful!";
            } else {
                $message = "Error occurred while signing up";
            }
        }
    } else {
        $message = "Invalid employee ID";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="signup_styles.css"> <!-- Replace with your updated CSS file -->
    <title>Employee Signup</title>
</head>
<body>
    <div class="center">
        <h2>Employee Signup</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="employee_id">Employee ID:</label>
            <input type="text" id="employee_id" name="employee_id" required>
            <br>
            <label for="designation">Designation:</label>
            <input type="text" id="designation" name="designation">
            <br>
            <label for="department">Department:</label>
            <select id="department" name="department" required>
                <option value="" disabled selected>Select Department</option>
                <option value="Finance/Accounting">Finance/Accounting</option>
                <option value="Marketing/Sales">Marketing/Sales</option>
                <option value="Customer Service/Support">Customer Service/Support</option>
                <option value="Information Technology (IT)">Information Technology (IT)</option>
                <option value="Logistics">Logistics</option>
                <option value="Administration">Administration</option>
                <option value="Human Resources">Human Resources</option>
            </select>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Signup</button>
        </form>
    </div>
    <script>
    <?php if (!empty($message)) : ?>
        alert("<?php echo $message; ?>");
        <?php if ($redirect) : ?>
            setTimeout(function() {
                window.location.href = './em_login.php';
            }, 2000);
        <?php endif; ?>
    <?php endif; ?>
</script>
</body>
</html>
