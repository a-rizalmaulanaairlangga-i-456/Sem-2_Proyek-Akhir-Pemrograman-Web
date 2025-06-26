<?php
    session_start();
    include __DIR__ . '/../koneksi.php';

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
    $tugas_id = $_GET['tugas_id'];

    // Ambil info tugas
    $q_tugas = "SELECT t.nama, mk.nama_mk 
                FROM tugas t 
                JOIN mata_kuliah_dosen mk ON t.mk_id = mk.id 
                WHERE t.id = $tugas_id";
    $tugas = $kon->query($q_tugas)->fetch_assoc();

    // Ambil data pengumpulan
    $q_data = "
        SELECT 
            ftm.id AS file_tugas_id,
            ftm.filename,
            mhs.nrp,
            mhs.nama,
            n.nilai_angka,
            n.nilai_huruf
        FROM file_tugas_mhs ftm
        JOIN data_mhs mhs ON ftm.mhs_id = mhs.id
        LEFT JOIN nilai n ON ftm.id = n.file_tugas_id
        WHERE ftm.tugas_id = '$tugas_id' 
    ";
    $data_file = mysqli_query($kon, $q_data);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rincian Tugas</title>
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
<body class="bg-gray-100">
    <?php include __DIR__ . '/../navigasi.php'; ?>
    
    <div class="px-6 pb-6 pt-28">
        <!-- tombol kembali ke halaman sebelumnya -->
        <a href="kelas_dosen.php?mk_id=<?= $mk_id ?>" class="flex w-fit items-center text-lg text-black hover:text-blue-600 hover:underline transition-all duration-300 hover:-translate-x-1 group">
            <i data-feather="arrow-left" class="mr-1 w-4 h-4 group-hover:text-blue-600 transition-all duration-300"></i>
            <span>Kembali</span>
        </a>

        <!-- header halaman -->
        <div class="bg-white px-6 pt-4 pb-8 rounded-xl shadow mb-6 mt-3">
            <h1 class="text-2xl font-bold mb-2">Rincian Tugas</h1>
            <p class="text-lg"><?= $tugas['nama_mk'] ?> - <?= $tugas['nama'] ?></p>
        </div>
    
        <!-- tabel rincian pengumpulan tugas mahasiswa -->
        <div class="bg-white p-6 rounded-xl shadow">
            <table class="w-full table-auto border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">NRP</th>
                        <th class="border px-4 py-2">Nama</th>
                        <th class="border px-4 py-2">File</th>
                        <th class="border px-4 py-2">Nilai</th>
                        <th class="border px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($data_file)): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?= htmlspecialchars($row['nrp']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                            <td class="px-4 py-2">
                                <?php 
                                    $upload_dir = '../uploads/tugas_mhs/';
                                    $filename = basename($row['filename']);
                                ?>
                                <a href="<?= $upload_dir . $filename ?>" class="text-blue-500 hover:underline" target="_blank">
                                    <?= htmlspecialchars($filename) ?>
                                </a>
                            </td>
                            <td class="px-4 py-2"><?= $row['nilai_angka'] . ' - ' . $row['nilai_huruf'] ?? '-' ?></td>
                            <td class="px-4 py-2 text-center">
                                <?php if (is_null($row['nilai_angka'])): ?>
                                    <button 
                                        class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition" 
                                        onclick="openModal(<?= $row['file_tugas_id'] ?>, null)">
                                        Beri Nilai
                                    </button>
                                <?php else: ?>
                                    <button 
                                        class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition" 
                                        onclick="openModal(<?= $row['file_tugas_id'] ?>, <?= $row['nilai_angka'] ?>)">
                                        Edit Nilai
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    
        <!-- Modal Input Nilai -->
        <div id="nilaiModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow w-1/3">
                <h2 id="judulModal" class="text-xl font-bold mb-4">Beri/Edit Nilai</h2>
                <form action="proses_nilai.php" method="post">
                    <!-- data tersembunyi -->
                    <input type="hidden" name="mk_id" value="<?= $mk_id ?>">
                    <input type="hidden" name="file_id" id="file_id">

                    <label class="block mb-2">Nilai Angka</label>
                    <input type="number" name="nilai" id="nilai" class="w-full p-2 border rounded mb-4" min="0" max="100" required>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('nilaiModal').classList.add('hidden')" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(fileId, nilaiAngka) {
            const judulModal = document.getElementById('judulModal');
            
            // Ubah judul tergantung apakah nilai sudah ada atau belum
            if (nilaiAngka === null || nilaiAngka === undefined) {
                judulModal.textContent = 'Beri Nilai';
                document.getElementById('nilai').value = '';
            } else {
                judulModal.textContent = 'Edit Nilai';
                document.getElementById('nilai').value = nilaiAngka;
            }

            document.getElementById('file_id').value = fileId;
            document.getElementById('nilaiModal').classList.remove('hidden');
        }
    </script>

    <!-- bebaskan memory -->
    <?php
        mysqli_free_result($data_file);
    ?>
    <!-- script untuk cdn icon -->
    <script>
        feather.replace();
    </script>
</body>
</html>
