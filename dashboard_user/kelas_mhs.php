<?php
    include '../koneksi.php';
    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'mahasiswa') {
        header("Location: ../login.php");
        exit;
    }

    $mk_id = $_GET['mk_id'] ?? null;

    if (!$mk_id) {
        echo "ID mata kuliah tidak ditemukan.";
        exit;
    }

    // mengambil informasi mata kuliah
    $query = "
        SELECT mk.nama_mk, d.nama AS nama_dosen, d.nip
        FROM mata_kuliah_dosen mk
        JOIN data_dosen d ON mk.dosen_id = d.id
        WHERE mk.id = '$mk_id'
    ";

    $result_mk = mysqli_query($kon, $query);
    $kelas = mysqli_fetch_assoc($result_mk);

    // mengambil informasi tugas
    $query_tugas = "
    SELECT 
        t.id, 
        t.nama, 
        t.deskripsi, 
        d.nama AS dosen, 
        d.nip,
        ftm.filename, 
        ftm.original_filename, 
        n.nilai_angka, 
        n.nilai_huruf
    FROM tugas t
    JOIN mata_kuliah_dosen mkd ON t.mk_id = mkd.id
    JOIN data_dosen d ON mkd.dosen_id = d.id
    LEFT JOIN file_tugas_mhs ftm ON t.id = ftm.tugas_id AND ftm.mhs_id = {$_SESSION['user']['user_id']}
    LEFT JOIN nilai n ON ftm.id = n.file_tugas_id
    WHERE t.mk_id = '$mk_id'
    ";
    $result_tugas = mysqli_query($kon, $query_tugas);

    if (!$kelas) {
        echo "Mata kuliah tidak ditemukan.";
        exit;
    }
    

    $notif = $_SESSION['notif'] ?? null;
    unset($_SESSION['notif']);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Akses Kelas</title>
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

        .box { 
            border: 1px solid #ccc; 
            padding: 15px; 
            margin: 10px; 
            border-radius: 8px; 
        }

        .tugas-box {
            margin: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .tugas-box h3 {
            margin: 0 0 10px 0;
            font-size: 1.5em;
        }

        .btn {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #45a049;
        }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>
    <?php include __DIR__ . '/../navigasi.php'; ?>

    <div class="px-6 pb-6 pt-28">
        <?php if ($notif): ?>
            <div id="notif-box" class="fixed top-10 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md">
                <div class="rounded border-l-4 p-4 shadow-lg 
                    <?= $notif['status'] === 'success' 
                        ? 'bg-green-100 border-green-500 text-green-800' 
                        : 'bg-red-100 border-red-500 text-red-800' ?>">
                    <div class="flex justify-between items-start">
                        <p class="font-medium"><?= htmlspecialchars($notif['message']) ?></p>
                        <button onclick="document.getElementById('notif-box').remove()" 
                            class="ml-4 text-xl font-bold leading-none focus:outline-none">&times;</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- tombol kembali ke halaman sebelumnya -->
        <a href="mahasiswa.php" class="flex w-fit items-center text-lg text-black hover:text-blue-600 hover:underline transition-all duration-300 hover:-translate-x-1 group">
            <i data-feather="arrow-left" class="mr-1 w-4 h-4 group-hover:text-blue-600 transition-all duration-300"></i>
            <span>Kembali</span>
        </a>

        <!-- header kelas -->
        <div class="bg-white/85 rounded-xl p-6 shadow mb-6 mt-3 gap-3">
            <p class="text-3xl text-indigo-400 font-bold mb-6"><?= $kelas['nama_mk'] ?></p>
        <p class="text-black flex gap-2">
            <span class="w-40">NIP</span>: <?= $kelas['nip'] ?>
        </p>
        <p class="text-black flex gap-2">
            <span class="w-40">Nama Dosen</span>: <?= $kelas['nama_dosen'] ?>
        </p>
            <button class="px-4 py-2 bg-green-600 hover:bg-green-500 active:bg-green-700 transition ease-in-out duration-300 text-white border-none rounded cursor-pointer mt-4" id="presensiBtn" onclick="presensi()">Presensi</button>
        </div>

        <!-- Menampilkan daftar tugas mahasiswa -->
        <div class="bg-white/85 rounded-xl p-6 shadow mt-6">
            <h2 class="text-2xl text-indigo-600 font-semibold mb-5">Daftar Tugas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php while ($tugas = mysqli_fetch_assoc($result_tugas)): ?>
                <div class="flex justify-between items-center mb-4 p-4 border rounded-lg hover:bg-white/95 hover:border-2 transition ease-in-out duration-100">
                    <div class="w-full">
                        <h3 class="font-medium text-indigo-600"><?= htmlspecialchars($tugas['nama']) ?></h3>
                        <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($tugas['deskripsi']) ?></p>

                        <!-- Tugas sudah dikumpulkan -->
                        <?php if ($tugas['filename']): ?>
                            <div class="mt-3" id="view-mode-<?= $tugas['id'] ?>">
                                <p class="text-sm text-green-600">
                                    File dikumpulkan: 
                                    <strong><?= htmlspecialchars($tugas['original_filename']) ?></strong>
                                </p>

                                <!-- tugas sudah dinilai -->
                                <?php if ($tugas['nilai_angka'] !== null): ?>
                                    <p class="text-sm text-blue-700 mt-1">
                                        Tugas sudah dinilai - <?= $tugas['nilai_angka'] ?> (<?= $tugas['nilai_huruf'] ?>)
                                    </p>
                                <?php else: ?>
                                    <p class="text-sm text-yellow-600 mt-1">Tugas belum dinilai</p>
                                    <button 
                                        onclick="toggleEditMode(<?= $tugas['id'] ?>)" 
                                        class="mt-2 bg-blue-500 hover:bg-blue-400 text-white px-4 py-2 rounded transition ease-in-out duration-200"
                                    >
                                        Ubah File
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Form Ubah File -->
                            <?php if ($tugas['nilai_angka'] === null): ?>
                                <div id="edit-mode-<?= $tugas['id'] ?>" class="hidden mt-3">
                                    <form action="proses_up_file.php" method="POST" enctype="multipart/form-data" class="flex gap-2 items-center">
                                        <!-- mengirim data tersembunyi -->
                                        <input type="hidden" name="mk_id" value="<?= $mk_id ?>">
                                        <input type="hidden" name="tugas_id" value="<?= $tugas['id'] ?>">

                                        <input type="file" name="file_tugas" class="text-sm border p-1 rounded" required>
                                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-400 text-white px-4 py-2 rounded transition ease-in-out duration-200">
                                            Update
                                        </button>
                                        <button 
                                            type="button" 
                                            onclick="toggleEditMode(<?= $tugas['id'] ?>)" 
                                            class="bg-gray-500 hover:bg-gray-400 text-white px-4 py-2 rounded transition ease-in-out duration-200"
                                        >
                                            Batal
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>

                        <!-- Belum Upload -->
                        <?php else: ?>
                            <p class="text-sm text-red-600 mt-2">Tugas belum dikumpulkan</p>
                            <form action="proses_up_file.php" method="POST" enctype="multipart/form-data" class="mt-2 flex gap-2 items-center">
                                <!-- mengirim data tersembunyi -->
                                <input type="hidden" name="mk_id" value="<?= $mk_id ?>">
                                <input type="hidden" name="tugas_id" value="<?= $tugas['id'] ?>">

                                <input type="file" name="file_tugas" class="text-sm border p-1 rounded" required>
                                <button type="submit" class="bg-indigo-500 hover:bg-indigo-400 text-white px-4 py-2 rounded transition ease-in-out duration-200">
                                    Upload File
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- script tombol presensi -->
    <script>
        function presensi() {
            const btn = document.getElementById('presensiBtn');
            btn.innerText = "Telah Presensi";
            btn.style.backgroundColor = "#888";
            btn.disabled = true;
        }
    </script>

    <!-- script perubahan tombol kirim file -->
    <script>
        function toggleEditMode(tugasId) {
            const viewMode = document.getElementById(`view-mode-${tugasId}`);
            const editMode = document.getElementById(`edit-mode-${tugasId}`);
            
            viewMode.classList.toggle('hidden');
            editMode.classList.toggle('hidden');
        }
    </script>

    <!-- script untuk cdn icon -->
    <script>
        feather.replace();
    </script>
</body>
</html>
