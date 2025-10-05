#!/bin/bash
# 'set -e' akan menghentikan skrip secara otomatis jika terjadi error.
set -e

echo "--- Memulai Skrip Entrypoint ---"

# DEBUG: Tampilkan isi direktori untuk memastikan file telah disalin dengan benar.
echo "Isi direktori /var/www/minigame/:"
ls -la /var/www/minigame/
echo "Isi direktori /var/www/minigame/database/:"
ls -la /var/www/minigame/database/

echo "Memulai layanan MySQL..."
service mysql start

# Cek apakah database sudah ada atau belum.
if [ ! -d "/var/lib/mysql/minigame_db" ]; then
    echo "Database tidak ditemukan. Melakukan konfigurasi awal MySQL..."
    # Tunggu beberapa detik untuk memastikan MySQL siap.
    sleep 5

    # Mengambil password dari environment variable.
    DB_PASSWORD=${MYSQL_ROOT_PASSWORD:-minigamemamura}
    
    echo "Mengatur password root dan mengimpor schema database..."
    
    # Menjalankan semua perintah MySQL dalam satu sesi untuk menghindari masalah autentikasi.
    # Perintah di antara <<EOF dan EOF akan dieksekusi oleh mysql.
    mysql -u root --skip-password <<EOF
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${DB_PASSWORD}';
FLUSH PRIVILEGES;
SOURCE /var/www/minigame/database/schema.sql;
EOF

    echo "Konfigurasi MySQL berhasil."
else
    echo "Database sudah ada. Melewatkan konfigurasi."
fi

# Teruskan environment variable agar bisa dibaca oleh PHP.
export MYSQL_ROOT_PASSWORD

# Mulai service Apache di foreground agar container tetap berjalan.
echo "Memulai layanan Apache..."
exec apache2ctl -D FOREGROUND