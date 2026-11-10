<?php
$id = $_GET['id'];

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "temanggung_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "DELETE FROM mata_air WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Record with id = $id deleted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header("Location: index.php");
exit();
?>