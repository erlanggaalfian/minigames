/*
 * File: main.js
 * Lokasi: public/assets/js/
 * Deskripsi: Skrip ini bertanggung jawab untuk menganimasikan latar belakang
 * kanvas di halaman utama (index.php) agar terlihat lebih hidup.
 */

// --- Inisialisasi Canvas ---
// Mengambil elemen canvas dari HTML
const canvas = document.getElementById('background-canvas');
// Mendapatkan "alat gambar" 2D untuk canvas
const ctx = canvas.getContext('2d');

// Mengatur ukuran canvas agar memenuhi seluruh jendela browser
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

// Array untuk menyimpan semua objek partikel
let particlesArray;

// --- Class (Blueprint) untuk Partikel ---
// Class ini mendefinisikan bagaimana setiap partikel harus terlihat dan bergerak.
class Particle {
    constructor(x, y, directionX, directionY, size, color) {
        this.x = x; // Posisi horizontal
        this.y = y; // Posisi vertikal
        this.directionX = directionX; // Arah gerak horizontal
        this.directionY = directionY; // Arah gerak vertikal
        this.size = size; // Ukuran partikel
        this.color = color; // Warna partikel
    }

    // Method untuk menggambar partikel di canvas
    draw() {
        ctx.beginPath();
        // Menggambar lingkaran (arc) untuk setiap partikel
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2, false);
        ctx.fillStyle = this.color;
        ctx.fill();
    }

    // Method untuk memperbarui posisi partikel di setiap frame animasi
    update() {
        // Jika partikel menyentuh tepi horizontal (kiri/kanan) canvas, balik arahnya
        if (this.x > canvas.width || this.x < 0) {
            this.directionX = -this.directionX;
        }
        // Jika partikel menyentuh tepi vertikal (atas/bawah) canvas, balik arahnya
        if (this.y > canvas.height || this.y < 0) {
            this.directionY = -this.directionY;
        }

        // Memperbarui posisi partikel berdasarkan arah geraknya
        this.x += this.directionX;
        this.y += this.directionY;

        // Menggambar ulang partikel di posisi barunya
        this.draw();
    }
}

// --- Fungsi Utama ---

// Fungsi untuk membuat dan mengisi array dengan partikel-partikel
function init() {
    particlesArray = [];
    // Menentukan jumlah partikel secara dinamis berdasarkan ukuran layar
    let numberOfParticles = (canvas.height * canvas.width) / 9000;
    
    for (let i = 0; i < numberOfParticles; i++) {
        // Memberi nilai acak pada setiap properti partikel
        let size = (Math.random() * 5) + 1;
        let x = (Math.random() * ((innerWidth - size * 2) - (size * 2)) + size * 2);
        let y = (Math.random() * ((innerHeight - size * 2) - (size * 2)) + size * 2);
        let directionX = (Math.random() * 0.4) - 0.2; // Kecepatan gerak horizontal (bisa positif/negatif)
        let directionY = (Math.random() * 0.4) - 0.2; // Kecepatan gerak vertikal
        let color = 'rgba(240, 146, 40, 0.5)'; // Warna oranye transparan

        particlesArray.push(new Particle(x, y, directionX, directionY, size, color));
    }
}

// Fungsi loop animasi yang berjalan terus-menerus
function animate() {
    // Meminta browser untuk menjalankan fungsi 'animate' lagi di frame berikutnya
    requestAnimationFrame(animate);
    // Membersihkan seluruh canvas sebelum menggambar frame baru
    ctx.clearRect(0, 0, innerWidth, innerHeight);

    // Memperbarui posisi setiap partikel di dalam array
    for (let i = 0; i < particlesArray.length; i++) {
        particlesArray[i].update();
    }
}

// --- Event Listener ---
// Menjalankan fungsi 'init' lagi jika ukuran jendela browser berubah
// agar jumlah dan posisi partikel disesuaikan (responsif)
window.addEventListener('resize', () => {
    canvas.width = innerWidth;
    canvas.height = innerHeight;
    init();
});

// --- Menjalankan Animasi ---
init();   // Buat partikel pertama kali
animate(); // Mulai loop animasi
