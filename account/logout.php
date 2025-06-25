<?php
    session_start();
    session_destroy();      // Hancurkan sesi
    header("Location: http://localhost/Praktikum%20Pemrograman%20Web/Praktikum%209-10.%20PHP%20DataBase%20MySQL/halaman_utama.php");     // Kembali ke menu utama
    exit;
?>
