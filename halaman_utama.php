<?php
    session_start();
    include 'koneksi.php';

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Halaman Utama</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right,rgba(84, 90, 248, 0.75),rgb(133, 251, 255));
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 80px auto;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2rem;
        }

        .role-section {
            margin-bottom: 30px;
        }

        .role-section h2 {
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .btn-group {
            display: flex;
            gap: 20px;
        }

        .btn {
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .btn-login {
            background-color: #3490dc;
        }

        .btn-login:hover {
            background-color: #2779bd;
        }

        .btn-register {
            background-color: #38c172;
        }

        .btn-register:hover {
            background-color: #2fa360;
        }

    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <?php
        // Cek apakah role sudah di-set dalam session, jika sudah berarti sudah login
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];
            
            // tampilan dashboard kondisional berdasarkan role
            if ($role === 'admin') {
                include './dashboard_user/admin.php';
            } elseif ($role === 'dosen') {
                include './dashboard_user/dosen.php';
            } elseif ($role === 'mahasiswa') {
                include './dashboard_user/mahasiswa.php';
            }
        } else {
    ?>

        <!-- tampilan jika user belum login -->
        <div class="container mx-auto px-4 py-10">
            <h1 class="text-3xl font-bold text-center mb-10">Selamat Datang di Sistem Login & Registrasi</h1>

            <!-- pilihan login setiap role -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Mahasiswa -->
                <div class="bg-white shadow-lg rounded-lg p-6 text-center hover:shadow-2xl transition-shadow duration-300">
                    <h2 class="text-xl font-semibold mb-6">Mahasiswa</h2>
                    <img src="assets/mhs.png" alt="Mahasiswa" class="w-32 h-32 mx-auto mb-5 text-blue-600 object-cover rounded-full">
                    <div class="flex flex-col gap-4 mb-2">
                        <a href="account/login.php?role=mahasiswa" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-full transition duration-300">Login</a>
                        <a href="account/register.php?role=mahasiswa" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-full transition duration-300">Daftar</a>
                    </div>
                </div>

                <!-- Dosen -->
                <div class="bg-white shadow-lg rounded-lg p-6 text-center hover:shadow-2xl transition-shadow duration-300">
                    <h2 class="text-xl font-semibold mb-6">Dosen</h2>
                    <img src="assets/dosen.png" alt="Dosen" class="w-32 h-32 mx-auto mb-5 object-cover rounded-full">
                    <div class="flex flex-col gap-4 mb-2">
                        <a href="account/login.php?role=dosen" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-full transition duration-300">Login</a>
                        <a href="account/register.php?role=dosen" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-full transition duration-300">Daftar</a>
                    </div>
                </div>

                <!-- Admin -->
                <div class="bg-white shadow-lg rounded-lg p-6 text-center hover:shadow-2xl transition-shadow duration-300">
                    <h2 class="text-xl font-semibold mb-6">Admin</h2>
                    <img src="assets/admin.png" alt="Admin" class="w-32 h-32 mx-auto mb-5 object-cover rounded-full">
                    <div class="flex flex-col gap-4 mb-2">
                        <a href="account/login.php?role=admin" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-full transition duration-300">Login</a>
                        <a href="account/register.php?role=admin" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-full transition duration-300">Daftar</a>
                    </div>
                </div>
            </div>
        </div>
    <?php
        }
    ?>
</body>
</html>

<?php
    $kon->close();
?>
