<?php
include 'koneksi.php';

$id = $_GET['id'];  
$query = "SELECT * FROM items WHERE id = $id";
$sql = mysqli_query($conn, $query);
$result = mysqli_fetch_assoc($sql);

if (!$result) {
    echo "Barang tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id']; 
    $judul = $_POST['judul']; 
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga']; 
    $lokasi = $_POST['lokasi'];  
    $durasi = $_POST['durasi'];  

    $updateQuery = "UPDATE items SET 
                    judul = '$judul', 
                    deskripsi = '$deskripsi', 
                    harga = '$harga', 
                    lokasi = '$lokasi', 
                    durasi = '$durasi' 
                    WHERE id = $id";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Barang berhasil diupdate!'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat mengupdate barang.'); window.location.href = 'index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Barang</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fontawesome/css/font-awesome.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>

<style>
        .navbar {
            background: #3F8AFA;
            border-bottom: 2px color(255, 255, 255, 0.5);
        }

        .navbar-brand,
        .nav-link,
        .navbar-text {
            color: white !important;
        }

        .navbar-menu {
            background-color: #f8f9fa !important;
            border-top: 1px solid #dee2e6;
        }

        .navbar-menu .nav-link {
            color: #343a40 !important;
            font-weight: 500;
            text-transform: uppercase;
            padding-right: 1.5rem;
        }

        .btn-info {
            background-color: #3F8AFA;
            border-color: white;
            color: white;
        }

        .btn-info2 {
            background-color: #3F8AFA;
            border-color: white;
            color: white;
            width: 90px;
        }

        .btn-info2:hover {
            background-color: #032A63;
            border-color: #032A63;
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
            overflow: hidden;
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

        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid transparent;
            border-radius: 4px;
            text-align: center;
        }
    </style>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
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
                    <input class="form-control me-2" type="search" placeholder="Cari judul..." aria-label="Search" name="keyword" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                    <button class="btn btn-info2" type="submit">Cari</button>
                </form>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->

<div class="container mt-5">
    <h2>Form Update Barang</h2>
    <form action="ubah_admin.php?id=<?php echo $result['id']; ?>" method="POST">
        <input type="hidden" name="id" value="<?php echo $result['id']; ?>">

        <div class="mb-3">
            <label for="judul" class="form-label">Judul Barang</label>
            <input type="text" class="form-control" id="judul_baru" name="judul" value="<?php echo $result['judul']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Barang</label>
            <textarea class="form-control" id="deskripsi_baru" name="deskripsi" required><?php echo $result['deskripsi']; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="harga" class="form-label">Harga</label>
            <input type="text" class="form-control" id="harga_baru" name="harga" value="<?php echo $result['harga']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="lokasi" class="form-label">Lokasi</label>
            <input type="text" class="form-control" id="lokasi_baru" name="lokasi" value="<?php echo $result['lokasi']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="durasi" class="form-label">Durasi (Hari)</label>
            <input type="number" class="form-control" id="durasi_baru" name="durasi" value="<?php echo $result['durasi']; ?>" required>
        </div>

        <button type="submit" class="btn btn-info" id="update">
            <i class="fa fa-pencil"></i> Update Barang
        </button>
    </form>
</div>

</body>
</html>
