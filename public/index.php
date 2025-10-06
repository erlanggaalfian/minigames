<?php
/*
 * File: index.php
 * Lokasi: public/
 * Deskripsi: Halaman utama aplikasi minigame.
 */

session_start();

if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['currentPlayerName']);
    unset($_SESSION['currentPlayerSocialType']);
    header("Location: index.php");
    exit;
}

if (isset($_POST['social_media_name']) && isset($_POST['social_media_type'])) {
    if (trim($_POST['social_media_name']) !== '') {
        $_SESSION['currentPlayerName'] = trim($_POST['social_media_name']);
        $_SESSION['currentPlayerSocialType'] = trim($_POST['social_media_type']);
        header("Location: index.php");
        exit;
    }
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
    <audio src="games/jump/assets/menu.mp3" autoplay loop></audio>

    <div class="main-container">
        <header class="main-header">
            <h1>MINI GAMES BOOTH</h1>
        </header>

        <?php if (!isset($_SESSION['currentPlayerName']) || empty($_SESSION['currentPlayerName'])): ?>
        
        <div class="social-qr-container">
            <img src="assets/images/IG.png" alt="QR Instagram" class="qr-code">
            <img src="assets/images/FB.png" alt="QR Facebook" class="qr-code">
            <img src="assets/images/TK.png" alt="QR TikTok" class="qr-code">
        </div>

        <div class="input-card">
            <h2>Masukkan Akun Sosmed</h2>
            <form method="POST" action="index.php">
                
                <p class="choice-instruction">Pilih Salah Satu:</p>

                <div class="social-choice">
                    <input type="radio" id="ig" name="social_media_type" value="Instagram" class="social-choice-input" required>
                    <label for="ig" class="social-choice-label">Instagram</label>

                    <input type="radio" id="fb" name="social_media_type" value="Facebook" class="social-choice-input" required>
                    <label for="fb" class="social-choice-label">Facebook</label>

                    <input type="radio" id="tk" name="social_media_type" value="TikTok" class="social-choice-input" required>
                    <label for="tk" class="social-choice-label">TikTok</label>
                </div>

                <div class="input-group">
                    <input type="text" name="social_media_name" placeholder="Nama Akun (Contoh=@mamura.net.id)" required>
                    <button type="submit">Mulai Game</button>
                </div>
            </form>
        </div>

        <?php else: ?>
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

    <img src="assets/images/menuFrame.png" alt="Game Frame" class="main-frame"> 
</body>
</html>