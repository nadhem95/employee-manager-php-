<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
</head>
<body>

<?php
require_once(__DIR__ . '/../db/DB.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    // File upload directory
// Define the upload directory path
// Define the upload directory path
$uploadDir = __DIR__ . '/../uploads/';

    // Get the uploaded file name
    $fileName = basename($_FILES["file"]["name"]);

    // Path to save the uploaded file
    $filePath = $uploadDir . $fileName;

    // Move the uploaded file to the specified directory
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
        // Insert file information into the database
        $sql = "INSERT INTO files (file_name, file_path) VALUES ('$fileName', '$filePath')";
        $result = $conn->query($sql);

        if ($result) {
            echo "File uploaded successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $conn->close();
    } else {
        echo "Error uploading file.";
    }
}
?>

<h2>Upload a File</h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <input type="file" name="file" id="file">
    <button type="submit" name="submit">Upload File</button>
</form>

</body>
</html>
