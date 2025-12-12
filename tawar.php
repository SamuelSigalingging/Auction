<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id = $_GET['id'];
$error = null;

if (!isset($_SESSION['id_user'])) {
    $_SESSION['notif_type'] = 'warning';
    $_SESSION['notif_message'] = 'Anda harus Login untuk mengajukan penawaran.';
    header("Location: login.php");
    exit();
}

$stmt_item = $conn->prepare("SELECT id, judul, deskripsi, harga, lokasi, durasi, status, gambar FROM items WHERE id = ?");
$stmt_item->bind_param("i", $id);
$stmt_item->execute();
$result_item = $stmt_item->get_result();

if ($result_item->num_rows === 0) {
    header("Location: index.php");
    exit();
}
$result = $result_item->fetch_assoc();
$harga_saat_ini = $result['harga'];

if ($result['status'] == 'Belum Diverifikasi') {
    $_SESSION['notif_type'] = 'danger';
    $_SESSION['notif_message'] = 'Barang ini belum diverifikasi. Anda tidak dapat menawar sekarang.';
    header("Location: index.php");
    exit;
} elseif ($result['status'] == 'Terjual') {
    $_SESSION['notif_type'] = 'danger';
    $_SESSION['notif_message'] = 'Barang ini sudah terjual. Anda tidak dapat menawar sekarang.';
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $hargaBaru = $_POST['harga'];

    if (!is_numeric($hargaBaru) || $hargaBaru <= 0) {
        $error = 'Penawaran harus berupa angka yang valid dan lebih besar dari nol.';
    } elseif ($hargaBaru <= $harga_saat_ini) {
        $harga_format = number_format($harga_saat_ini, 0, ',', '.');
        $error = "Harga yang diajukan harus lebih tinggi dari harga saat ini (Rp {$harga_format}).";
    }

    if (!$error) {
        $updateQuery = "UPDATE items SET harga = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt_update, "di", $hargaBaru, $id);

        if (mysqli_stmt_execute($stmt_update)) {

            $_SESSION['notif_type'] = 'success';
            $_SESSION['notif_message'] = 'Penawaran Anda sebesar Rp ' . number_format($hargaBaru, 0, ',', '.') . ' berhasil diajukan!';

            header("Location: tawar.php?id=$id");
            exit();
        } else {
            $error = 'Terjadi kesalahan saat memperbarui harga di database: ' . mysqli_error($conn);
        }
    }
}

$notif_script = '';
if (isset($_SESSION['notif_message'])) {
    $msg = $_SESSION['notif_message'];
    $type = $_SESSION['notif_type'];
    $title = ($type == 'success' ? 'Berhasil!' : ($type == 'warning' ? 'Perhatian!' : 'Error!'));

    unset($_SESSION['notif_message']);
    unset($_SESSION['notif_type']);

    $notif_script = "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastEl = document.getElementById('liveToast');
            var toast = new bootstrap.Toast(toastEl);
            
            toastEl.querySelector('.toast-title').innerText = '$title';
            toastEl.querySelector('.toast-body').innerHTML = '<span class=\"text-$type fw-bold\">$msg</span>';

            var header = toastEl.querySelector('.toast-header');
            header.classList.remove('bg-danger', 'bg-warning', 'bg-success'); 
            header.classList.add('bg-' + '$type', 'text-white'); 
            
            toast.show();
        });
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($result['judul']); ?> - Tawar</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fontawesome/css/font-awesome.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>

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
                    <input class="form-control me-2" type="search" placeholder="Cari berdasarkan nama..." aria-label="Search" name="keyword" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                    <button class="btn btn-info2" type="submit">Cari</button>
                </form>

            </div>
        </div>
    </nav>
    <!-- Navbar End -->

    <div class="container mt-4">
        <div class="table-responsive">
            <table class="table align-middle table-hover table-bordered">
                <thead>
                    <tr class="table-primary">
                        <th>
                            <center>Nama Item<center>
                        </th>
                        <th>Deskripsi</th>
                        <th>Harga Saat Ini</th>
                        <th>Lokasi</th>
                        <th>Durasi(Hari)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <center><?php echo $result['judul']; ?></center>
                        </th>
                        <td><?php echo $result['deskripsi']; ?></td>
                        <td>Rp <?php echo number_format($result['harga'], 0, ',', '.'); ?></td>
                        <td><?php echo $result['lokasi']; ?></td>
                        <td><?php echo $result['durasi']; ?></td>
                        <td><?php echo $result['status']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="container mt-5">
            <h2>Ajukan Harga Tertinggi Mu</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="harga" class="form-label">Harga Penawaran (Harus > Rp <?php echo number_format($harga_saat_ini, 0, ',', '.'); ?>)</label>
                    <input type="number" step="any" min="<?php echo $harga_saat_ini + 1; ?>" class="form-control" id="harga" name="harga" required>
                </div>
                <button type="submit" class="btn btn-info" id="ajukan">Ajukan Harga</button>
                <a href="pembelian.php?id=<?php echo $id; ?>" type="button" class="btn btn-success">Beli Sekarang</a>
            </form>
        </div>

        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto toast-title">Pesan Sistem</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body toast-message">
                </div>
            </div>
        </div>
        </body>

</html>