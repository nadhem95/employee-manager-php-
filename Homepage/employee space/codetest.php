<?php
// Include database connection
require_once(__DIR__ . '/../db/DB.php');

// Start session
session_start();

// Check if employee is logged in
if (!isset($_SESSION["employee_id"])) {
    header("Location: em_login.php");
    exit();
}

// Fetch employee data from the database
$sql = "SELECT * FROM employee WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("s", $_SESSION["employee_id"]);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $employee_data = $result->fetch_assoc();
} else {
    die("Employee not found");
}

// Handle leave request form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO demandes_conge (start_date, start_time, end_date, end_time, leave_type, duration, reason, employee_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $start_date, $start_time, $end_date, $end_time, $leave_type, $duration, $reason, $_SESSION["employee_id"]);

    // Set parameters and execute the statement
    $start_date = $_POST["start-date"];
    $start_time = isset($_POST["start-date-time"]) ? $_POST["start-date-time"] : null;
    $end_date = $_POST["end-date"];
    $end_time = isset($_POST["end-date-time"]) ? $_POST["end-date-time"] : null;
    $leave_type = $_POST["leave-type"];
    $duration = $_POST["duration"];
    $reason = $_POST["reason"];

    if ($stmt->execute()) {
        echo "Request created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="csstest.css">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
</head>
<body>
<div class="fixed-container">
    <header class="header">    
    <a href="">Employee Dashboard</a>
    <div class="logout">
        <a href="logout.php" class="btn btn-primary">Logout</a>
    </div>
</header>

<aside>
    <ul>
        <li><a href="codetest.php">Employee Profile</a></li>
        <li><a href="#" onclick="toggleInternshipApplications()">Les Demandes De Stage</a></li>
        <li><a href="#" onclick="toggleLeaveRequest()">Demande de congé</a></li>
    </ul>
</aside>  
</div>
 <div class="content employee-profile" id="employee-profile">
    <h1>Employee Profile</h1>
    <img src="employee.png" alt="Profile Picture" class="profile-picture">
    <div class="info-item">
        <span class="info-label">Name:</span>
        <span class="info-value"><?php echo $employee_data['name']; ?></span>
    </div>
    <div class="info-item">
        <span class="info-label">Email:</span>
        <span class="info-value"><?php echo $employee_data['email']; ?></span>
    </div>
    <div class="info-item">
        <span class="info-label">Employee ID:</span>
        <span class="info-value"><?php echo $employee_data['employee_id']; ?></span>
    </div>
    <div class="info-item">
        <span class="info-label">Department:</span>
        <span class="info-value"><?php echo $employee_data['department']; ?></span>
    </div>
</div>

<div class="internship-applications" id="internship-applications" style="display: none;">
        <h2>Internship Applications</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Domain</th>
                <th>Type</th>
                <th>University</th>
                <th>Motivation</th>
                <th>CV</th>
                <th>Actions</th>
            </tr>
            <?php
// Fetch internship applications from the database
$sql = "SELECT * FROM demandes_stage";
$result = $conn->query($sql);

// Display data in a table
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row['id']."</td>";
        echo "<td>".$row['prenom']." ".$row['nom']."</td>";
        echo "<td>".$row['email']."</td>";
        echo "<td>".$row['tel']."</td>";
        echo "<td>".$row['domaine']."</td>";
        echo "<td>".$row['type_stage']."</td>";
        echo "<td>".$row['universite']."</td>";
        echo "<td class='motivation'>".$row['motivation']."</td>";
        echo "<td><a href='".$row['cv_path']."' class='cv-link' target='_blank'>View CV</a></td>";
        echo "<td class='actions'>";
        echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
        echo "<input type='hidden' name='application_id' value='".$row['id']."'>";
        echo "<button type='submit' name='action' value='accept' class='accept'><a href='mailto:".$row['email']."?subject=Votre%20demande%20de%20stage%20a%20été%20acceptée&body=Bonjour%20".$row['prenom'].",%0A%0ANous%20sommes%20heureux%20de%20vous%20informer%20que%20votre%20demande%20de%20stage%20chez%20Tunisie%20Telecom%20a%20été%20acceptée.%20Nous%20vous%20contacterons%20bientôt%20pour%20les%20détails%20supplémentaires.%0A%0ACordialement,%0AVotre%20équipe%20de%20recrutement'>Accepter</a></button>";
        echo "<button type='submit' name='action' value='deny' class='deny'><a href='mailto:".$row['email']."?subject=Votre%20demande%20de%20stage%20a%20été%20refusée&body=Bonjour%20".$row['prenom'].",%0A%0ANous%20regrettons%20de%20vous%20informer%20que%20votre%20demande%20de%20stage%20chez%20Tunisie%20Telecom%20a%20été%20refusée.%20Nous%20vous%20remercions%20de%20votre%20intérêt%20et%20vous%20souhaitons%20bonne%20chance%20dans%20vos%20recherches%20futures.%0A%0ACordialement,%0AVotre%20équipe%20de%20recrutement'>Refuser</a></button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>No internship applications found</td></tr>";
}
// Handle accept or deny action
if (isset($_POST['action']) && isset($_POST['application_id'])) {
    $action = $_POST['action'];
    $application_id = $_POST['application_id'];

    if ($action === 'deny') {
        // Delete application from the database if it's refused
        $sql_delete = "DELETE FROM demandes_stage WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $application_id);
        $stmt_delete->execute();
    }
}
?>
</table>
</div>

