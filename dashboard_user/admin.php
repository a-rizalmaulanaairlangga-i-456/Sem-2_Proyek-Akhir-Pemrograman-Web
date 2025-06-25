<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include __DIR__ . '/../koneksi.php';

    // Ambil data mahasiswa
    $queryMhs = "SELECT * FROM data_mhs";
    $resultMhs = $kon->query($queryMhs);
    // Statistik mahasiswa
    $totalMhs = $kon->query("SELECT COUNT(*) AS total FROM data_mhs")->fetch_assoc();
    $maleMhs = $kon->query("SELECT COUNT(*) AS male FROM data_mhs WHERE jenis_kelamin = 'L'")->fetch_assoc();
    $femaleMhs = $kon->query("SELECT COUNT(*) AS female FROM data_mhs WHERE jenis_kelamin = 'P'")->fetch_assoc();

    // Ambil data dosen
    $queryDosen = "SELECT * FROM data_dosen";
    $resultDosen = $kon->query($queryDosen);
    // Statistik dosen
    $totalDosen = $kon->query("SELECT COUNT(*) AS total FROM data_dosen")->fetch_assoc();
    $maleDosen = $kon->query("SELECT COUNT(*) AS male FROM data_dosen WHERE jenis_kelamin = 'L'")->fetch_assoc();
    $femaleDosen = $kon->query("SELECT COUNT(*) AS female FROM data_dosen WHERE jenis_kelamin = 'P'")->fetch_assoc();

    // Ambil data admin
    $queryAdmin = "SELECT * FROM data_admin";
    $resultAdmin = $kon->query($queryAdmin);
    // Statistik admin
    $totalAdmin = $kon->query("SELECT COUNT(*) AS total FROM data_admin")->fetch_assoc();
    $maleAdmin = $kon->query("SELECT COUNT(*) AS male FROM data_admin WHERE jenis_kelamin = 'L'")->fetch_assoc();
    $femaleAdmin = $kon->query("SELECT COUNT(*) AS female FROM data_admin WHERE jenis_kelamin = 'P'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Admin</title>
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

        /* style tabel */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: Arial, sans-serif;
            font-size: 14px;
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid rgba(203, 213, 225, 1);
        }

        th {
            background-color: rgba(37, 99, 235, 0.9); /* Tailwind bg-blue-600/90 */
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        td {
            text-align: left;
            background-color: rgba(255, 255, 255, 0.8); /* putih 80% */
        }

        tr:nth-child(even) td {
            background-color: rgba(245, 245, 245, 0.9); /* putih lebih tua */
        }

        tr:hover td {
            background-color:rgb(199, 227, 253);
        }

        /* Optional: smooth border rounding on entire table */
        table tr:first-child th:first-child {
            border-top-left-radius: 12px;
        }

        table tr:first-child th:last-child {
            border-top-right-radius: 12px;
        }

        table tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }

        table tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <?php include __DIR__ . '/../navigasi.php'; ?>

    <div class="px-5 pt-10 pb-20 mt-12">
        <!-- data mahasiswa -->
        <div class="text-center mt-5 mb-2">
            <span class="text-3xl font-bold pt-2 pb-2 px-3 bg-white/40 rounded-t-xl">Data Mahasiswa</span>
        </div>
        <!-- tabel data mahasiswa -->
        <table>
            <tr>
                <th>No</th>
                <th>NRP</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Jurusan</th>
                <th>Email Student</th>
                <th>Alamat</th>
                <th>No HP</th>
                <th>Asal SMA</th>
                <th>Mata Kuliah Favorit</th>
                <th>Hobi</th>
                <th>Pekerjaan Impian</th>
                <th>Aksi</th> 
            </tr>
            <?php
            $no = 1;
            while ($row = $resultMhs->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$no}</td>
                    <td>{$row['nrp']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['jenis_kelamin']}</td>
                    <td>{$row['jurusan']}</td>
                    <td>{$row['email_student']}</td>
                    <td>{$row['alamat']}</td>
                    <td>{$row['no_hp']}</td>
                    <td>{$row['asal_SMA']}</td>
                    <td>{$row['mata_kuliah_favorit']}</td>
                    <td>{$row['hobi']}</td>
                    <td>{$row['pekerjaan_impian']}</td>";
                echo "<td class='p-2'>
                        <div class='flex flex-col gap-2 w-full'>
                            <a href='./modifikasi data/edit.php?role=mahasiswa&nrp={$row['nrp']}' class='bg-blue-500 hover:bg-blue-400 active:bg-blue-600 text-white px-3 py-2 rounded text-center w-full'>Edit</a>
                            <a href='./modifikasi data/delete.php?role=mahasiswa&id={$row['nrp']}' onclick='return confirm(\"Yakin ingin menghapus?\")' class='bg-red-500 hover:bg-red-400 active:bg-red-600 text-white px-3 py-2 rounded text-center w-full'>Hapus</a>
                        </div>
                    </td>";
                echo "</tr>";
                $no++;
            }
            ?>
        </table>
        <!-- statistik mahasiswa -->
        <div class="flex justify-between bg-white/35 mt-2 p-5 rounded-lg">
            <div>Total Mahasiswa: <?= $totalMhs['total'] ?> | Laki-laki: <?= $maleMhs['male'] ?> | Perempuan: <?= $femaleMhs['female'] ?></div>
            <a href="modifikasi data/create.php?role=mahasiswa" class="bg-blue-500 hover:bg-blue-600 active:bg-blue-400 transition ease-in-out duration-100 text-white font-medium py-2 px-4 rounded-lg">Tambah Mahasiswa</a>
        </div>
    
        <!-- data dosen -->
        <div class="text-center mt-20 mb-2">
            <span class="text-3xl font-bold pt-2 pb-2 px-3 bg-white/40 rounded-t-xl">Data Dosen</span>
        </div>
        <!-- tabel data dosen -->
        <table>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>No HP</th>
                <th>Aksi</th> 
            </tr>
            <?php
            $no = 1;
            while ($row = $resultDosen->fetch_assoc()) {
                echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['nip']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['jenis_kelamin']}</td>
                    <td>{$row['email_lecturer']}</td>
                    <td>{$row['alamat']}</td>
                    <td>{$row['no_hp']}</td>";
                echo "<td class='p-2'>
                        <div class='flex flex-col gap-2 w-full'>
                            <a href='./modifikasi data/edit.php?role=dosen&nip={$row['nip']}' class='bg-blue-500 hover:bg-blue-400 active:bg-blue-600 text-white px-3 py-2 rounded text-center w-full'>Edit</a>
                            <a href='./modifikasi data/delete.php?role=dosen&id={$row['nip']}' onclick='return confirm(\"Yakin ingin menghapus?\")' class='bg-red-500 hover:bg-red-400 active:bg-red-600 text-white px-3 py-2 rounded text-center w-full'>Hapus</a>
                        </div>
                    </td>";
                echo "</tr>";
                $no++;
            }
            ?>
        </table>
        <!-- statistik dosen -->
        <div class="flex justify-between bg-white/35 mt-2 p-5 rounded-lg">
            <div>Total Dosen: <?= $totalDosen['total'] ?> | Laki-laki: <?= $maleDosen['male'] ?> | Perempuan: <?= $femaleDosen['female'] ?></div>
            <a href="modifikasi data/create.php?role=dosen" class="bg-blue-500 hover:bg-blue-600 active:bg-blue-400 transition ease-in-out duration-100 text-white font-medium py-2 px-4 rounded-lg">Tambah Dosen</a>
        </div>

        <!-- data admin -->
        <div class="text-center mt-20 mb-2">
            <span class="text-3xl font-bold pt-2 pb-2 px-3 bg-white/40 rounded-t-xl">Data Admin</span>
        </div>
        <!-- tabel data admin -->
        <table>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>No HP</th>
                <th>Aksi</th> 
            </tr>
            <?php
            $no = 1;
            while ($row = $resultAdmin->fetch_assoc()) {
                echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['nip']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['jenis_kelamin']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['alamat']}</td>
                    <td>{$row['no_hp']}</td>";
                    echo "<td class='p-2'>
                        <div class='flex flex-col gap-2 w-full'>
                            <a href='./modifikasi data/edit.php?role=admin&nip={$row['nip']}' class='bg-blue-500 hover:bg-blue-400 active:bg-blue-600 text-white px-3 py-2 rounded text-center w-full'>Edit</a>
                            <a href='./modifikasi data/delete.php?role=admin&id={$row['nip']}' onclick='return confirm(\"Yakin ingin menghapus?\")' class='bg-red-500 hover:bg-red-400 active:bg-red-600 text-white px-3 py-2 rounded text-center w-full'>Hapus</a>
                        </div>
                    </td>";
                echo "</tr>";
                $no++;
            }
            ?>
        </table>
        <!-- statistik admin -->
        <div class="flex justify-between bg-white/35 mt-2 p-5 rounded-lg">
            <div>Total Admin: <?= $totalAdmin['total'] ?> | Laki-laki: <?= $maleAdmin['male'] ?> | Perempuan: <?= $femaleAdmin['female'] ?></div>
            <a href="modifikasi data/create.php?role=admin" class="bg-blue-500 hover:bg-blue-600 active:bg-blue-400 transition ease-in-out duration-100 text-white font-medium py-2 px-4 rounded-lg">Tambah Admin</a>
        </div>
    </div>
</body>
</html>

