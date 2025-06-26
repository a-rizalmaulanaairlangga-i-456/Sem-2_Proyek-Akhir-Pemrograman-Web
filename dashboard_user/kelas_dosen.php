<?php
    session_start();
    include '../koneksi.php';

    // Cek login dan role
    if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
        header("Location: ../halaman_utama.php");
        exit;
    }

    // Pastikan user sudah login dan memiliki peran dosen
    if ($_SESSION['role'] !== 'dosen') {
        echo "Akses ditolak.";
        exit;
    }

    $mk_id = $_GET['mk_id'];
    $dosen_id = $_SESSION['user']['user_id'];

    // Ambil data mata kuliah dan dosen
    $q = "SELECT mkd.nama_mk, d.nama, d.nip 
        FROM mata_kuliah_dosen mkd 
        JOIN data_dosen d ON mkd.dosen_id = d.id 
        WHERE mkd.id = $mk_id 
        AND mkd.dosen_id = $dosen_id";
    $r = $kon->query($q);
    $data = $r->fetch_assoc();

    // Ambil tugas
    $tugas_result = $kon->query("SELECT * FROM tugas WHERE mk_id = $mk_id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelas - <?= $data['nama_mk'] ?></title>
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
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>
    <?php include __DIR__ . '/../navigasi.php'; ?>

    <div class="px-6 pb-6 pt-24">
        <!-- tombol kembali ke halaman sebelumnya -->
        <a href="dosen.php" class="flex w-fit items-center text-lg text-black hover:text-blue-600 hover:underline transition-all duration-300 hover:-translate-x-1 group">
            <i data-feather="arrow-left" class="mr-1 w-4 h-4 group-hover:text-blue-600 transition-all duration-300"></i>
            <span>Kembali</span>
        </a>

        <!-- header kelas -->
        <div class="bg-white/85 rounded-xl p-6 shadow mb-6 mt-3 gap-3">
            <h1 class="text-2xl text-indigo-400 font-bold"><?= $data['nama_mk'] ?></h1>
            <div class="mt-4 flex gap-4">
                <button class="px-4 py-2 bg-green-600 hover:bg-green-500 active:bg-green-700 transition ease-in-out duration-300 text-white border-none rounded cursor-pointer" id="presensiBtn" onclick="presensi()">Buka Presensi</button>
                <button onclick="toggleModalTugas()" class="bg-blue-500 hover:bg-blue-400 active:bg-blue-600 transition ease-in-out duration-300 text-white px-4 py-2 rounded">Buat Tugas</button>
            </div>
        </div>
    
        <!-- Modal Buat Tugas -->
        <div id="modalTugas" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div id="modalBoxTugas" class="text-blue-600 bg-white/85 p-6 rounded-lg shadow w-1/3 transform scale-95 opacity-0 transition duration-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Buat Tugas Baru</h2>
                    <i data-feather="x-circle" onclick="toggleModalTugas()" class="cursor-pointer text-gray-500 hover:text-red-500 transition ease-in-out duration-300"></i>
                </div>
                <form action="proses_tugas.php" method="post">
                    <input type="hidden" name="mk_id" value="<?= $mk_id ?>">
                    <div class="form-group mb-2">
                        <label>Nama Tugas:</label>
                        <input type="text" name="nama_tugas" required class="w-full border rounded px-2 py-1">
                    </div>
                    <div class="form-group mb-4">
                        <label>Deskripsi:</label>
                        <textarea name="deskripsi" class="w-full border rounded px-2 py-1"></textarea>
                    </div>
                    <button type="submit" class="cursor-pointer bg-green-500 hover:bg-green-400 active:bg-green-600 transition ease-in-out duration-300 text-white px-4 py-2 rounded">Buat</button>
                </form>
            </div>
        </div>
    
        <!-- Daftar Tugas -->
        <div class="bg-white/85 rounded-xl p-6 shadow mt-6">
            <h2 class="text-xl text-indigo-600 font-semibold mb-4">Pengumpulan Tugas Mahasiswa</h2>
            <?php while ($tugas = $tugas_result->fetch_assoc()): ?>
                <?php
                    $tugas_id = $tugas['id'];
                    $r = $kon->query("SELECT COUNT(*) as jumlah FROM file_tugas_mhs WHERE tugas_id = $tugas_id");
                    $count = $r->fetch_assoc()['jumlah'];
                ?>
                <div class="flex justify-between items-center mb-4 p-3 border rounded-lg hover:bg-white/95 hover:border-2 transition ease-in-out duration-100">
                    <div>
                        <h3 class="font-medium text-indigo-600"><?= $tugas['nama'] ?></h3>
                        <p class="text-sm text-gray-600"><?= $count ?> mahasiswa sudah mengumpulkan</p>
                    </div>
                    <a href="rincian_tugas.php?mk_id=<?= $mk_id ?>&tugas_id=<?= $tugas_id ?>" class="bg-indigo-500 hover:bg-indigo-400 active:bg-indigo-600 transition ease-in-out duration-300 text-white px-4 py-2 rounded">Lihat Rincian</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- script tombol buka presensi -->
    <script>
        function presensi() {
            const btn = document.getElementById('presensiBtn');
            btn.innerText = "Presensi Telah Dibuka";
            btn.style.backgroundColor = "#888";
            btn.disabled = true;
        }
    </script>

    <!-- script modal buat tugas -->
    <script>
        function toggleModalTugas() {
            const modal = document.getElementById('modalTugas');
            const box = document.getElementById('modalBoxTugas');
            
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    box.classList.remove('scale-95', 'opacity-0');
                    box.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                box.classList.remove('scale-100', 'opacity-100');
                box.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }
        }

        // Menutup modal saat klik di luar box
        document.addEventListener('click', function (e) {
            const modal = document.getElementById('modalTugas');
            const box = document.getElementById('modalBoxTugas');

            if (!modal.classList.contains('hidden') && e.target === modal) {
                toggleModalTugas();
            }
        });
    </script>

    <!-- script untuk cdn icon -->
    <script>
        feather.replace();
    </script>
</body>
</html>
