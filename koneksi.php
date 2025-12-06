<?php  
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'auction';
    $port = '3306';

    $conn = mysqli_connect( $host, $user, $pass, $db, $port);
        if ($conn) {
    } else {
        echo "Koneksi gagal: " . mysqli_connect_error();
    }




    

?>