<?php
$db_host = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "employes";

$conn = mysqli_connect('localhost','root','', 'employes');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
