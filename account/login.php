<?php
    session_start();
    require_once "../koneksi.php"; // koneksi ke database

    $errorMsg = "";

    // Ambil role dari URL yang memastikan hanya sekali dan valid
    $allowed_roles = ['mahasiswa', 'dosen', 'admin'];
    $role = isset($_GET['role']) && in_array($_GET['role'], $allowed_roles) ? $_GET['role'] : 'mahasiswa';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Ambil input dari form
        $emailInput = $kon->real_escape_string($_POST['email']);
        $passwordInput = $_POST['password'];

        // Query berdasarkan email dan role
        $sql = "SELECT * FROM akun WHERE email = '$emailInput' AND role = '$role'";
        $result = $kon->query($sql);

        if ($result && $result->num_rows === 1) {
            $akun = $result->fetch_assoc();

            // Verifikasi password (gunakan md5 atau password_verify jika menggunakan hash modern)
            if (md5($passwordInput) === $akun['password']) {
                $_SESSION['user'] = $akun;
                $_SESSION['role'] = $akun['role'];
                $_SESSION['email'] = $akun['email'];

                // Redirect ke halaman utama
                header("Location: ../halaman_utama.php");
                exit;
            } else {
                $errorMsg = "Password salah.";
            }
        } else {
            $errorMsg = "Akun tidak ditemukan.";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
                Login <?php echo ucfirst($role); ?>
            </h2>
            
            <!-- peringatan error -->
            <?php 
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($errorMsg)) echo "
                    <div id='alertBox' class='w-full bg-red-100 border border-red-400 text-red-700 px-4 py-3 mt-5 rounded relative' role='alert'>
                        <strong class='font-bold'>$errorMsg</strong>
                        <span class='block sm:inline'>Mungkin ada yang typo?</span>
                        <span onclick=\"document.getElementById('alertBox').style.display='none'\"
                            class='absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer'>
                            <svg class='fill-current h-6 w-6 text-red-500' role='button' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'>
                                <title>Close</title>
                                <path d='M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697
                                    l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697
                                    l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z'/>
                            </svg>
                        </span>
                    </div>
                ";
            ?>

            <!-- form login -->
            <form method="POST" action="">
                <!-- Field untuk memasukkan email atau username -->
                <label for="email">Email:</label>
                <input type="text" name="email" id="email" required>
                
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
                <br>
                <div class="flex justify-between items-center mt-8">
                    <!-- Tombol kembali ke index atau halaman awal -->
                    <a href="../halaman_utama.php" class="no-underline h-fit bg-red-500 text-white py-2 px-5 rounded transition duration-300 ease-in-out hover:bg-red-700 hover:scale-105">Kembali</a>
                    
                    <!-- Tombol submit -->
                    <input type="submit" value="Login" class="py-2.5 px-5 border-0 bg-[rgb(54,149,251)] text-white rounded-md cursor-pointer text-base transition-all duration-300 ease-in-out hover:bg-[rgb(9,127,252)] hover:scale-[1.03] hover:shadow-[0_4px_10px_rgba(0,0,0,0.2)]">
                </div>
            </form>
    
            <!-- Tombol untuk berpindah ke halaman Register -->
            <p class="text-lg mt-14">Belum punya akun? <a href="register.php" class="underline underline-offset-4 text-blue-900">Daftar di sini</a></p>
        </div>
    </div>
</body>
</html>
