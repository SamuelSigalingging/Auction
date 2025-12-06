<?php
include 'koneksi.php';

$id = $_GET['id'];
$query = "SELECT * FROM items WHERE id = $id";
$sql = mysqli_query($conn, $query);
$result = mysqli_fetch_assoc($sql);

if (!$result) {
    echo "Item tidak ditemukan.";
    exit;
}

// Mengecek apakah barang sudah terjual
if ($result['status'] == 'Terjual') {
    echo "<script>alert('Item sudah terjual.'); window.location.href = 'index.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaPembeli = $_POST['nama'];
    $hargaPembeli = $_POST['jumlah'];  // Harga yang diajukan oleh pembeli
    $hargaBarang = $result['harga'];  // Harga barang yang tertera

    // Cek apakah harga yang diajukan lebih tinggi atau sama dengan harga barang
    if ($hargaPembeli >= $hargaBarang) {
        date_default_timezone_set('Asia/Jakarta');
        $waktuTransaksi = date('Y-m-d H:i:s');

        // Menambahkan data transaksi ke tabel transaksi
        $transaksiQuery = "INSERT INTO transaksi (id, nama_pembeli, total_harga, waktu_transaksi) 
                           VALUES ($id, '$namaPembeli', $hargaPembeli, '$waktuTransaksi')";

        // Memperbarui status barang menjadi Terjual
        $updateQuery = "UPDATE items SET status = 'Terjual' WHERE id = $id";

        if (mysqli_query($conn, $transaksiQuery) && mysqli_query($conn, $updateQuery)) {
            echo "<script>alert('Pembelian berhasil! Terima kasih telah membeli.'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat memproses pembelian.'); window.location.href = 'index.php';</script>";
        }
    } else {
        echo "<script>alert('Harga yang diajukan tidak cukup untuk membeli item ini.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
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
    <h2>Detail Pembelian</h2>
    <table class="table table-bordered">
        <tr>
            <th>Nama Item</th>
            <td><?php echo $result['judul']; ?></td>
        </tr>
        <tr>
            <th>Harga</th>
            <td><?php echo $result['harga']; ?></td>
        </tr>
        <tr>
            <th>Deskripsi</th>
            <td><?php echo $result['deskripsi']; ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo $result['status']; ?></td>
        </tr>
    </table>

    <h2>Form Pembelian</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Pembeli</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah Uang</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" required> 
        </div>
        <div class="mb-3">
            <label for="metode pembayaran" class="form-label">Pilih Metode Pembayaran</label>
            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                <option value="" disabled selected>-- Pilih Opsi --</option>
                <option value="transfer_bank">Transfer Bank (BCA/Mandiri)</option>
                <option value="dana">E-Wallet (Dana/Gopay)</option>
                <option value="kartu_kredit">Kartu Kredit / Debit</option>
                <option value="cod">Cash on Delivery (COD)</option>
            </select> 
        </div>
        <button type="submit" class="btn btn-info">Konfirmasi Pembelian</button>
    </form>
</div>
</body>
</html>
