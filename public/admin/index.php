<?php
/*
 * File: index.php
 * Lokasi: public/admin/
 * Deskripsi: Halaman login untuk panel admin. Juga menangani pengalihan ke halaman setup jika diperlukan.
 */

// Memulai atau melanjutkan session untuk mengelola status login
session_start();

// Memanggil file koneksi database untuk melakukan pengecekan
require_once __DIR__ . '/../../config/database.php';

// --- Logika Pengalihan Setup ---
// Cek apakah ada admin di database. Jika tidak ada sama sekali,
// arahkan ke halaman setup untuk membuat akun pertama.
$result = $conn->query("SELECT id FROM admins LIMIT 1");
if ($result->num_rows === 0) {
    header("Location: setup.php");
    exit;
}

// --- Logika Pengalihan Dashboard ---
// Jika admin sudah login, langsung arahkan ke dashboard.
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

// --- Menampilkan Pesan Error ---
// Mengecek apakah ada pesan error dari percobaan login sebelumnya.
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']); // Hapus pesan agar tidak muncul lagi
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Menghubungkan ke file CSS admin -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <!-- Form login akan mengirim data ke file perantara 'auth.php' -->
        <form action="auth.php" method="POST">
            <!-- Input tersembunyi untuk memberi tahu handler bahwa ini adalah aksi 'login' -->
            <input type="hidden" name="action" value="login">

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <?php if ($error_message): ?>
                <!-- Menampilkan pesan error jika ada -->
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>

