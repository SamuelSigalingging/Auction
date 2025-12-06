<?php
session_start();
include 'koneksi.php';


if (!isset($_SESSION['username'])) {
    echo "<script>
            alert('Anda harus login terlebih dahulu!');
            window.location.href = 'login.php';
          </script>";
    exit;
}

if (isset($_POST['submit'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $lokasi = $_POST['lokasi'];
    $durasi = $_POST['durasi'];
    $status = 'Belum Diverifikasi';

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $fileTmpPath = $_FILES['gambar']['tmp_name'];
        $fileName = $_FILES['gambar']['name'];
        $fileSize = $_FILES['gambar']['size'];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png'];
        if (!in_array($fileType, $allowedTypes)) {
            echo "<div class='alert alert-danger'>Hanya file JPG, JPEG, dan PNG yang diperbolehkan.</div>";
            exit;
        }

        $uploadDir = './gambar/';
        $destinationPath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            $gambar = $destinationPath;

            // Insert auction item into the database
            $query = "INSERT INTO items (judul, deskripsi, harga, lokasi, status, durasi, gambar) 
                      VALUES ('$judul', '$deskripsi', '$harga', '$lokasi', '$status', '$durasi', '$gambar')";

            if (mysqli_query($conn, $query)) {
                echo "<div class='alert alert-success'>Barang berhasil diajukan untuk lelang!</div>";
                header("refresh:2;url=index.php");
            } else {
                echo "<div class='alert alert-danger'>Kesalahan: " . mysqli_error($conn) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Gagal mengunggah file gambar.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Tidak ada file yang diunggah.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Barang Lelang</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fontawesome/css/font-awesome.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<style>
        /* Gradient Warna Anda */
        .navbar {
            background: #2D5493;
            border-bottom: 2px color(255, 255, 255, 0.5);
        }

        .navbar-brand,
        .nav-link,
        .navbar-text {
            color: white !important;
        }

        /* ... (CSS Tombol Info, Hover, dll. yang sudah ada) ... */

        .navbar-menu {
            background-color: #f8f9fa !important;
            /* Tambahkan border-top untuk memperjelas pemisahan jika diperlukan */
            border-top: 1px solid #dee2e6;
        }

        .navbar-menu .nav-link {
            color: #343a40 !important;
            font-weight: 500;
            text-transform: uppercase;
            padding-right: 1.5rem;
        }

        /* Styling Tombol Normal (misalnya tombol Login Anda) */
        .btn-info {
            background-color: #3F8AFA;
            border-color: white;
            color: white;
        }

        .btn-info:hover {
            background-color: #032A63;
            border-color: #032A63;
            color: white;
        }

        .navbar .nav-link:hover {
            color: blue !important;
        }

        .carousel-fixed-height {
            height: 80px;
            /* Coba nilai ini */
            overflow: hidden;
            /* Tambahkan margin-top/bottom jika perlu, tapi untuk header, biarkan 0 */
        }

        .carousel-fixed-height img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            margin-top: 20px;
        }

        .card {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
    </style>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="gambar/logo.png" alt="Logo LELANGZ" height="40">
            </a>

            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['username'])): ?>
                        <span class="navbar-text me-3">Selamat datang, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>!</span>
                        <a href="logout.php" class="btn btn-info">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-info">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <nav class="navbar navbar-expand-lg navbar-menu bg-light">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav mx-auto justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">INFO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ajukan.php">AYO LELANG</a>
                    </li>
                </ul>

                <form class="d-flex ms-auto col-lg-4" action="index.php" method="GET">
                    <input class="form-control me-2" type="search" placeholder="Cari berdasarkan nama..." aria-label="Search" name="keyword" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                    <button class="btn btn-info" type="submit">Cari</button>
                </form>

            </div>
        </div>
    </nav>
<!-- Navbar End -->

<div class="container mt-5">
    <h2>Ajukan Barang untuk Lelang</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="judul" class="form-label">Judul Barang</label>
            <input type="text" class="form-control" id="judul" name="judul" required>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Barang</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label for="harga" class="form-label">Harga Awal</label>
            <input type="number" class="form-control" id="harga" name="harga" required>
        </div>
        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi</label>
            <input type="text" class="form-control" id="lokasi" name="lokasi" required>
        </div>
        <div class="mb-3">
            <label for="durasi" class="form-label">Durasi Lelang (hari)</label>
            <input type="number" class="form-control" id="durasi" name="durasi" required>
        </div>
        <div class="mb-3">
            <label for="gambar" class="form-label">Unggah Gambar</label>
            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-arrow-up"></i>  Ajukan Barang</button>
    </form>
</div>

</body>
</html>
