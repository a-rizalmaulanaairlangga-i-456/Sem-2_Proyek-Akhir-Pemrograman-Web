<?php
    session_start();
    include '../koneksi.php';

    // cek akses role
    $allowed_roles = ['admin', 'dosen', 'mahasiswa'];
    $current_role = isset($_GET['role']) && in_array($_GET['role'], $allowed_roles) ? $_GET['role'] : 'mahasiswa';

    // Cek login dan role
    if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
        header("Location: ../halaman_utama.php");
        exit;
    }

    // dapatkan identifier user
    $identifier_field = ($current_role == 'mahasiswa') ? 'nrp' : 'nip';
    $identifier = isset($_GET[$identifier_field]) ? $kon->real_escape_string($_GET[$identifier_field]) : null;

    if (!$identifier) {
        echo ucfirst($identifier_field) . " tidak ditentukan.";
        exit();
    }

    // dapatkan nama tabel berdasarkan role
    $table_name = 'data_' . ($current_role == 'admin' ? 'admin' : ($current_role == 'dosen' ? 'dosen' : 'mhs'));

    // dapatkan semua data sebelumnya
    $sql = "SELECT * FROM $table_name WHERE $identifier_field='$identifier'";
    $result = $kon->query($sql);

    if ($result->num_rows != 1) {
        echo "Data tidak ditemukan.";
        exit();
    }

    $data = $result->fetch_assoc();

    // proses submit form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // field untuk semua role
        $old_identifier = $kon->real_escape_string($_POST['old_identifier']);
        $identifier_new = $kon->real_escape_string($_POST[$identifier_field]);
        $nama = $kon->real_escape_string($_POST['nama']);
        $jenis_kelamin = $kon->real_escape_string($_POST['jenis_kelamin']);
        $email = $kon->real_escape_string($_POST['email']);
        $alamat = $kon->real_escape_string($_POST['alamat']);
        $no_hp = $kon->real_escape_string($_POST['no_hp']);

        // cek jika identifier yang diinputkan baru
        if ($identifier_new != $old_identifier) {
            $cekQuery = "SELECT * FROM $table_name WHERE $identifier_field = '$identifier_new'";
            $cekResult = $kon->query($cekQuery);
            // cek jika identifier sudah ada
            if ($cekResult->num_rows > 0) {
                $errorMsg = ucfirst($identifier_field) . " $identifier_new sudah terdaftar!";
            }
        }

        // field role spesifik
        if ($current_role == 'mahasiswa') {
            $jurusan = $kon->real_escape_string($_POST['jurusan']);
            $asal_SMA = $kon->real_escape_string($_POST['asal_SMA'] ?? '');
            $mata_kuliah_favorit = $kon->real_escape_string($_POST['mata_kuliah_favorit'] ?? '');
            $hobi = $kon->real_escape_string($_POST['hobi'] ?? '');
            $pekerjaan_impian = $kon->real_escape_string($_POST['pekerjaan_impian'] ?? '');
        }

        // Update data jika tidak ada error
        if (!isset($errorMsg)) {
            if ($current_role == 'mahasiswa') {
                $sql_update = "UPDATE $table_name SET 
                    $identifier_field='$identifier_new',
                    nama='$nama',
                    jenis_kelamin='$jenis_kelamin',
                    jurusan='$jurusan',
                    email_student='$email',
                    alamat='$alamat',
                    no_hp='$no_hp',
                    asal_SMA='$asal_SMA',
                    mata_kuliah_favorit='$mata_kuliah_favorit',
                    hobi='$hobi',
                    pekerjaan_impian='$pekerjaan_impian'
                    WHERE $identifier_field='$old_identifier'";
            } else {
                $email_field = ($current_role == 'admin') ? 'email' : 'email_lecturer';
                $sql_update = "UPDATE $table_name SET 
                    $identifier_field='$identifier_new',
                    nama='$nama',
                    jenis_kelamin='$jenis_kelamin',
                    $email_field='$email',
                    alamat='$alamat',
                    no_hp='$no_hp'
                    WHERE $identifier_field='$old_identifier'";
            }

            if ($kon->query($sql_update)) {
                header("Location: ../halaman_utama.php");
                exit;
            } else {
                $errorMsg = "Error: " . $kon->error;
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Data <?= ucfirst($current_role) ?></title>
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
            max-width: 700px;
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
        <!-- judul form -->
        <h2>Edit Data <?= ucfirst($current_role) ?></h2>
        
        <!-- peringatan error -->
        <?php if (isset($errorMsg)): ?>
            <div class="error-message"><?= $errorMsg ?></div>
        <?php endif; ?>

        <!-- form semua role -->
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="old_identifier" value="<?= htmlspecialchars($data[$identifier_field]) ?>">
            
            <div class="form-group">
                <label for="<?= $identifier_field ?>"><?= strtoupper($identifier_field) ?>:</label>
                <input type="text" name="<?= $identifier_field ?>" id="<?= $identifier_field ?>" 
                       value="<?= htmlspecialchars($data[$identifier_field]) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin:</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" <?= ($data['jenis_kelamin'] == "L") ? "selected" : "" ?>>Laki-laki</option>
                    <option value="P" <?= ($data['jenis_kelamin'] == "P") ? "selected" : "" ?>>Perempuan</option>
                </select>
            </div>
            
            <!-- form lanjutan jika role mahasiswa -->
            <?php if ($current_role == 'mahasiswa'): ?>
                <!-- Mahasiswa specific fields -->
                <div class="form-group">
                    <label for="jurusan">Jurusan:</label>
                    <select id="jurusan" name="jurusan" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php
                        $jurusan_options = [
                            "D3 Teknik Elektronika", 
                            "D3 Teknik Telekomunikasi", 
                            "D3 Teknik Elektro Industri", 
                            "D3 Teknik Informatika", 
                            "D3 Teknologi Multimedia Broadcasting", 
                            "D3 Teknik Informatika Kampus Lamongan", 
                            "D3 Teknologi Multimedia Broadcasting Kampus Lamongan", 
                            "D3 Teknik Informatika Kampus Sumenep", 
                            "D3 Teknologi Multimedia Broadcasting Kampus Sumenep", 
                            "D3 PJJ Teknik Informatika", 
                            "D4 Teknik Elektronika", 
                            "D4 Teknik Telekomunikasi", 
                            "D4 Teknik Elektro Industri", 
                            "D4 Teknik Informatika", 
                            "D4 Teknik Mekatronika", 
                            "D4 Teknik Komputer", 
                            "D4 Sistem Pembangkitan Energi", 
                            "D4 Teknologi Game", 
                            "D4 Teknologi Rekayasa Internet", 
                            "D4 Teknologi Rekayasa Multimedia", 
                            "D4 Sains Data Terapan", 
                            "D4 PJJ Teknik Telekomunikasi", 
                            "S2 Teknik Elektro", 
                            "S2 Teknik Informatika dan Komputer"
                        ];
                        
                        foreach ($jurusan_options as $option) {
                            $selected = ($data['jurusan'] == $option) ? "selected" : "";
                            echo "<option value=\"$option\" $selected>$option</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Student:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email_student']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="asal_SMA">Asal SMA:</label>
                    <input type="text" id="asal_SMA" name="asal_SMA" value="<?= htmlspecialchars($data['asal_SMA'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="mata_kuliah_favorit">Mata Kuliah Favorit:</label>
                    <select id="mata_kuliah_favorit" name="mata_kuliah_favorit">
                        <option value="">-- Pilih --</option>
                        <?php
                        $mk_options = [
                            "Praktikum Algoritma dan Struktur Data", 
                            "Algoritma dan Struktur Data",
                            "Praktikum Sistem Operasi", 
                            "Sistem Operasi", 
                            "Praktikum Basis Data", 
                            "Basis Data",
                            "Praktikum Pemrograman Web", 
                            "Pemrograman Web", 
                            "Matematika 2", 
                            "Kewarganegaraan"
                        ];
                        
                        foreach ($mk_options as $option) {
                            $selected = ($data['mata_kuliah_favorit'] == $option) ? "selected" : "";
                            echo "<option value=\"$option\" $selected>$option</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="hobi">Hobi:</label>
                    <input type="text" id="hobi" name="hobi" value="<?= htmlspecialchars($data['hobi'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="pekerjaan_impian">Pekerjaan Impian:</label>
                    <input type="text" id="pekerjaan_impian" name="pekerjaan_impian" value="<?= htmlspecialchars($data['pekerjaan_impian'] ?? '') ?>">
                </div>
                
            <?php else: ?>
                <!-- form lanjutan jika role admin atau dosen -->
                <div class="form-group">
                    <label for="email">Email <?= ucfirst($current_role) ?>:</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($current_role == 'admin' ? $data['email'] : $data['email_lecturer']) ?>" required>
                </div>
            <?php endif; ?>
            
            <!-- Form lanjutan untuk semua role -->
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" rows="3"><?= htmlspecialchars($data['alamat']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="no_hp">No HP:</label>
                <input type="text" id="no_hp" name="no_hp" value="<?= htmlspecialchars($data['no_hp']) ?>">
            </div>
            
            <div class="form-actions">
                <a href="../halaman_utama.php" class="btn btn-back">Kembali</a>
                <button type="submit" class="btn btn-submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</body>
</html>