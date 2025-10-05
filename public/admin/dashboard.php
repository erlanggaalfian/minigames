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

// Menentukan arah urutan berikutnya untuk link di header tabel
$next_order = ($order === 'desc') ? 'asc' : 'desc';

// --- Bangun Query Utama ---
$sql = "SELECT id, game_name, social_media_name, score, play_timestamp FROM scores";
$params = [];
$types = "";

if ($filter_game !== 'all') {
    $sql .= " WHERE game_name = ?";
    $params[] = $filter_game;
    $types .= "s";
}

// Menentukan kolom untuk diurutkan
$order_column = 'play_timestamp';
if ($sort_by === 'score') {
    $order_column = 'score';
}

// Memastikan arah urutan valid untuk keamanan
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

    <div class="main-content">
        <header class="dashboard-header">
            <h1>Dashboard Skor Pemain</h1>
            <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>!</p>
        </header>

        <main class="dashboard-container">
            <div class="table-container">
                <div class="table-header">
                    <form method="GET" action="dashboard.php" class="filter-form">
                        <div class="filter-wrapper">
                            <input type="hidden" name="sort_by" value="<?php echo htmlspecialchars($sort_by); ?>">
                            <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
                            <select name="filter_game" onchange="this.form.submit()">
                                <option value="all">Tampilkan Semua Game</option>
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
                                    <td><?php echo htmlspecialchars($row['social_media_name']); ?></td>
                                    <td class="score-cell"><?php echo number_format($row['score']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($row['play_timestamp']))); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="no-data">Tidak ada data skor yang ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="action-buttons">
                        <button type="submit" class="delete-button" onclick="return confirm('Apakah Anda yakin ingin menghapus skor yang ditandai?');">Hapus yang Ditandai</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

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

