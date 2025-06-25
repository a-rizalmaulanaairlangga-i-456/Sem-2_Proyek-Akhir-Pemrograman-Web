<?php
    session_start();
    include '../koneksi.php'; 

    // Pastikan user sudah login dan memiliki peran dosen
    if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'dosen') {
        header("Location: ../login.php");
        exit;
    }

    // Ambil data dari form POST
    $nama_tugas = $_POST['nama_tugas'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $mk_id = $_POST['mk_id'] ?? '';

    // Validasi jika value sudah diinputkan dan id mata kuliah berhasil dikirim dan diterima
    if ($nama_tugas && $mk_id) {
        // Simpan ke database
        $query = "INSERT INTO tugas (mk_id, nama, deskripsi) VALUES ('$mk_id', '$nama_tugas', '$deskripsi')";
        $result = mysqli_query($kon, $query);

        if ($result) {
            $_SESSION['notif'] = "Tugas berhasil dibuat!";
        } else {
            $_SESSION['notif'] = "Gagal membuat tugas: " . mysqli_error($kon);
        }
    } else {
        $_SESSION['notif'] = "Harap lengkapi semua data tugas.";
    }

    // Redirect kembali ke halaman akses_kelas
    header("Location: kelas_dosen.php?mk_id=$mk_id");
    exit;
?>
