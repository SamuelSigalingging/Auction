<?php
session_start();
include 'koneksi.php';

$error = null;

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password_input = $_POST['password'];
    
    $admin_username = "admin";
    $admin_password = "admin123";

    if ($username === $admin_username && $password_input === $admin_password) {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $username;
        header('Location: admin.php');
        exit(); 
    }
    
    $stmt = $conn->prepare("SELECT id_user, username, password, role FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    
    if (!$stmt) {
        $error = "Gagal menyiapkan query: " . $conn->error;
    } else {
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password_input, $user['password'])) {
                // Login User Berhasil
                $_SESSION['role'] = $user['role']; 
                $_SESSION['username'] = $user['username'];
                $_SESSION['id_user'] = $user['id_user']; 
                
                header('Location: index.php'); // Arahkan semua user ke index (kecuali admin statis)
                exit();
            }
        }
    }
    
    // --- JIKA GAGAL (Username tidak ditemukan ATAU Password salah) ---
    $error = "Username atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fontawesome/css/font-awesome.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <style>
        /* GANTI PATH INI */
        .background-blur {
            background: url('gambar/background2.png') no-repeat center center fixed;
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
            overflow: hidden; /* Sembunyikan scrollbar yang muncul karena blur */
            position: relative;
        }
        .login-card {
            width: 100%;
            max-width: 350px; /* Sedikit lebih kecil */
            background: rgba(255, 255, 255, 0.9); /* Card agak transparan */
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            position: relative; /* Penting agar muncul di atas background */
        }
        .login-card h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #333;
        }

        .btn-info { 
            background-color: #3F8AFA;
            border-color: white;
            width: 100%;
            border-radius: 25px;
            color:white;
            font-weight: bold;
            padding: 10px;
        }

        .btn-info:hover {
            background-color: #032A63;
            border-color: #032A63;
            color: white;
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
    <div class="background-blur"></div> <div class="login-card">
        <h2 class="text-center">Selamat Datang</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="submit" class="btn-info">Login</button>
        </form>
        <p class="text-center mt-3"><small>Belum punya akun? <a href="register.php">Daftar di sini</a></small></p>
    </div>
</body>
</html>