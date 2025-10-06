<?php
/*
 * File: setup.php
 * Lokasi: public/admin/
 * Deskripsi: Halaman untuk membuat akun admin pertama jika belum ada.
 */
session_start();
require_once __DIR__ . '/../../config/database.php';

// Cek lagi: jika sudah ada admin, jangan tampilkan halaman ini.
$result = $conn->query("SELECT id FROM admins LIMIT 1");
if ($result->num_rows > 0) {
    header("Location: index.php");
    exit;
}

$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Setup</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="login-container">
        <h2>Buat Akun Admin Pertama</h2>
        <p style="text-align: center; margin-bottom: 1rem;">Belum ada akun admin terdeteksi. Silakan buat akun pertama Anda.</p>
        <form action="auth.php" method="POST">
            <input type="hidden" name="action" value="setup">

            <div class="input-group">
                <label for="username">Username Baru</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password" required>
            </div>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <button type="submit">Buat Akun & Login</button>
        </form>
    </div>
</body>
</html>