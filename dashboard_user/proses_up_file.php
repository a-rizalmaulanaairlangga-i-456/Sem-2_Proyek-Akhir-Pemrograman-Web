<?php
    session_start();
    include '../koneksi.php';

    // Cek login dan role
    if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
        header("Location: ../halaman_utama.php");
        exit;
    }

    $id_mhs = $_SESSION['user']['user_id'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tugas_id = $_POST['tugas_id'] ?? '';
        $file_tugas = $_FILES['file_tugas'] ?? null;
        $mk_id = $_POST['mk_id'] ?? '';

        // Validasi input
        if (!$tugas_id || !$file_tugas || $file_tugas['error'] !== 0) {
            $_SESSION['notif'] = [
                'status' => 'error',
                'message' => 'Data tugas atau file tidak valid.'
            ];
            header("Location: kelas_mhs.php?mk_id=$mk_id");
            exit;
        }

        // Validasi tipe file
        $allowed_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!in_array($file_tugas['type'], $allowed_types)) {
            $_SESSION['notif'] = [
                'status' => 'error',
                'message' => 'Tipe file tidak diizinkan. Harap unggah file PDF atau DOC/DOCX.'
            ];
            header("Location: kelas_mhs.php?mk_id=$mk_id");
            exit;
        }

        // Ambil nama asli file LENGKAP (termasuk ekstensi)
        $original_filename = $file_tugas['name']; 

        // Generate nama file baru (format: mhs_id_tugas_id_nama_asli)
        $clean_name = preg_replace('/[^A-Za-z0-9_.-]/', '_', pathinfo($original_filename, PATHINFO_FILENAME));
        $ext = pathinfo($original_filename, PATHINFO_EXTENSION);
        $new_filename = $id_mhs . '_' . $tugas_id . '_' . $clean_name . '.' . $ext;
        
        $upload_dir = '../uploads/tugas_mhs/';
        
        // Buat folder jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0775, true);
        }

        $file_path = $upload_dir . $new_filename;

        // Cek apakah sudah pernah upload
        $cek_query = "SELECT id, filepath FROM file_tugas_mhs WHERE tugas_id = '$tugas_id' AND mhs_id = '$id_mhs' LIMIT 1";
        $cek_result = mysqli_query($kon, $cek_query);

        if (move_uploaded_file($file_tugas['tmp_name'], $file_path)) {
            if (mysqli_num_rows($cek_result) > 0) {
                // Update file yang sudah ada
                $existing = mysqli_fetch_assoc($cek_result);
                $old_path = $existing['filepath'];

                // Hapus file lama jika ada
                if (file_exists($old_path)) {
                    unlink($old_path);
                }

                $update_query = "
                    UPDATE file_tugas_mhs 
                    SET 
                        filename = '$new_filename',
                        original_filename = '$original_filename',
                        filepath = '$file_path',
                        uploaded_at = NOW()
                    WHERE id = {$existing['id']}
                ";
                $result = mysqli_query($kon, $update_query);
            } else {
                // Insert baru
                $insert_query = "
                    INSERT INTO file_tugas_mhs 
                    (tugas_id, mhs_id, filename, original_filename, filepath, uploaded_at)
                    VALUES 
                    ('$tugas_id', '$id_mhs', '$new_filename', '$original_filename', '$file_path', NOW())
                ";

                $result = mysqli_query($kon, $insert_query);
            }

            if ($result) {
                $_SESSION['notif'] = [
                    'status' => 'success',
                    'message' => 'File berhasil diunggah.'
                ];
            } else {
                // Hapus file yang sudah diupload jika query gagal
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                $_SESSION['notif'] = [
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data ke database.'
                ];
            }
        } else {
            $_SESSION['notif'] = [
                'status' => 'error',
                'message' => 'Gagal mengunggah file.'
            ];
        }

        header("Location: kelas_mhs.php?mk_id=$mk_id");
        exit;
    }
?>