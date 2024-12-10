<?php
// Include database connection
require_once('DB.php');

session_start();
if (!isset($_SESSION["admin_username"])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch leave requests from the database
$sql = "SELECT * FROM demandes_conge";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error fetching leave requests: " . mysqli_error($conn));
}
$leave_requests = mysqli_fetch_all($result, MYSQLI_ASSOC);
// Fetch employee accounts from the database
$sql_accounts = "SELECT * FROM employee";
$result_accounts = mysqli_query($conn, $sql_accounts);
if (!$result_accounts) {
    die("Error fetching employee accounts: " . mysqli_error($conn));
}
$employee_accounts = mysqli_fetch_all($result_accounts, MYSQLI_ASSOC);
// Handle accept or deny action for leave requests
if (isset($_POST['action']) && isset($_POST['request_id'])) {
    $action = $_POST['action'];
    $request_id = $_POST['request_id'];

    // Update request status in the database
    $sql_update = "UPDATE demandes_conge SET status = ? WHERE employee_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $action, $request_id);
    $stmt_update->execute();

    if ($stmt_update->affected_rows > 0) {
        // Redirect to admin panel
        header("Location: admin_panel.php");
    } else {
        echo "Error updating request status.";
    }
}
// Handle delete action for employee accounts
if (isset($_POST['delete']) && isset($_POST['employee_id'])) {
    $employee_id = $_POST['employee_id'];

    // Delete employee account from the database
    $sql_delete = "DELETE FROM employee WHERE employee_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $employee_id);
    $stmt_delete->execute();

    if ($stmt_delete->affected_rows > 0) {
        // Redirect to admin panel
        header("Location: admin_panel.php");
    } else {
        echo "Error deleting employee account.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="panel_styles.css">
    <title>Panel Administrateur</title>
</head>
<body>
    <h1>Bienvenue, Admin</h1>
    <h2>Demandes de Congé en Attente</h2>
    <table>
        <thead>
            <tr>
                <th>ID Employé</th>
                <th>Date de Début</th>
                <th>Date de Fin</th>
                <th>Type de Congé</th>
                <th>Durée</th>
                <th>Raison</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php 
foreach ($leave_requests as $request): 
    // Fetching employee email from the database
    $employee_id = $request['employee_id'];
$sql_email = "SELECT email FROM employee WHERE employee_id = ?";
$stmt_email = $conn->prepare($sql_email);
$stmt_email->bind_param("i", $employee_id);
$stmt_email->execute();
$result_email = $stmt_email->get_result();

if ($result_email->num_rows > 0) {
    $row_email = $result_email->fetch_assoc();
    $employee_email = $row_email['email'];
} else {
    // Handle the case where the email is not found
    $employee_email = ''; // or any default value
}
?>
    <tr>
        <td><?php echo $request['employee_id']; ?></td>
        <td><?php echo $request['start_date']; ?></td>
        <td><?php echo $request['end_date']; ?></td>
        <td><?php echo $request['leave_type']; ?></td>
        <td><?php echo $request['duration']; ?></td>
        <td><?php echo $request['reason']; ?></td>
        <td><?php echo $request['status']; ?></td>
        <td>
            <?php if ($request['status'] == 'Pending'): ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="request_id" value="<?php echo $request['employee_id']; ?>">
                    <button type="submit" name="action" value="approved" class="approve-btn">Approuver</button>
                    <button type="submit" name="action" value="reject" class="reject-btn">Refuser</button>
                </form>
            <?php elseif ($request['status'] == 'approved'): ?>
                <a href="mailto:<?php echo $employee_email; ?>?subject=Demande de congé approuvée&body=Votre demande de congé a été approuvée. Nous sommes heureux de vous informer que votre demande de congé a été approuvée. Vous pouvez désormais planifier vos activités en conséquence. Merci." class="button">Informez l'employé</a>
                <?php elseif ($request['status'] == 'reject'): ?>
                <a href="mailto:<?php echo $employee_email; ?>?subject=Demande de congé refusée&body=Votre demande de congé a été refusée. Nous vous informons que votre demande de congé a été refusée pour le moment. Veuillez prendre les mesures nécessaires en conséquence. Merci." class="button">Informez l'employé</a>
            <?php endif; ?>

        </td>
    </tr>
<?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <h2>Comptes Employés</h2>
    <table>
        <thead>
            <tr>
                <th>ID Employé</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Département</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($employee_accounts as $account): ?>
            <tr>
                <td><?php echo $account['employee_id']; ?></td>
                <td><?php echo $account['name']; ?></td>
                <td><?php echo $account['email']; ?></td>
                <td><?php echo $account['department']; ?></td>
                <td>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <a href="edit_employee.php?employee_id=<?php echo $account['employee_id']; ?>" class="button">Edit</a>
                        <input type="hidden" name="employee_id" value="<?php echo $account['employee_id']; ?>">
                        <button type="submit" name="delete"class="reject-btn">Delete</button>
                    </form>                
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a1 href="admin_logout.php">Déconnexion</a1>
</body>
</html>
