<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['verifikasi_id'])) {
    $verifikasi_id = mysqli_real_escape_string($conn, $_GET['verifikasi_id']);
    $query = "UPDATE items SET status ='Diverifikasi' WHERE id='$verifikasi_id'";
    $msg = mysqli_query($conn, $query) ? "Barang sudah diverifikasi!" : "Gagal memverifikasi barang.";
}

// if (isset($_GET['verifikasi_id'])) {
//     $verifikasi_id = mysqli_real_escape_string($conn, $_GET['verifikasi_id']);
//     $query = "UPDATE items SET status_verifikasi='Diverifikasi' WHERE id='$verifikasi_id'";
//     if (mysqli_query($conn, $query)) {
//         $msg = "Barang sudah diverifikasi!";
//     } else {
//         $msg = "Gagal memverifikasi barang.";
//     }
// }


if (isset($_GET['update_status_id'])) {
    $update_status_id = mysqli_real_escape_string($conn, $_GET['update_status_id']);
    $new_status = mysqli_real_escape_string($conn, $_GET['status']);
    $query = "UPDATE items SET status='$new_status' WHERE id='$update_status_id'";
    $msg = mysqli_query($conn, $query) ? "Status barang sudah diperbarui!" : "Gagal memperbarui status barang.";
}

if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);

    $deleteTransaksiQuery = "DELETE FROM transaksi WHERE id='$delete_id'";
    $deleteTransaksiResult = mysqli_query($conn, $deleteTransaksiQuery);

    if ($deleteTransaksiResult) {
        $deleteItemQuery = "DELETE FROM items WHERE id='$delete_id'";
        $msg = mysqli_query($conn, $deleteItemQuery) ? "Barang berhasil sudah dihapus!" : "Gagal menghapus barang dari tabel items.";
    } else {
        $msg = "Gagal menghapus data terkait dari tabel transaksi.";
    }
}


// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_all'])) {
//     $update_all = mysqli_real_escape_string($conn, $_POST['update_all']);
//     $new_judul = mysqli_real_escape_string($conn, $_POST['judul']);
//     $new_deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
//     $new_harga = mysqli_real_escape_string($conn, $_POST['harga']);
//     $new_lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
//     $new_durasi = mysqli_real_escape_string($conn, $_POST['durasi']);

//     $query = "UPDATE items 
//               SET judul = '$new_judul',
//                   deskripsi = '$new_deskripsi',
//                   harga = '$new_harga',
//                   lokasi = '$new_lokasi',
//                   durasi = '$new_durasi'
//               WHERE id = '$update_all'";

//     $msg = mysqli_query($conn, $query) 
//            ? "Data barang berhasil diperbarui!" 
//            : "Gagal memperbarui data barang.";
// }

$query = "SELECT * FROM items";
$sql = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Lelang</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fontawesome/css/font-awesome.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="waktu.js"></script>
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
    <!-- Navbar End -->

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Kelola Barang Lelang</h1>

                <!-- Pesan Notifikasi -->
                <?php if (isset($msg)) { ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?php echo $msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php } ?>

                <!-- Tabel Data Barang -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col"><center>ID<center></th>
                                <th scope="col">Judul</th>
                                <th scope="col">Deskripsi</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Durasi (Hari)</th>
                                <th scope="col">Status item</th>
                                <th scope="col">Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($result = mysqli_fetch_assoc($sql)) { ?>
                            <tr>
                                <td><center><?php echo $result['id']; ?><center></td>
                                <td><?php echo $result['judul']; ?></td>
                                <td><?php echo $result['deskripsi']; ?></td>
                                <td>Rp <?php echo number_format($result['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $result['lokasi']; ?></td>
                                <td id="countdown"><?php echo $result['durasi']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo ($result['status'] == 'Terjual') ? 'success' : ($result['status'] == 'Tersedia' ? 'primary' : 'warning'); ?>">
                                        <?php echo $result['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="admin.php?update_status_id=<?php echo $result['id']; ?>&status=Terjual"
                                            class="btn btn-success btn-sm">Terjual</a>
                                        <a href="admin.php?update_status_id=<?php echo $result['id']; ?>&status=Tersedia"
                                            class="btn btn-primary btn-sm">Tersedia</a>
                                        <a href="admin.php?delete_id=<?php echo $result['id']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</a>
                                            <a href="ubah_admin.php?id=<?php echo $result['id']; ?>"
                                            class="btn btn-info btn-sm">Ubah</a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
