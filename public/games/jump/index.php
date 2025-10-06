<?php
/*
 * File: index.php
 * Lokasi: public/games/jump/
 * Deskripsi: Halaman utama untuk menjalankan game JUMP.
 */

// Memulai atau melanjutkan session untuk mengakses nama pemain
session_start();

// Proteksi: Jika pemain mencoba mengakses URL game ini secara langsung tanpa nama,
// mereka akan diarahkan kembali ke halaman utama untuk memasukkan nama.
if (!isset($_SESSION['currentPlayerName']) || empty($_SESSION['currentPlayerName'])) {
    header("Location: ../../index.php");
    exit; // Menghentikan eksekusi skrip
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JUMP Game</title>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  
<audio id="jumpSound" src="assets/jump.mp3" preload="auto"></audio>
<audio id="menuMusic" src="assets/menu.mp3" preload="auto" loop autoplay></audio>

<canvas id="gameCanvas"></canvas>

  <div id="gameOverScreen">
    <div class="gameOver-box">
      <h1>Game Over</h1>
      <p>Your Final Score:</p>
      <div id="finalScore">0</div>
      <a href="../../index.php?action=reset" class="back-button">Kembali Ke Menu</a>
    </div>
  </div>

  <div id="countdown"></div>

  <script>
    // Mengirim nama pemain dan jenis sosmed ke JavaScript
    const currentPlayerName = "<?php echo htmlspecialchars($_SESSION['currentPlayerName']); ?>";
    const currentPlayerSocialType = "<?php echo htmlspecialchars($_SESSION['currentPlayerSocialType']); ?>";
  </script>
  <script src="game.js"></script>
</body>
</html>