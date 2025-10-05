<?php
/*
 * File: save-score.php
 * Lokasi: src/api/
 * Deskripsi: API untuk menerima dan menyimpan skor ke database.
 * Versi ini menyertakan penanganan error yang lebih detail.
 */

// Menampilkan semua error PHP untuk memudahkan debugging.
// Hapus atau beri komentar pada baris ini saat aplikasi sudah live (produksi).
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Header untuk mengizinkan permintaan dari JavaScript (CORS) dan menentukan tipe konten.
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Memanggil file koneksi database terpusat yang aman.
require_once __DIR__ . '/../../config/database.php';

// Mengambil data JSON mentah yang dikirim dari game.js.
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Cek apakah proses decoding JSON berhasil.
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Kode error 400: Bad Request
    echo json_encode(["status" => "error", "message" => "Format JSON yang diterima tidak valid."]);
    exit;
}

// Memastikan semua data yang dibutuhkan ada dan tidak kosong.
if (!empty($data['game_name']) && !empty($data['social_media']) && isset($data['score'])) {
  
  $game_name = $data['game_name'];
  $social_media = $data['social_media'];
  $score = $data['score'];

  // Menggunakan Prepared Statement untuk keamanan (mencegah SQL Injection).
  $stmt = $conn->prepare("INSERT INTO scores (game_name, social_media_name, score) VALUES (?, ?, ?)");
  
  // Cek jika proses 'prepare statement' gagal (misalnya, nama tabel atau kolom salah).
  if ($stmt === false) {
    http_response_code(500); // Kode error 500: Internal Server Error
    echo json_encode(["status" => "error", "message" => "Gagal menyiapkan statement database: " . $conn->error]);
    exit;
  }

  // Mengikat parameter: 'ssi' berarti tipe datanya string, string, integer.
  $stmt->bind_param("ssi", $game_name, $social_media, $score);

  // Menjalankan perintah untuk memasukkan data ke dalam tabel.
  if ($stmt->execute()) {
    // Jika berhasil, kirim respons sukses.
    echo json_encode(["status" => "success", "message" => "Skor berhasil disimpan!"]);
  } else {
    // Jika gagal, kirim pesan error yang spesifik.
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal menjalankan statement database: " . $stmt->error]);
  }
  
  // Menutup statement.
  $stmt->close();
} else {
  // Jika ada data yang kurang atau kosong.
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Data tidak lengkap.", "data_diterima" => $data]);
}

// Menutup koneksi database.
$conn->close();
?>

