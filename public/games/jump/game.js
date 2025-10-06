/*
 * File: game.js
 * Lokasi: public/games/jump/
 * Deskripsi: Semua logika inti untuk permainan JUMP.
 */

// --- Inisialisasi Variabel Global & Elemen HTML ---
const jumpSound = document.getElementById("jumpSound");
const menuMusic = document.getElementById("menuMusic");
const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

// --- Variabel & Konstanta Permainan ---
let gameRunning = false;
let bird, pipes, score, lives, speed;
let pausedAfterHit = false;
const pipeDistance = 380;

// --- Memuat Aset Gambar ---
let background = new Image();
background.src = "assets/background.jpg";
let birdImg = new Image();
birdImg.src = "assets/bird.png";
let pipeTopImg = new Image();
pipeTopImg.src = "assets/cloud.png";
let pipeBottomImg = new Image();
pipeBottomImg.src = "assets/tower.png";
let gameFrameImg = new Image();
gameFrameImg.src = "assets/menuFrame.png";

let assetsLoaded = 0;
const totalAssets = 5;
[background, birdImg, pipeTopImg, pipeBottomImg, gameFrameImg].forEach(img => {
    img.onload = () => {
        assetsLoaded++;
        if (assetsLoaded === totalAssets) {
            document.dispatchEvent(new Event('assetsReady'));
        }
    };
});

// Fungsi untuk mereset HANYA level (posisi karakter & rintangan)
function resetLevel() {
  bird = { x: 80, y: canvas.height / 2, width: 100, height: 80, gravity: 0, jump: -6 };
  pipes = [];
  speed = 2;
  pausedAfterHit = false;
  
  let initialPipes = 5;
  for (let i = 0; i < initialPipes; i++) {
    let gap = 200;
    let topHeight = Math.random() * (canvas.height / 2) + 50;
    let bottomY = topHeight + gap;
    let pipeX = (canvas.width / 1.5) + (i * pipeDistance);
    pipes.push({ x: pipeX, y: 0, width: 80, height: topHeight, type: "top" });
    pipes.push({ x: pipeX, y: bottomY, width: 80, height: canvas.height - bottomY, type: "bottom" });
  }
}

function startCountdown(onComplete) {
  const countdown = document.getElementById("countdown");
  // Pastikan baris ini menggunakan "flex", bukan "block"
  countdown.style.display = "flex"; 
  let count = 3;
  countdown.innerText = count;
  const interval = setInterval(() => {
    count--;
    if (count > 0) {
      countdown.innerText = count;
    } else {
      clearInterval(interval);
      countdown.style.display = "none";
      if (onComplete) onComplete();
    }
  }, 1000);
}

// --- Fungsi Gambar ---
function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(background, 0, 0, canvas.width, canvas.height);
    pipes.forEach(pipe => {
        if (pipe.type === "top") ctx.drawImage(pipeTopImg, pipe.x, pipe.y, pipe.width, pipe.height);
        else ctx.drawImage(pipeBottomImg, pipe.x, pipe.y, pipe.width, pipe.height);
    });
    
    // Memanggil fungsi drawTail() di sini
    drawTail();

    ctx.drawImage(birdImg, bird.x, bird.y, bird.width, bird.height);
    ctx.drawImage(gameFrameImg, 0, 0, canvas.width, canvas.height);
    ctx.fillStyle = "white";
    ctx.font = "30px 'Press Start 2P', cursive";
    ctx.fillText("Score: " + score, 20, 40);
    drawLives();
}

function drawLives() {
    const maxLives = 3;
    for (let i = 0; i < maxLives; i++) {
        const heartSize = 30, padding = 10, startX = 20, startY = 60;
        const x = startX + i * (heartSize + padding), y = startY;
        ctx.beginPath();
        ctx.moveTo(x + heartSize / 2, y + heartSize * 0.4);
        ctx.bezierCurveTo(x + heartSize / 2, y + heartSize * 0.3, x, y, x, y + heartSize * 0.6);
        ctx.bezierCurveTo(x, y + heartSize, x + heartSize, y + heartSize, x + heartSize, y + heartSize * 0.6);
        ctx.bezierCurveTo(x + heartSize, y, x + heartSize / 2, y + heartSize * 0.3, x + heartSize / 2, y + heartSize * 0.4);
        ctx.closePath();
        ctx.fillStyle = i < lives ? '#ff0303' : '#555';
        ctx.fill();
    }
}

