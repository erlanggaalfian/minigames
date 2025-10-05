<?php
/*
 * File: save-score.php
 * Lokasi: public/api/
 * Deskripsi: File ini bertindak sebagai perantara publik yang aman untuk API.
 * Ia menerima permintaan dari game dan memanggil file logika sebenarnya
 * yang berada di luar direktori publik ('src').
 */

// Memanggil file handler yang berisi semua logika dari direktori 'src'.
// Path ini aman karena dieksekusi di sisi server.
require_once __DIR__ . '/../../src/api/save-score.php';
