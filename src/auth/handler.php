<?php
/*
 * File: handler.php
 * Lokasi: src/auth/
 * Deskripsi: Menangani semua logika autentikasi: login, logout, dan setup admin pertama.
 */
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // --- Logika untuk Aksi LOGIN ---
    if ($_POST['action'] === 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT password_hash FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                header("Location: /admin/dashboard.php");
                exit;
            }
        }
        
        $_SESSION['error_message'] = "Username atau password salah.";
        header("Location: /admin/");
        exit;
    }

    // --- Logika untuk Aksi LOGOUT ---
    if ($_POST['action'] === 'logout') {
        session_destroy();
        header("Location: /admin/");
        exit;
    }

    // --- Logika untuk Aksi SETUP ---
    if ($_POST['action'] === 'setup') {
        // Keamanan: Cek lagi apakah sudah ada admin
        $result = $conn->query("SELECT id FROM admins LIMIT 1");
        if ($result->num_rows > 0) {
            header("Location: /admin/");
            exit;
        }

        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $_SESSION['error_message'] = "Username dan password tidak boleh kosong.";
            header("Location: /admin/setup.php");
            exit;
        }

        // Hash password untuk keamanan
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Simpan admin baru ke database
        $stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password_hash);

        if ($stmt->execute()) {
            // Jika berhasil, langsung loginkan admin
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: /admin/dashboard.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Terjadi kesalahan. Gagal membuat akun.";
            header("Location: /admin/setup.php");
            exit;
        }
    }
}

// Jika file diakses secara tidak semestinya, arahkan ke halaman utama.
header("Location: /");
exit;
?>
