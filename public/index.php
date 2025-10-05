<?php
/*
 * File: index.php
 * Lokasi: public/
 * Deskripsi: Halaman utama aplikasi minigame.
 */

session_start();

if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['currentPlayerName']);
    header("Location: index.php");
    exit;
}

if (isset($_POST['social_media_name'])) {
    $_SESSION['currentPlayerName'] = trim($_POST['social_media_name']);
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mamura Mini Games Booth</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="main-container">
        <header class="main-header">
            <img src="assets/images/Logo.png" alt="Mamura Logo" class="mamura-logo">
            <h1>MINI GAMES BOOTH</h1>
        </header>

        <?php if (!isset($_SESSION['currentPlayerName']) || empty($_SESSION['currentPlayerName'])): ?>
        
        <!-- MODIFIKASI: Kontainer QR Code sekarang menjadi elemen utama di tengah -->
        <div class="social-qr-container">
            <img src="assets/images/IG.png" alt="QR Instagram" class="qr-code">
            <img src="assets/images/FB.png" alt="QR Facebook" class="qr-code">
            <img src="assets/images/TK.png" alt="QR TikTok" class="qr-code">
        </div>

        <!-- MODIFIKASI: Input section sekarang hanya berisi form dan judulnya -->
        <div id="player-input-section">
            <h2>Masukkan Nama / Akun Sosmed</h2>
            <form method="POST" action="index.php">
                <input type="text" name="social_media_name" placeholder="Contoh: @mamura.net.id" required>
                <button type="submit">Mulai Petualangan</button>
            </form>
        </div>

        <?php else: ?>
        <!-- Tampilan setelah nama dimasukkan -->
        <div id="game-selection">
            <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['currentPlayerName']); ?>!</h2>
            <div class="subtitle-wrapper">
                <p>Pilih Game Untuk Dimainkan</p>
                <a href="index.php?action=reset" class="change-player-btn">(Bukan kamu? Ganti Pemain)</a>
            </div>
            <div class="game-list">
                <a href="games/jump/" class="game-card">
                    <img src="games/jump/assets/background.jpg" alt="Jump Game">
                    <div class="game-title">JUMP</div>
                </a>
                <div class="game-card disabled">
                    <img src="https://placehold.co/400x300/161b22/8b949e?text=Coming+Soon" alt="Game 2">
                    <div class="game-title">Coming Soon</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

