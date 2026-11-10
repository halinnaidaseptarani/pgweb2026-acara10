<?php
$nama_desa   = $_POST['nama_desa'];
$nama_mata   = $_POST['nama_mata'];
$kecamatan   = $_POST['kecamatan'];
$point_x     = $_POST['point_x'];
$point_y     = $_POST['point_y'];

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "temanggung_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO mata_air (nama_desa, nama_mata, kecamatan, point_x, point_y) 
        VALUES ('$nama_desa', '$nama_mata', '$kecamatan', $point_x, $point_y)";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header("Location: ../index.php");
exit();
?>