<?php
    require_once "../koneksi.php";
    $errorMsg = "";

    // Ambil role dari URL (gunakan hanya satu kali)
    $role = $_GET['role'] ?? 'mahasiswa';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $kon->real_escape_string($_POST['email']); // asumsikan input "user" adalah email
        $password = md5($_POST['password']); // masih pakai md5 sesuai kode awal

        // Cek apakah akun dengan email tersebut dan role sudah ada
        $checkQuery = "SELECT * FROM akun WHERE email = '$email' AND role = '$role'";
        $checkResult = $kon->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            $errorMsg = "Akun dengan email tersebut sudah terdaftar.";
        } else {
            // Cek apakah email ada di data mahasiswa atau dosen
            if ($role === "mahasiswa") {
                $cekEmailQuery = "SELECT id FROM data_mhs WHERE email_student = '$email'";
            } elseif ($role === "dosen") {
                $cekEmailQuery = "SELECT id FROM data_dosen WHERE email_lecturer = '$email'";
            } else {
                // Untuk admin, bisa diizinkan langsung mendaftar (opsional)
                $cekEmailQuery = null;
            }

            $user_id = null;

            if ($cekEmailQuery) {
                $emailResult = $kon->query($cekEmailQuery);
                if ($EmailResult->num_rows > 0) {
                    $row = $emailResult->fetch_assoc();
                    $user_id = $row['id'];
                } else {
                    $errorMsg = "Email belum terdaftar dalam data " . $role . ".";
                }
            }

            // Jika tidak ada error, lakukan insert akun
            if ($errorMsg === "") {
                $query = "INSERT INTO akun (email, password, role, user_id) VALUES ('$email', '$password', '$role', " . ($user_id ?? "NULL") . ")";
                if ($kon->query($query)) {
                    header("Location: login.php?role=$role");
                    exit;
                } else {
                    $errorMsg = "Gagal mendaftar. Silakan coba lagi.";
                }
            }
        }
    }
?>

<!-- Bagian HTML sama seperti form Anda sebelumnya -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        /* Untuk mensetting background agar memiliki efek blur */
        html, body {
            height: 100%;
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
            background-repeat: no-repeat;
            filter: blur(20px);
            z-index: -1;
        }

        form {
            max-width: 600px;
            margin: auto;
        }

        label {
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
            font-size: 1.125rem;
            font-weight: 600;
        }

        input, select, textarea {
            background-color:rgb(228, 241, 255);
        }

        input[type="text"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color:rgb(54, 149, 251);
            color: #fff;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease-in-out;
        }

        input[type="submit"]:hover {
            background-color:rgb(9, 127, 252);
            transform: scale(1.03);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

    </style>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <div class="w-1/2 mx-auto mt-34 p-6 backdrop-blur-sm bg-white/35 rounded-2xl shadow-md place-items-center">
        <div class="w-[80%] mx-auto place-items-center">
            <!-- judul utama -->
            <h2 class="text-3xl mb-14 font-semibold text-gray-800">
                Register <?php echo ucfirst($role); ?>
            </h2>

            <!-- peringatan error -->
            <?php 
                if (!empty($errorMsg)) echo "
                    <div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-5'>
                        <strong>$errorMsg</strong>
                    </div>
                ";
            ?>

            <!-- form register -->
            <form method="POST" action="">
                <label for="email">Email:</label>
                <input type="text" name="email" id="email" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>

                <div class="flex justify-between items-center mt-8">
                    <a href="../halaman_utama.php" class="bg-red-500 text-white py-2 px-5 rounded hover:bg-red-700">Kembali</a>
                    <input type="submit" value="Daftar" class="bg-blue-500 text-white py-2 px-5 rounded hover:bg-blue-600">
                </div>
            </form>

            <p class="text-lg mt-14">Sudah punya akun? 
                <a href="login.php?role=<?php echo $role ?>" class="underline text-blue-900">Masuk di sini</a>
            </p>
        </div>
    </div>
</body>
</html>
