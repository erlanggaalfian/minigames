<?php
/*
 * File: delete_scores.php
 * Lokasi: public/admin/
 * Deskripsi: Menangani logika untuk menghapus skor yang dipilih dari dashboard.
 */

session_start();

// Keamanan: Pastikan hanya admin yang sudah login yang bisa menjalankan skrip ini.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403); // Kode 403: Forbidden
    die("Akses ditolak. Anda harus login sebagai admin.");
}

// Memanggil file koneksi database terpusat.
// Path ini disesuaikan dari lokasi file saat ini.
require_once __DIR__ . '/../../config/database.php';

// Pastikan permintaan adalah POST dan ada ID skor yang dikirim.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['score_ids'])) {
    
    $score_ids = $_POST['score_ids'];

    // Validasi: Pastikan semua ID adalah angka untuk mencegah SQL Injection.
    $sanitized_ids = [];
    foreach ($score_ids as $id) {
        if (is_numeric($id)) {
            $sanitized_ids[] = (int)$id;
        }
    }

    if (!empty($sanitized_ids)) {
        // Membuat placeholder '?' sebanyak jumlah ID yang akan dihapus.
        $placeholders = implode(',', array_fill(0, count($sanitized_ids), '?'));

        // Menyiapkan perintah SQL DELETE yang aman.
        $stmt = $conn->prepare("DELETE FROM scores WHERE id IN ($placeholders)");

        // Mengikat setiap ID ke placeholder.
        $stmt->bind_param(str_repeat('i', count($sanitized_ids)), ...$sanitized_ids);

        // Jalankan perintah.
        $stmt->execute();
        $stmt->close();
    }
}

// Setelah selesai, arahkan admin kembali ke halaman dashboard.
header("Location: dashboard.php");
exit;
?>
