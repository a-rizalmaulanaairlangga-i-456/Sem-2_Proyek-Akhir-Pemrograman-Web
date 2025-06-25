<?php
    session_start();
    include '../koneksi.php';

    // pastikan hanya role admin yang bisa menghapus data
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        die("<script>alert('Akses ditolak! Hanya admin yang bisa menghapus data.'); window.location='../halaman_utama.php';</script>");
    }

    // pastikan parameter role lengkap (ada role dan ada id)
    if (!isset($_GET['role']) || !isset($_GET['id'])) {
        die("<script>alert('Parameter tidak lengkap!'); window.location='../halaman_utama.php';</script>");
    }

    $role = $_GET['role'];
    $id = $_GET['id'];

    // validasi role yang dikirim
    $allowed_roles = ['admin', 'dosen', 'mahasiswa'];
    if (!in_array($role, $allowed_roles)) {
        die("<script>alert('Role tidak valid!'); window.location='../halaman_utama.php';</script>");
    }

    // definisikan nama tabel, identifier dan email sesuai role
    switch ($role) {
        case 'mahasiswa':
            $table = 'data_mhs';
            $id_field = 'nrp';
            $email_field = 'email_student';
            break;
        case 'dosen':
            $table = 'data_dosen';
            $id_field = 'nip';
            $email_field = 'email_lecturer';
            break;
        case 'admin':
            $table = 'data_admin';
            $id_field = 'nip';
            $email_field = 'email';
            break;
    }

    // hindari penghapusan data diri sendiri oleh admin
    if ($role == 'admin' && $id == $_SESSION['user']['user_id']) {
        die("<script>alert('Tidak bisa menghapus akun sendiri!'); window.location='../halaman_utama.php';</script>");
    }

    // mulai transaction
    $kon->begin_transaction();

    try {
        // 1. dapatkan email dan tabel yang sesuai
        $sql_get_email = "SELECT $email_field FROM $table WHERE $id_field = ?";
        $stmt = $kon->prepare($sql_get_email);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Data tidak ditemukan!");
        }
        
        $data = $result->fetch_assoc();
        $email = $data[$email_field];
        $stmt->close();

        // 2. hapus data yang dipilih
        $sql_delete_data = "DELETE FROM $table WHERE $id_field = ?";
        $stmt = $kon->prepare($sql_delete_data);
        $stmt->bind_param("s", $id);
        
        if (!$stmt->execute()) {
            throw new Exception("Gagal menghapus data: " . $stmt->error);
        }
        $stmt->close();

        // 3. hapus akun yang sesuai jika ada
        $sql_delete_account = "DELETE FROM akun WHERE email = ? AND role = ?";
        $stmt = $kon->prepare($sql_delete_account);
        $stmt->bind_param("ss", $email, $role);
        
        if (!$stmt->execute()) {
            throw new Exception("Gagal menghapus akun: " . $stmt->error);
        }
        $stmt->close();

        // kommit transaction jika semua query berhasil
        $kon->commit();
        
        echo "<script>alert('Data dan akun terkait berhasil dihapus!'); window.location='../halaman_utama.php';</script>";
        
    } catch (Exception $e) {
        // kembalikan transaction jika ada error
        $kon->rollback();
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location='../halaman_utama.php';</script>";
    }
?>