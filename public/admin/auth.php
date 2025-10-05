<?php
/*
 * File: auth.php
 * Lokasi: public/admin/
 * Deskripsi: File ini bertindak sebagai perantara publik yang aman.
 * Ia menerima permintaan dari form login/logout dan memanggil file
 * logika sebenarnya yang berada di luar direktori publik ('src').
 */

// Menambahkan ini di bagian atas akan membantu menampilkan error PHP yang sebenarnya
// selama masa development, bukan hanya halaman error 500.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Memanggil file handler yang berisi semua logika dari direktori 'src'.
// Path ini aman karena dieksekusi di sisi server.
require_once __DIR__ . '/../../src/auth/handler.php';

