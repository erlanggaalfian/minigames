<?php
/*
 * File: save-score.php
 * Lokasi: src/api/
 * Deskripsi: API untuk menerima dan menyimpan skor ke database.
 */

header("Content-Type: application/json");
require_once __DIR__ . '/../../config/database.php';

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Memastikan semua data yang dibutuhkan ada
if (!empty($data['game_name']) && !empty($data['social_media']) && !empty($data['social_media_type']) && isset($data['score'])) {
  
  $game_name = $data['game_name'];
  $social_media = $data['social_media'];
  $social_media_type = $data['social_media_type']; // Ambil data baru
  $score = $data['score'];

  // Update statement untuk memasukkan kolom baru
  $stmt = $conn->prepare("INSERT INTO scores (game_name, social_media_name, social_media_type, score) VALUES (?, ?, ?, ?)");
  
  if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal menyiapkan statement database: " . $conn->error]);
    exit;
  }

  // Mengikat parameter: 'sssi' -> string, string, string, integer
  $stmt->bind_param("sssi", $game_name, $social_media, $social_media_type, $score);

  if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Skor berhasil disimpan!"]);
  } else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Gagal menjalankan statement database: " . $stmt->error]);
  }
  
  $stmt->close();
} else {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Data tidak lengkap.", "data_diterima" => $data]);
}

$conn->close();
?>