// **MODIFIKASI: Fungsi untuk menggambar ekor/jejak roket diperbaiki**
let tailParticles = [];
function drawTail () {
  // Posisi partikel disesuaikan agar pas dengan belakang roket (nozzle).
  tailParticles.push({ x: bird.x + 20, y: bird.y + bird.height / 2, alpha: 1 });
  
  ctx.save();
  tailParticles.forEach((particle, index) => {
    // Menggunakan gradient untuk efek api yang lebih bagus.
    const gradient = ctx.createLinearGradient(particle.x, particle.y, particle.x - 100, particle.y);
    gradient.addColorStop(0, `rgba(0, 255, 255, ${particle.alpha})`); // Cyan terang di sumber
    gradient.addColorStop(1, `rgba(0, 100, 255, 0)`); // Pudar menjadi biru transparan di ujung

    ctx.strokeStyle = gradient;
    ctx.lineWidth = 25; // Sedikit lebih tipis
    ctx.lineCap = 'round'; // Ujung yang lebih halus
    ctx.shadowBlur = 15;
    ctx.shadowColor = "rgba(0, 200, 255, 0.7)";

    ctx.beginPath();
    ctx.moveTo(particle.x, particle.y);
    ctx.lineTo(particle.x - 100, particle.y);
    ctx.stroke();

    // Pudar lebih cepat agar efeknya tidak terlalu panjang.
    particle.alpha -= 0.04;
    if (particle.alpha <= 0) {
      tailParticles.splice(index, 1);
    }
  });
  ctx.restore();
}

// --- Fungsi Logika ---
function update() {
  if (pausedAfterHit) return;
  bird.gravity += 0.4;
  bird.y += bird.gravity;
  pipes.forEach(pipe => pipe.x -= speed);
  while (pipes.length > 0 && pipes[0].x + pipes[0].width < 0) { pipes.splice(0, 2); }
  let lastPipe = pipes[pipes.length - 1];
  if (lastPipe.x < canvas.width - pipeDistance) {
    let gap = 200, topHeight = Math.random() * (canvas.height / 2) + 50, bottomY = topHeight + gap;
    let newPipeX = lastPipe.x + pipeDistance;
    pipes.push({ x: newPipeX, y: 0, width: 80, height: topHeight, type: "top" });
    pipes.push({ x: newPipeX, y: bottomY, width: 80, height: canvas.height - bottomY, type: "bottom" });
  }
  if (bird.y < 0 || bird.y + bird.height > canvas.height || detectCollision()) {
    loseLife();
  } else {
    score++;
    if (score > 0 && score % 500 === 0) speed += 0.5;
  }
}

function detectCollision() {
  const hitbox = { x: bird.x + 20, y: bird.y + 15, width: bird.width - 40, height: bird.height - 30 };
  return pipes.some(p => hitbox.x < p.x + p.width && hitbox.x + hitbox.width > p.x && hitbox.y < p.y + p.height && hitbox.y + hitbox.height > p.y);
}

function loseLife() {
  lives--;
  if (lives <= 0) {
    endGame();
  } else {
    pausedAfterHit = true;
    startCountdown(() => {
        resetLevel(); // Hanya reset level, bukan seluruh game
        pausedAfterHit = false;
    });
  }
}

async function endGame() {
  gameRunning = false;
  const gameData = { 
    game_name: 'JUMP', 
    social_media: currentPlayerName, 
    social_media_type: currentPlayerSocialType,
    score: score 
  };
  try {
    const response = await fetch('/api/save-score.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(gameData),
    });
    console.log("Status Respons Server:", response.status, response.statusText);
    const result = await response.json();
    console.log('Respons Server (JSON):', result);
  } catch (error) {
    console.error('Error saat mengirim skor:', error);
  }
  document.getElementById("finalScore").innerText = score;
  document.getElementById("gameOverScreen").style.display = "flex";
}

// --- Game Loop Utama ---
function gameLoop() {
  if (!gameRunning) return;
  update();
  draw();
  requestAnimationFrame(gameLoop);
}

// --- Event Listener ---
function handleJump() {
  if (gameRunning && !pausedAfterHit) {
    bird.gravity = -6;
    jumpSound.currentTime = 0;
    jumpSound.play();
  }
}
document.addEventListener("keydown", e => e.code === "Space" && handleJump());
document.addEventListener("mousedown", e => e.button === 0 && handleJump());

// --- Inisialisasi Game ---
document.addEventListener('assetsReady', () => {
    score = 0;
    lives = 3; 
    resetLevel(); 
    startCountdown(() => {
        gameRunning = true;
        gameLoop();
    });
});