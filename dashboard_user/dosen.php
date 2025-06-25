<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include __DIR__ . '/../koneksi.php';

    // Cek role
    if ($_SESSION['role'] !== 'dosen') {
        echo "Akses ditolak.";
        exit;
    }

    $dosen_id = $_SESSION['user']['user_id'];

    // Ambil daftar mata kuliah
    $mk_query = "SELECT * FROM mata_kuliah_dosen WHERE dosen_id = $dosen_id";
    $mk_result = $kon->query($mk_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Untuk mensetting background agar memiliki efek blur */
        html, body {
            height: 100%;
        }

        body::before {
            content: '';
            position: fixed;
            width: 100%;
            height: 100%;
            background-image: url('https://4kwallpapers.com/images/walls/thumbs_3t/21290.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(20px);
            z-index: -1;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/../navigasi.php'; ?>

    <div class="px-8 pb-6 pt-24">
        <h1 class="text-3xl font-bold mb-6">Dashboard Dosen</h1>

        <!-- List Mata Kuliah -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($mk = $mk_result->fetch_assoc()): ?>
                <div class="bg-white/65 hover:bg-white/85 transition ease-in-out duration-100 rounded-xl shadow-md p-4">
                    <h2 class="text-xl font-semibold mb-4"><?= $mk['nama_mk'] ?></h2>
                    <a href="/Praktikum Pemrograman Web/Praktikum 9-10. PHP DataBase MySQL/dashboard_user/kelas_dosen.php?mk_id=<?= $mk['id'] ?>" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-400 active:bg-blue-600 transition ease-in-out duration-00">Akses Kelas</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
