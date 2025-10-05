<?php
/*
 * File: database.php
 * Lokasi: config/
 * Deskripsi: File terpusat untuk koneksi ke database MySQL.
 */

// --- Pengaturan Variabel Koneksi ---
$servername = "127.0.0.1";       // PENTING: Gunakan nama service dari docker-compose.yml, BUKAN 'localhost'.
$username   = "root";
$password   = "minigamemamura";
$dbname     = "minigame_db";

// --- Logika Koneksi dengan Retry ---
$max_attempts = 5; // Jumlah maksimal percobaan koneksi
$attempt = 0;
$conn = null; // Inisialisasi koneksi sebagai null

while ($attempt < $max_attempts) {
    try {
        // Mencoba membuat koneksi baru
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Jika tidak ada error koneksi, keluar dari loop
        if (!$conn->connect_error) {
            break; 
        }
    } catch (mysqli_sql_exception $e) {
        // Tangkap error jika koneksi ditolak, tapi jangan hentikan skrip dulu
    }

    // Jika koneksi gagal, tunggu 2 detik sebelum mencoba lagi
    $attempt++;
    sleep(2);
}

// Setelah loop selesai, cek apakah koneksi berhasil dibuat
if ($conn === null || $conn->connect_error) {
  // Jika tetap gagal setelah semua percobaan, hentikan aplikasi dan tampilkan error
  die("Koneksi database gagal setelah beberapa percobaan: " . ($conn ? $conn->connect_error : mysqli_connect_error()));
}
$conn->query("SET time_zone = '+07:00'");
?>