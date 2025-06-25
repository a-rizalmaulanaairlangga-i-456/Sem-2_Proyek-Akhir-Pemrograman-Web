<?php
    include '../koneksi.php';

    // ambil data id file tugas, value nilai yang diinputkan dan mata kuliah
    $file_id = $_POST['file_id'];
    $nilai = $_POST['nilai'];
    $mk_id = $_POST['mk_id'];

    // Fungsi konversi nilai huruf
    function nilaiHuruf($angka) {
        if ($angka >= 85) return "A";
        if ($angka >= 75) return "B";
        if ($angka >= 65) return "C";
        if ($angka >= 55) return "D";
        return "E";
    }
    $nilai_huruf = nilaiHuruf($nilai);

    // Ambil tugas_id dari file_tugas_mhs berdasarkan file_id
    $get_tugas = mysqli_query($kon, "SELECT tugas_id, mhs_id FROM file_tugas_mhs WHERE id = $file_id");
    $data = mysqli_fetch_assoc($get_tugas);
    $tugas_id = $data['tugas_id'];
    $mhs_id = $data['mhs_id'];

    // Cek apakah sudah ada nilai untuk file tersebut
    $cek = mysqli_query($kon, "SELECT id FROM nilai WHERE file_tugas_id = $file_id");
    if (mysqli_num_rows($cek) > 0) {
        // Update nilai
        $sql = "UPDATE nilai SET nilai_angka = $nilai, nilai_huruf = '$nilai_huruf' WHERE file_tugas_id = $file_id";
    } else {
        // Insert nilai baru
        $sql = "INSERT INTO nilai (nilai_angka, nilai_huruf, file_tugas_id, tugas_id, mhs_id) 
                VALUES ($nilai, '$nilai_huruf', $file_id, $tugas_id, $mhs_id)";
    }

    mysqli_query($kon, $sql);
    header("Location: rincian_tugas.php?mk_id=$mk_id&tugas_id=$tugas_id");
    exit;
?>
