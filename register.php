<?php
session_start();
include 'koneksi.php'; 

$error = null; // Inisialisasi variabel error

if (isset($_POST['submit'])) {
    // Ambil data POST di awal
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. VALIDASI INPUT DASAR
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Semua field wajib diisi.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } 
    
    // HANYA LANJUTKAN PROSES KE DB JIKA TIDAK ADA ERROR VALIDASI
    if (!$error) {
        
        // 2. Cek apakah Username sudah ada
        $stmt = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username sudah terdaftar. Silakan gunakan username lain.";
            $stmt->close();
        } else {
            $stmt->close();

            // 3. Proses Insert ke DB
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; 

            $query_insert = "INSERT INTO user (username, password, role) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($query_insert);
            
            // JANGAN PAKAI $password di sini, karena $password hanya digunakan di atas
            $stmt_insert->bind_param("sss", $username, $hashed_password, $role);

            if ($stmt_insert->execute()) {
                // Pendaftaran Berhasil
                $_SESSION['notif_message'] = "Pendaftaran berhasil! Silakan Login.";
                $_SESSION['notif_type'] = 'success';
                
                header('Location: login.php');
                exit();
            } else {
                // Error jika INSERT gagal
                $error = "Gagal mendaftarkan pengguna. Error: " . $conn->error;
            }
            $stmt_insert->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fontawesome/css/font-awesome.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <style>
        /* CSS yang sama dengan login.php untuk konsistensi */
        .background-blur {
            background: url('gambar/bg_lelang.jpg') no-repeat center center fixed; /* Ganti path gambar Anda */
            background-size: cover;
            filter: blur(5px);
            position: absolute;
            top: -5px; bottom: -5px; left: -5px; right: -5px;
            z-index: -1;
        }
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            overflow: hidden;
            position: relative;
        }
        .login-card { /* Digunakan juga untuk register card */
            width: 100%;
            max-width: 400px; 
            background: rgba(255, 255, 255, 0.9); 
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            position: relative;
        }
        .login-card h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #333;
        }
        .btn-info { /* Tombol warna pink/ungu */
            background-color: #f06292;
            border-color: #f06292;
            width: 100%;
            border-radius: 25px;
            color:white;
            font-weight: bold;
            padding: 10px;
        }
        .btn-info:hover {
            background-color: #e91e63;
            border-color: #e91e63;
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
</head>
<body>
    <div class="background-blur"></div>

    <div class="login-card">
        <h2 class="text-center">Daftar Akun Baru</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="register.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
             <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" name="submit" class="btn-info">Daftar</button>
        </form>
        <p class="text-center mt-3"><small>Sudah punya akun? <a href="login.php">Login di sini</a></small></p>
    </div>
</body>
</html>