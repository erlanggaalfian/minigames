<?php
/*
 * File: dashboard.php
 * Lokasi: public/admin/
 * Deskripsi: Halaman utama panel admin untuk menampilkan, memfilter, dan menghapus skor.
 */
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

// --- Logika untuk Filter dan Urutan ---
$filter_game = $_GET['filter_game'] ?? 'all';
$sort_by = $_GET['sort_by'] ?? 'latest';
$order = $_GET['order'] ?? 'desc';
$next_order = ($order === 'desc') ? 'asc' : 'desc';

// --- Ambil Data Statistik Ringkas ---
$total_players = $conn->query("SELECT COUNT(DISTINCT social_media_name) as count FROM scores")->fetch_assoc()['count'];
$total_games_played = $conn->query("SELECT COUNT(id) as count FROM scores")->fetch_assoc()['count'];
$top_score_result = $conn->query("SELECT social_media_name, score FROM scores ORDER BY score DESC LIMIT 1")->fetch_assoc();
$top_score = $top_score_result ? $top_score_result['score'] : 0;
$top_player = $top_score_result ? $top_score_result['social_media_name'] : '-';


// --- Bangun Query Utama untuk Tabel Skor ---
$sql = "SELECT id, game_name, social_media_name, social_media_type, score, play_timestamp FROM scores";
$params = [];
$types = "";

if ($filter_game !== 'all') {
    $sql .= " WHERE game_name = ?";
    $params[] = $filter_game;
    $types .= "s";
}

$order_column = 'play_timestamp';
if ($sort_by === 'score') $order_column = 'score';

$order_direction = (strtoupper($order) === 'ASC') ? 'ASC' : 'DESC';
$sql .= " ORDER BY $order_column $order_direction";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$games_result = $conn->query("SELECT DISTINCT game_name FROM scores ORDER BY game_name ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <a href="dashboard.php" class="active">Dashboard</a>
        </nav>
        <div class="logout-section">
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
    </div>

    <main class="main-content">
        <header class="dashboard-header">
            <div>
                <h1>Dashboard Skor</h1>
                <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>!</p>
            </div>
        </header>

        <div class="stat-cards-container">
            <div class="stat-card">
                <div class="icon-wrapper players"><i class='bx bxs-user-account'></i></div>
                <div class="info">
                    <h3><?php echo $total_players; ?></h3>
                    <p>Total Pemain Unik</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper games"><i class='bx bxs-game'></i></div>
                <div class="info">
                    <h3><?php echo $total_games_played; ?></h3>
                    <p>Total Sesi Bermain</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper highscore"><i class='bx bxs-trophy'></i></div>
                <div class="info">
                    <h3><?php echo number_format($top_score); ?></h3>
                    <p>Skor Tertinggi (<?php echo htmlspecialchars($top_player); ?>)</p>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3>Skor Terbaru</h3>
                <form method="GET" action="dashboard.php" class="filter-form">
                    <div class="filter-wrapper">
                        <input type="hidden" name="sort_by" value="<?php echo htmlspecialchars($sort_by); ?>">
                        <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
                        <select name="filter_game" onchange="this.form.submit()">
                            <option value="all">Semua Game</option>
                            <?php while($game = $games_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($game['game_name']); ?>" <?php echo ($filter_game === $game['game_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($game['game_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </form>
            </div>

            <form action="delete_scores.php" method="POST" id="scores-form">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>Game</th>
                            <th>Jenis Sosmed</th>
                            <th>Nama Pemain</th>
                            <th>
                                <a href="?sort_by=score&order=<?php echo ($sort_by === 'score') ? $next_order : 'desc'; ?>&filter_game=<?php echo htmlspecialchars($filter_game); ?>">
                                    Skor <?php if ($sort_by === 'score') echo ($order === 'desc') ? '&#9660;' : '&#9650;'; ?>
                                </a>
                            </th>
                            <th>
                                 <a href="?sort_by=latest&order=<?php echo ($sort_by === 'latest') ? $next_order : 'desc'; ?>&filter_game=<?php echo htmlspecialchars($filter_game); ?>">
                                    Waktu Bermain <?php if ($sort_by === 'latest') echo ($order === 'desc') ? '&#9660;' : '&#9650;'; ?>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" name="score_ids[]" value="<?php echo $row['id']; ?>" class="score-checkbox"></td>
                                <td><span class="game-badge"><?php echo htmlspecialchars($row['game_name']); ?></span></td>
                                <td>
                                    <?php 
                                        $type = strtolower($row['social_media_type']);
                                        if ($type === 'instagram') echo "<i class='bx bxl-instagram'></i> ";
                                        if ($type === 'facebook') echo "<i class='bx bxl-facebook-circle'></i> ";
                                        if ($type === 'tiktok') echo "<i class='bx bxl-tiktok'></i> ";
                                        echo htmlspecialchars($row['social_media_type']);
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['social_media_name']); ?></td>
                                <td class="score-cell"><?php echo number_format($row['score']); ?></td>
                                <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($row['play_timestamp']))); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="no-data">Tidak ada data skor yang ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if ($result->num_rows > 0): ?>
                <div class="action-buttons">
                    <button type="submit" class="delete-button" onclick="return confirm('Apakah Anda yakin ingin menghapus skor yang ditandai?');">Hapus yang Ditandai</button>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('select-all').addEventListener('change', function(e) {
            document.querySelectorAll('.score-checkbox').forEach(c => c.checked = e.target.checked);
        });
    </script>
</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>