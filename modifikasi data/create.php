<?php
    session_start();
    include '../koneksi.php';

    // Cek login dan role
    if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
        header("Location: ../login.php");
        exit;
    }

    $allowed_roles = ['admin', 'dosen', 'mahasiswa'];
    $current_role = isset($_GET['role']) && in_array($_GET['role'], $allowed_roles) ? $_GET['role'] : 'mahasiswa';

    if (!in_array($current_role, $allowed_roles)) {
        header("Location: ../halaman_utama.php");
        exit;
    }

    // Proses Form Submit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // jika membuat data untuk role admin atau dosen
        if ($current_role == 'admin' || $current_role == 'dosen') {
            // Proses data admin/dosen
            $nip = $kon->real_escape_string($_POST['nip'] ?? '');
            $nama = $kon->real_escape_string($_POST['nama']);
            $jenis_kelamin = $kon->real_escape_string($_POST['jenis_kelamin']);
            $email = $kon->real_escape_string($_POST['email']);
            $alamat = $kon->real_escape_string($_POST['alamat']);
            $no_hp = $kon->real_escape_string($_POST['no_hp']);

            // Tentukan tabel target dan kolom email berdasarkan role
            $table = ($current_role == 'admin') ? 'data_admin' : 'data_dosen';
            $email_field = ($current_role == 'admin') ? 'email' : 'email_lecturer';

            // Validasi NIP unik
            $cekQuery = "SELECT * FROM $table WHERE nip = '$nip'";
            $cekResult = $kon->query($cekQuery);
            
            if ($cekResult->num_rows > 0) {
                $errorMsg = "NIP $nip sudah terdaftar!";
            }

            if (!isset($errorMsg)) {
                $sql = "INSERT INTO $table 
                        (nip, nama, jenis_kelamin, $email_field, alamat, no_hp) 
                        VALUES 
                        ('$nip', '$nama', '$jenis_kelamin', '$email', '$alamat', '$no_hp')";
                
                if ($kon->query($sql)) {
                    header("Location: ../halaman_utama.php");
                    exit;
                } else {
                    $errorMsg = "Error: " . $kon->error;
                }
            }
        } 

        // jika membuat data untuk role mahasiswa
        elseif ($current_role == 'mahasiswa') {
            // Proses data mahasiswa
            $nrp = $kon->real_escape_string($_POST['nrp']);
            $nama = $kon->real_escape_string($_POST['nama']);
            $jenis_kelamin = $kon->real_escape_string($_POST['jenis_kelamin']);
            $jurusan = $kon->real_escape_string($_POST['jurusan']);
            $email_student = $kon->real_escape_string($_POST['email_student']);
            $alamat = $kon->real_escape_string($_POST['alamat']);
            $no_hp = $kon->real_escape_string($_POST['no_hp']);
            $asal_SMA = $kon->real_escape_string($_POST['asal_SMA'] ?? '');
            $hobi = $kon->real_escape_string($_POST['hobi'] ?? '');
            $pekerjaan_impian = $kon->real_escape_string($_POST['pekerjaan_impian'] ?? '');
            $mata_kuliah_favorit = $kon->real_escape_string($_POST['mata_kuliah_favorit'] ?? '');

            // Validasi NRP unik
            $cekQuery = "SELECT * FROM data_mhs WHERE nrp = '$nrp'";
            $cekResult = $kon->query($cekQuery);
            
            if ($cekResult->num_rows > 0) {
                $errorMsg = "NRP $nrp sudah terdaftar!";
            }

            if (!isset($errorMsg)) {
                $sql = "INSERT INTO data_mhs 
                        (nrp, nama, jenis_kelamin, jurusan, email_student, alamat, no_hp, asal_SMA, hobi, pekerjaan_impian, mata_kuliah_favorit) 
                        VALUES 
                        ('$nrp', '$nama', '$jenis_kelamin', '$jurusan', '$email_student', '$alamat', '$no_hp', '$asal_SMA', '$hobi', '$pekerjaan_impian', '$mata_kuliah_favorit')";
                
                if ($kon->query($sql)) {
                    header("Location: ../halaman_utama.php");
                    exit;
                } else {
                    $errorMsg = "Error: " . $kon->error;
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Input Data <?= ucfirst($current_role) ?></title>
    <style>
        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://4kwallpapers.com/images/walls/thumbs_3t/21290.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(20px);
            z-index: -1;
        }

        .form-container {
            max-width: 570px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.46);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .error-message {
            color: #e74c3c;
            background-color: #fadbd8;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #34495e;
            font-weight: 600;
        }

        input, select, textarea {
            background-color: #ecf0f1;
            transition: all 0.3s;
        }

        input[type="text"],
        input[type="email"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #bdc3c7;
            border-radius: 6px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }


        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3498db;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-back {
            background-color: #e74c3c;
            color: white;
            border: none;
        }

        .btn-back:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .btn-submit {
            background-color: #3498db;
            color: white;
            border: none;
        }

        .btn-submit:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(41, 128, 185, 0.3);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <!-- judul pembuatan data sesuai role -->
        <h2>Form Input Data <?= ucfirst($current_role) ?></h2>
        
        <!-- peringatan error -->
        <?php if (isset($errorMsg)): ?>
            <div class="error-message"><?= $errorMsg ?></div>
        <?php endif; ?>

        <!-- form buat data baru -->
        <form method="POST" action="">
            <!-- form untuk role mahasiswa -->
            <?php if ($current_role == 'mahasiswa'): ?>
                <div class="form-group">
                    <label for="nrp">NRP:</label>
                    <input type="text" id="nrp" name="nrp" required>
                </div>
                
                <div class="form-group">
                    <label for="nama">Nama Lengkap:</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin:</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="jurusan">Jurusan:</label>
                    <select id="jurusan" name="jurusan" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <option value="D3 Teknik Elektronika">D3 Teknik Elektronika</option>
                        <option value="D3 Teknik Telekomunikasi">D3 Teknik Telekomunikasi</option>
                        <option value="D3 Teknik Elektro Industri">D3 Teknik Elektro Industri</option>
                        <option value="D3 Teknik Informatika">D3 Teknik Informatika</option>
                        <option value="D3 Teknologi Multimedia Broadcasting">D3 Teknologi Multimedia Broadcasting</option>
                        <option value="D3 Teknik Informatika Kampus Lamongan">D3 Teknik Informatika Kampus Lamongan</option>
                        <option value="D3 Teknologi Multimedia Broadcasting Kampus Lamongan">D3 Teknologi Multimedia Broadcasting Kampus Lamongan</option>
                        <option value="D3 Teknik Informatika Kampus Sumenep">D3 Teknik Informatika Kampus Sumenep</option>
                        <option value="D3 Teknologi Multimedia Broadcasting Kampus Sumenep">D3 Teknologi Multimedia Broadcasting Kampus Sumenep</option>
                        <option value="D3 PJJ Teknik Informatika">D3 PJJ Teknik Informatika</option>
                        <option value="D4 Teknik Elektronika">D4 Teknik Elektronika</option>
                        <option value="D4 Teknik Telekomunikasi">D4 Teknik Telekomunikasi</option>
                        <option value="D4 Teknik Elektro Industri">D4 Teknik Elektro Industri</option>
                        <option value="D4 Teknik Informatika">D4 Teknik Informatika</option>
                        <option value="D4 Teknik Mekatronika">D4 Teknik Mekatronika</option>
                        <option value="D4 Teknik Komputer">D4 Teknik Komputer</option>
                        <option value="D4 Sistem Pembangkitan Energi">D4 Sistem Pembangkitan Energi</option>
                        <option value="D4 Teknologi Game">D4 Teknologi Game</option>
                        <option value="D4 Teknologi Rekayasa Internet">D4 Teknologi Rekayasa Internet</option>
                        <option value="D4 Teknologi Rekayasa Multimedia">D4 Teknologi Rekayasa Multimedia</option>
                        <option value="D4 Sains Data Terapan">D4 Sains Data Terapan</option>
                        <option value="D4 PJJ Teknik Telekomunikasi">D4 PJJ Teknik Telekomunikasi</option>
                        <option value="S2 Teknik Elektro">S2 Teknik Elektro</option>
                        <option value="S2 Teknik Informatika dan Komputer">S2 Teknik Informatika dan Komputer</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="email_student">Email Student:</label>
                    <input type="email" id="email_student" name="email_student" required>
                </div>
                
                <div class="form-group">
                    <label for="alamat">Alamat:</label>
                    <textarea id="alamat" name="alamat" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="no_hp">No HP:</label>
                    <input type="text" id="no_hp" name="no_hp">
                </div>
                
                <div class="form-group">
                    <label for="asal_SMA">Asal SMA:</label>
                    <input type="text" id="asal_SMA" name="asal_SMA">
                </div>
                
                <div class="form-group">
                    <label for="mata_kuliah_favorit">Mata Kuliah Favorit:</label>
                    <select id="mata_kuliah_favorit" name="mata_kuliah_favorit">
                        <option value="">-- Pilih --</option>
                        <option value="Praktikum Algoritma dan Struktur Data">Praktikum Algoritma dan Struktur Data</option>
                        <option value="Algoritma dan Struktur Data">Algoritma dan Struktur Data</option>
                        <option value="Praktikum Sistem Operasi">Praktikum Sistem Operasi</option>
                        <option value="Sistem Operasi">Sistem Operasi</option>
                        <option value="Praktikum Basis Data">Praktikum Basis Data</option>
                        <option value="Basis Data">Basis Data</option>
                        <option value="Praktikum Pemrograman Web">Praktikum Pemrograman Web</option>
                        <option value="Pemrograman Web">Pemrograman Web</option>
                        <option value="Matematika 2">Matematika 2</option>
                        <option value="Kewarganegaraan">Kewarganegaraan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="hobi">Hobi:</label>
                    <input type="text" id="hobi" name="hobi">
                </div>
                
                <div class="form-group">
                    <label for="pekerjaan_impian">Pekerjaan Impian:</label>
                    <input type="text" id="pekerjaan_impian" name="pekerjaan_impian">
                </div>
                
            <!-- Form Admin/Dosen -->
            <?php else: ?>
                <div class="form-group">
                    <label for="nip">NIP:</label>
                    <input type="text" id="nip" name="nip" required>
                </div>
                
                <div class="form-group">
                    <label for="nama">Nama Lengkap:</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin:</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="email">Email <?= ($current_role == 'admin') ? 'Admin' : 'Dosen' ?>:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="alamat">Alamat:</label>
                    <textarea id="alamat" name="alamat" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="no_hp">No HP:</label>
                    <input type="text" id="no_hp" name="no_hp">
                </div>
            <?php endif; ?>
            
            <div class="form-actions">
                <a href="../halaman_utama.php" class="btn btn-back">Kembali</a>
                <button type="submit" class="btn btn-submit">Simpan Data</button>
            </div>
        </form>
    </div>
</body>
</html>