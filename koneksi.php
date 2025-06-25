<?php
    // Konfigurasi koneksi ke MySQL
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "pemrograman-web_prak.9-10.php-mysql-database";

    // Membuat koneksi menggunakan MySQLi
    $kon = mysqli_connect($servername, $username, $password, $dbname);  // $kon akan bernilai sebuah objek koneksi yang valid

    // Cek koneksi
    if (!$kon) {
        die("Koneksi gagal: " . mysqli_connect_error());    // mysqli_connect_error akan berisi pesan error
    }
?>