<div class="leave-request-form-container" id="leave-request-form-container" style="display: none;">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <h2>Créer une demande de congé</h2>        
        <label for="start-date">Date de début:</label><br>
        <input type="date" id="start-date" name="start-date"><br>
        <select id="start-date-time" name="start-date-time">
            <option value="">Select...</option>
            <option value="morning">Matin</option>
            <option value="afternoon">Après-midi</option>
        </select><br>

        <label for="end-date">Date de fin:</label><br>
        <input type="date" id="end-date" name="end-date"><br>
        <select id="end-date-time" name="end-date-time">
            <option value="">Select...</option>
            <option value="morning">Matin</option>
            <option value="afternoon">Après-midi</option>
        </select><br>

        <label for="leave-type">Type de congés:</label><br>
        <select id="leave-type" name="leave-type">
            <option value="paid-leave">Congé payé</option>
            <option value="unpaid-leave">Congé sans solde</option>
            <option value="military-duty">Congé pour obligations militaires</option>
            <option value="sick-leave">Congé de maladie</option>
            <option value="exceptional-leave">Congé exceptionnel</option>
            <option value="maternity-leave">Congé de Maternité</option>
            <option value="family-leave">Congé spéciaux pour raison de famille</option>
        </select><br>


        <label for="duration">Durée:</label><br>
        <input type="text" id="duration" name="duration"><br>

        <label for="reason">Cause (optionelle):</label><br>
        <textarea id="reason" name="reason"></textarea><br>

        <button class="demande-button" onclick="toggleLeaveRequest()">Créer la demande</button>
        <button class="annuler-button" onclick="toggleLeaveRequest()">Annuler</button>
    </form>
</div>

<script>
    function toggleInternshipApplications() {
    var employeeProfileDiv = document.getElementById('employee-profile');
    var internshipApplicationsDiv = document.getElementById('internship-applications');
    var leaveRequestFormDiv = document.getElementById('leave-request-form-container');

    if (internshipApplicationsDiv.style.display === 'none') {
        employeeProfileDiv.style.display = 'none';
        leaveRequestFormDiv.style.display = 'none';
        internshipApplicationsDiv.style.display = 'block';
    } else {
        employeeProfileDiv.style.display = 'block';
        internshipApplicationsDiv.style.display = 'none';
    }
}

function toggleLeaveRequest() {
    var employeeProfileDiv = document.getElementById('employee-profile');
    var internshipApplicationsDiv = document.getElementById('internship-applications');
    var leaveRequestFormDiv = document.getElementById('leave-request-form-container');

    if (leaveRequestFormDiv.style.display === 'none') {
        employeeProfileDiv.style.display = 'none';
        internshipApplicationsDiv.style.display = 'none';
        leaveRequestFormDiv.style.display = 'block';
    } else {
        employeeProfileDiv.style.display = 'block';
        leaveRequestFormDiv.style.display = 'none';
    }
}
</script>

</body>
</html>