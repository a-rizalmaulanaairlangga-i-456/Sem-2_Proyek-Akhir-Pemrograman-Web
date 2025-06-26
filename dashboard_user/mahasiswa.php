<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include __DIR__ . '/../koneksi.php';

    // Cek login dan role
    if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
        header("Location: ../halaman_utama.php");
        exit;
    }

    include __DIR__ . '/../navigasi.php';
    $mahasiswa = $_SESSION['user'];
    $id_mhs = $mahasiswa['user_id'];
    
    // mengambil informasi data mata kuliah dan dosennya
    $query = "
        SELECT mkd.id, mkd.nama_mk, d.nama AS nama_dosen 
        FROM mata_kuliah_mhs mm
        JOIN mata_kuliah_dosen mkd ON mm.mk_id = mkd.id
        JOIN data_dosen d ON mkd.dosen_id = d.id
        WHERE mm.mhs_id = '$id_mhs'
    ";

    $result = mysqli_query($kon, $query);

    // Debug jika query gagal
    if (!$result) {
        die("Query gagal: " . mysqli_error($kon));
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Mahasiswa</title>
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

    <div class="px-8 pb-6 pt-24">
        <h1 class="text-3xl font-bold mb-6">Dashboard Mahasiswa</h1>

        <!-- List Mata Kuliah -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white/65 hover:bg-white/85 transition ease-in-out duration-100 rounded-xl shadow-md p-5">
                    <h2 class="text-xl font-semibold"><?= $row['nama_mk'] ?></h2>
                    <p class="text-md mb-6"><?= $row['nama_dosen'] ?></p>
                    <a href="/Praktikum Pemrograman Web/Praktikum 9-10. PHP DataBase MySQL/dashboard_user/kelas_mhs.php?mk_id=<?= $row['id'] ?>" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-400 active:bg-blue-600 transition ease-in-out duration-00">Akses Kelas</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php
        mysqli_data_seek($result, 0);
    ?>
</body>
</html>
