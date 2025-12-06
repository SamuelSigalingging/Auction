<?php
session_start();
session_destroy(); // Mengakhiri sesi
header('Location: login.php'); // Arahkan kembali ke halaman login setelah logout
exit();
?>
