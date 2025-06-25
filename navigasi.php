<?php
    // cek apakah session sudah dideklarasikan
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once "koneksi.php";     // memastikan hanya didefinisikan sekali saja

    $namaUser = "";
    $dashboardLink = "#"; // default jika belum login

    // sesuaikan dashboard dengan role user jika sudah login
    if (isset($_SESSION['user'])) {
        $userId = $_SESSION['user']['user_id'];
        $role = $_SESSION['role'];

        if ($role === 'mahasiswa') {
            $query = "SELECT nama FROM data_mhs WHERE id = $userId";
            $dashboardLink = "/Praktikum Pemrograman Web/Praktikum 9-10. PHP DataBase MySQL/dashboard_user/mahasiswa.php";
        } elseif ($role === 'dosen') {
            $query = "SELECT nama FROM data_dosen WHERE id = $userId";
            $dashboardLink = "/Praktikum Pemrograman Web/Praktikum 9-10. PHP DataBase MySQL/dashboard_user/dosen.php";
        } elseif ($role === 'admin') {
            $query = "SELECT nama FROM data_admin WHERE id = $userId";
            $dashboardLink = "/Praktikum Pemrograman Web/Praktikum 9-10. PHP DataBase MySQL/dashboard_user/admin.php";
        }

        $userName = $kon->query($query);
        if ($userName && $row = $userName->fetch_assoc()) {
            $namaUser = $row['nama'];
        }
    }
?>

<!-- navigasi -->
<nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center p-4 backdrop-blur-sm bg-white/20 px-10">
    <!-- Logo kiri -->
    <a href="<?php echo $dashboardLink; ?>" class="text-blue-600 font-bold text-xl hover:underline">
        <img src="/Praktikum Pemrograman Web/Praktikum 9-10. PHP DataBase MySQL/assets/logo.png" alt="Logo" class="w-10 h-10 cursor-pointer transition-transform duration-300 ease-in-out hover:scale-110 active:scale-9 0" />
    </a>

    <!-- Konten kanan: login dan register atau user dan logout -->
    <div>
        <!-- jika user sudah login -->
        <?php if (isset($_SESSION['user'])): ?>
            <span class="mr-4 text-blue-600 text-lg">Halo, <?php echo htmlspecialchars($namaUser); ?></span>
            <a href="/Praktikum Pemrograman Web/Praktikum 9-10. PHP DataBase MySQL/account/logout.php" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-full transition duration-300">Logout</a>
        <!-- jika user belum login -->
        <?php else: ?>
            <button onclick="toggleModal()" class="cursor-pointer border border-blue-500 text-blue-500 bg-white/0 hover:bg-white/20 hover:text-blue-600 font-medium py-2 px-6 rounded-full mr-2 transition duration-500">
                Masuk
            </button>
            <button onclick="toggleRegisterModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-full transition duration-300">
                Daftar
            </button>
        <?php endif; ?>
    </div>
</nav>

<!-- Modal login -->
<div id="loginModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <!-- Modal Box -->
    <div id="modalBox" class="bg-white rounded-xl px-5 pt-7 pb-8 w-80 text-center shadow-lg transform scale-95 opacity-0 transition duration-300 ease-out">
        <div class="flex justify-between mb-4">
            <h2 class="text-2xl font-semibold mb-4">Pilih Jenis Login</h2>
            <svg 
                onclick="toggleModal()"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="cursor-pointer main-grid-item-icon" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
            <line x1="18" x2="6" y1="6" y2="18" />
            <line x1="6" x2="18" y1="6" y2="18" />
            </svg>
        </div>

        <!-- pilihan login -->
        <div class="flex flex-col gap-2">
            <a href="account/login.php?role=mahasiswa" class="flex items-center bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 gap-4 rounded transition">
                <i class="fa-solid fa-user-graduate text-center w-6"></i>
                <p>Mahasiswa</p>
            </a>
            <a href="account/login.php?role=dosen" class="flex items-center bg-green-500 hover:bg-green-600 text-white py-2 px-3 gap-4 rounded transition">
                <i class="fa-solid fa-chalkboard-user text-center w-6"></i>
                <p>Dosen</p>
            </a>
            <a href="account/login.php?role=admin" class="flex items-center bg-red-500 hover:bg-red-600 text-white py-2 px-3 gap-4 rounded transition">
                <i class="fa-solid fa-user-tie text-center w-6"></i>
                <p>Admin</p>
            </a>
        </div>
    </div>
</div>

<!-- Modal Register -->
<div id="registerModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div id="registerBox" class="bg-white rounded-xl px-5 pt-7 pb-8 w-80 text-center shadow-lg transform scale-95 opacity-0 transition duration-300 ease-out">
        <div class="flex justify-between mb-4">
            <h2 class="text-2xl font-semibold mb-4">Pilih Jenis Daftar</h2>
            <svg 
                onclick="toggleRegisterModal()"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="cursor-pointer" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
            <line x1="18" x2="6" y1="6" y2="18" />
            <line x1="6" x2="18" y1="6" y2="18" />
            </svg>
        </div>

        <!-- pilihan role pendaftaran -->
        <div class="flex flex-col gap-2">
            <a href="account/register.php?role=mahasiswa" class="flex items-center bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 gap-4 rounded transition">
                <i class="fa-solid fa-user-graduate text-center w-6"></i>
                <p>Mahasiswa</p>
            </a>
            <a href="account/register.php?role=dosen" class="flex items-center bg-green-500 hover:bg-green-600 text-white py-2 px-3 gap-4 rounded transition">
                <i class="fa-solid fa-chalkboard-user text-center w-6"></i>
                <p>Dosen</p>
            </a>
            <a href="account/register.php?role=admin" class="flex items-center bg-red-500 hover:bg-red-600 text-white py-2 px-3 gap-4 rounded transition">
                <i class="fa-solid fa-user-tie text-center w-6"></i>
                <p>Admin</p>
            </a>
        </div>
    </div>
</div>

<?php
    mysqli_data_seek($userName, 0);     // memindahkan pointer internal ke baris ke-0 dari hasil query
?>

<!-- script modal login -->
<script>
    // Fungsi untuk menampilkan modal login
    function toggleModal() {
        const modal = document.getElementById('loginModal');
        const box = document.getElementById('modalBox');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            // Tambah animasi masuk
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            // Tambah animasi keluar
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200); // tunggu animasi selesai
        }
    }

    // menutup modal jika klik di luar modal
    document.addEventListener('click', function (e) {
        const modal = document.getElementById('loginModal');
        const box = document.getElementById('modalBox');

        if (!modal.classList.contains('hidden') && e.target === modal) {
            toggleModal();
        }
    });
</script>

<!-- script modal register -->
<script>
    function toggleModal() {
        const modal = document.getElementById('loginModal');
        const box = document.getElementById('modalBox');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
    }

    function toggleRegisterModal() {
        const modal = document.getElementById('registerModal');
        const box = document.getElementById('registerBox');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 10);
        } else {
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
    }

    document.addEventListener('click', function (e) {
        const loginModal = document.getElementById('loginModal');
        const loginBox = document.getElementById('modalBox');
        if (!loginModal.classList.contains('hidden') && e.target === loginModal) {
            toggleModal();
        }

        const registerModal = document.getElementById('registerModal');
        const registerBox = document.getElementById('registerBox');
        if (!registerModal.classList.contains('hidden') && e.target === registerModal) {
            toggleRegisterModal();
        }
    });
</script>
