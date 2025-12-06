<?php
session_start(); // Pastikan session dimulai
include 'koneksi.php';

$query = "SELECT * FROM items";
$sql = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

        .card-img-top {
            width: 100%;
            height: 200px;
            /* Jaga agar tinggi gambar selalu 200px */
            object-fit: cover;
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

    <!--Carousel-->
    <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="gambar/lelang2.png" class="d-block w-100" alt="gambar">
            </div>
            <div class="carousel-item">
                <img src="gambar/lelang2.png" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="gambar/lelang2.png" class="d-block w-100" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <!--Carousel-->

    <!-- Card Section -->
    <div class="container mt-4">
    <div class="row d-flex justify-content-center"> 
        
        <?php 
        // Cek jika tidak ada hasil pencarian
        if (mysqli_num_rows($sql) == 0 && isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
            echo '<div class="col-12"><div class="alert alert-info" role="alert">Hasil pencarian untuk "<b>' . htmlspecialchars($_GET['keyword']) . '</b>" tidak ditemukan.</div></div>';
        }
        ?>

        <?php while ($result = mysqli_fetch_assoc($sql)) { ?>
            <?php
            // Pengecekan gambar (tetap dipertahankan)
            $path = "" . $result['gambar'];
            if (!file_exists($path)) {
                // error_log("Gambar tidak ditemukan: " . $path); // Pindahkan logging ke sini
            }
            
            $status_barang = $result['status'];
            $item_id = $result['id'];
            ?>
            
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card shadow-md w-100">
                    
                    <?php if (!empty($result['gambar']) && file_exists($path)) { ?>
                        <img src="<?php echo $path; ?>" class="card-img-top" alt="Gambar Barang">
                    <?php } else { ?>
                        <img src="gambar/default.jpg" class="card-img-top" alt="Gambar Barang">
                    <?php } ?>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $result['judul']; ?></h5>
                        <p class="card-text"><?php echo $result['deskripsi']; ?></p>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            Harga: Rp
                            <?php
                            $harga_angka = $result['harga'];
                            $harga_tampil = number_format($harga_angka, 0, ',', '.');
                            echo $harga_tampil;
                            ?>
                        </li>
                        <li class="list-group-item">Lokasi: <?php echo $result['lokasi']; ?></li>
                        <li class="list-group-item">Status: <strong><?php echo $status_barang; ?></strong></li>
                        <li class="list-group-item">Durasi (hari): <?php echo $result['durasi']; ?></li>
                    </ul>
                    
                    <div class="card-body">
                        <?php 
                        if ($status_barang == 'Tersedia') {
                            
                            echo '<a href="tawar.php?id=' . $item_id . '" class="btn btn-info w-100"><i class="fa fa-gavel"></i> Ajukan Penawaran</a>';
                        
                        } elseif ($status_barang == 'Belum Diverifikasi') {
                            
                            echo '<button class="btn btn-secondary w-100" disabled><i class="fa fa-clock-o"></i> Menunggu Verifikasi</button>';
                            
                        } elseif ($status_barang == 'Terjual') {
                           
                            echo '<button class="btn btn-danger w-100" disabled><i class="fa fa-lock"></i> Lelang Telah Berakhir</button>';
                        } else {
                            
                             echo '<button class="btn btn-secondary w-100" disabled>Status Tidak Valid</button>';
                        }
                        ?>
                    </div>
                    </div>
            </div>
        <?php } ?>
    </div>
</div>

</body>

</html>