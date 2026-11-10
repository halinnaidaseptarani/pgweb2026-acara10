<?php
// 1. Setting Koneksi ke Database
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "temanggung_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// 2. Query Ambil Data Mata Air
$sql = "SELECT * FROM mata_air";
$result = $conn->query($sql);
$total_data = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web GIS Eksekutif - Titik Mata Air Temanggung</title>
    <!-- Leaflet CSS Library -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --bg-main: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            padding: 30px;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
        }

        /* Header Style */
        .header-dashboard {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            padding: 25px 35px;
            border-radius: 16px;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246,.3);
        }

        .header-dashboard h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header-dashboard p {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }

        .badge-stat {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Card Layout */
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Map Container */
        #map {
            width: 100%;
            height: 520px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            z-index: 1;
        }

        /* Action bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-input {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-input:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3);
        }

        /* Modern Table */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            white-space: nowrap;
        }

        th, td {
            padding: 14px 18px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        td {
            border-bottom: 1px solid var(--border-color);
            color: #334155;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f8fafc;
        }

        /* Table Action Buttons */
        .badge-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .badge-edit {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
        }

        .badge-edit:hover {
            background-color: #d1fae5;
        }

        .badge-delete {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
        }

        .badge-delete:hover {
            background-color: #fee2e2;
        }

        /* Custom Leaflet Tooltip & Popup styling */
        .leaflet-popup-content-wrapper {
            border-radius: 10px;
            padding: 5px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .leaflet-popup-content {
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: #1f2937;
            line-height: 1.5;
        }

        /* Styling Custom Marker (Modern Pin) */
        .custom-water-marker {
            background-color: #2563eb;
            border: 2px solid #ffffff;
            border-radius: 50% 50% 50% 0;
            width: 26px;
            height: 26px;
            transform: rotate(-45deg);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .custom-water-marker::after {
            content: '';
            width: 8px;
            height: 8px;
            background-color: white;
            border-radius: 50%;
            transform: rotate(45deg);
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header Elegan ala Dashboard Premium -->
    <div class="header-dashboard">
        <div>
            <h1>Web GIS Sebaran Mata Air</h1>
        </div>
        <div class="badge-stat">
            📍 Total Titik: <strong><?php echo $total_data; ?> Lokasi</strong>
        </div>
    </div>

    <!-- Peta Card -->
    <div class="card">
        <div class="card-title">
            🗺️ Sebaran Titik Mata Air Kabupaten Temanggung
        </div>
        <div id="map"></div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="action-bar">
            <div class="card-title" style="margin-bottom:0;">
                📋 Tabel Database Titik Mata Air Kabupaten Temanggung
            </div>
            <a href="input/index.html" class="btn-input">
                <span>➕</span> Input Titik Baru
            </a>
        </div>

        <div class="table-responsive">
            <?php
            if ($result->num_rows > 0) {
                echo "<table>
                <tr>
                    <th>Nama Desa</th>
                    <th>Nama Mata Air</th>
                    <th>Kecamatan</th>
                    <th>Longitude (X)</th>
                    <th>Latitude (Y)</th>
                    <th style='text-align: center;'>Aksi</th>
                </tr>";
                
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td><strong>" . $row["nama_desa"] . "</strong></td>
                        <td>" . $row["nama_mata"] . "</td>
                        <td>" . $row["kecamatan"] . "</td>
                        <td><code>" . $row["point_x"] . "</code></td>
                        <td><code>" . $row["point_y"] . "</code></td>
                        <td style='text-align: center;'>
                            <a href='edit/index.php?id=" . $row["id"] . "' class='badge-action badge-edit'>Edit</a> 
                            <a href='delete.php?id=" . $row["id"] . "' class='badge-action badge-delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data mata air ini?\")'>Hapus</a>
                        </td>
                    </tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: var(--text-muted); text-align: center; padding: 20px;'>Belum ada data mata air yang tersimpan di dalam database.</p>";
            }
            ?>
        </div>
    </div>
</div>

<!-- Leaflet JavaScript Library -->
<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>
<script>
    /* Initial Map di pusat Temanggung */
    var map = L.map('map').setView([-7.3115, 110.1653], 11);

    /* Tile Basemap Premium */
    var basemap1 = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri'
    });

    var basemap2 = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: 'Map data &copy; Google'
    });

    basemap1.addTo(map);

    // Group layer untuk marker dari database
    var markerLayer = L.layerGroup().addTo(map);

    /* Definisi Custom Icon Pin Modern */
    var customWaterIcon = L.divIcon({
        className: 'custom-icon-wrapper',
        html: '<div class="custom-water-marker"></div>',
        iconSize: [26, 26],
        iconAnchor: [13, 26],
        popupAnchor: [0, -28]
    });

    <?php
    // Reset pointer result untuk pemetaan PHP ke JS Leaflet
    $result->data_seek(0);
    while($row = $result->fetch_assoc()) {
        $lat = $row['point_y'];
        $lng = $row['point_x'];
        $nama_mata = addslashes($row['nama_mata']);
        $nama_desa = addslashes($row['nama_desa']);
        $kecamatan = addslashes($row['kecamatan']);
        
        if (!empty($lat) && !empty($lng)) {
            // Memasukkan custom icon ke dalam L.marker
            echo "var marker = L.marker([$lat, $lng], {icon: customWaterIcon});\n";
            // Tooltip dengan animasi bersih saat hover
            echo "marker.bindTooltip('$nama_mata', {permanent: false, direction: 'top', className: 'custom-tooltip'});\n";
            // Popup saat diklik
            echo "marker.bindPopup('<div style=\"padding:4px;\"><b style=\"font-size:14px; color:#2563eb;\">$nama_mata</b><hr style=\"margin:6px 0; border:0; border-top:1px solid #eee;\"><b>Desa:</b> $nama_desa<br><b>Kecamatan:</b> $kecamatan</div>');\n";
            echo "marker.addTo(markerLayer);\n";
        }
    }
    ?>

    /* Control Layer */
    var baseMaps = {
        "Esri World Imagery": basemap1,
        "Google Streets": basemap2
    };

    var overlayMaps = {
        "Titik Sebaran Mata Air": markerLayer
    };

    L.control.layers(baseMaps, overlayMaps, {collapsed: false}).addTo(map);

    /* Scale Bar */
    L.control.scale({
        maxWidth: 150, position: 'bottomright'
    }).addTo(map);
</script>

</body>
</html>
<?php
$conn->close();
?>