<?php
// Include database connection
require_once('DB.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (isset($_POST['employee_id'], $_POST['name'], $_POST['email'])) {
        // Sanitize inputs to prevent SQL injection
        $employee_id = $_POST['employee_id'];
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        // Update employee details in the database
        $sql = "UPDATE employee SET name = ?, email = ? WHERE employee_id = ?";
        $stmt = $conn->prepare($sql);

        // Check if the query preparation was successful
        if (!$stmt) {
            die("Error preparing SQL statement: " . $conn->error);
        }

        // Bind parameters and execute the query
        $stmt->bind_param("ssi", $name, $email, $employee_id);

        if ($stmt->execute()) {
            // Redirect back to admin panel after successful update
            header("Location: admin_panel.php");
            exit();
        } else {
            // Show error message if update fails
            echo "Error updating employee details: " . $stmt->error;
        }
    } else {
        // Show error message if required fields are not filled
        echo "All fields are required.";
    }
} else {
    // Include the initial employee fetching code here
    // Check if employee ID is provided in the query parameter
    if (!isset($_GET['employee_id'])) {
        // Redirect back to admin panel or show error message
        header("Location: admin_panel.php");
        exit();
    }

    // Fetch employee details from the database using employee ID
    $employee_id = $_GET['employee_id'];
    $sql = "SELECT * FROM employee WHERE employee_id = ?";
    $stmt = $conn->prepare($sql);

    // Check if the query preparation was successful
    if (!$stmt) {
        die("Error preparing SQL statement: " . $conn->error);
    }

    // Bind the employee ID parameter
    $stmt->bind_param("i", $employee_id);

    // Execute the query
    if (!$stmt->execute()) {
        die("Error executing SQL statement: " . $stmt->error);
    }

    // Get the result of the query
    $result = $stmt->get_result();

    // Check if any rows were returned
    if ($result->num_rows == 0) {
        // Employee not found, redirect back to admin panel or show error message
        header("Location: admin_panel.php");
        exit();
    }

    // Fetch the employee details
    $employee = $result->fetch_assoc();

    // Display employee details in an edit form
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <style>
    body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }
    
    </style>
</head>
<body>
    <h1>Edit Employee</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $employee['name']; ?>"><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $employee['email']; ?>"><br>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
<?php } ?>
