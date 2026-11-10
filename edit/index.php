<!DOCTYPE html>
<html>
<body>
<h2>Form Edit Titik Mata Air</h2>
<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "temanggung_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM mata_air WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<form action='edit.php' method='post'>";
        while($row = $result->fetch_assoc()) {
            echo "<input type='hidden' name='id' value='".$row['id']."'>";
            echo "<label>Nama Desa:</label><br>";
            echo "<input type='text' name='nama_desa' value='".$row['nama_desa']."'><br>";
            echo "<label>Nama Mata Air:</label><br>";
            echo "<input type='text' name='nama_mata' value='".$row['nama_mata']."'><br>";
            echo "<label>Kecamatan:</label><br>";
            echo "<input type='text' name='kecamatan' value='".$row['kecamatan']."'><br>";
            echo "<label>Longitude (X):</label><br>";
            echo "<input type='text' name='point_x' value='".$row['point_x']."'><br>";
            echo "<label>Latitude (Y):</label><br>";
            echo "<input type='text' name='point_y' value='".$row['point_y']."'><br><br>";
            echo "<input type='submit' value='Update'>";
        }
        echo "</form>";
    } else {
        echo "Data tidak ditemukan.";
    }
} else {
    echo "ID tidak valid. Kembali ke <a href='../index.php'>Halaman Utama</a>.";
}
$conn->close();
?>
</body>
</html>