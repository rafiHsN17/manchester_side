<?php
session_start();

// ==================== KONFIGURASI DATABASE ====================
$host = 'localhost';
$dbname = 'manchester_side';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// ==================== FUNGSI HELPER ====================
function redirect($url) {
    header("Location: $url");
    exit();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function getTeamName($team) {
    return $team == 'manchester-united' ? 'Manchester United' : 'Manchester City';
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

// ==================== CEK LOGIN ====================
if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// ==================== AMBIL DATA ====================
// Statistik
$total_articles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$mu_articles = $pdo->query("SELECT COUNT(*) FROM articles WHERE team = 'manchester-united'")->fetchColumn();
$city_articles = $pdo->query("SELECT COUNT(*) FROM articles WHERE team = 'manchester-city'")->fetchColumn();
$total_matches = $pdo->query("SELECT COUNT(*) FROM matches")->fetchColumn();

// Berita terbaru
$stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 5");
$latest_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manchester Side</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">
                <h1>⚽ MANCHESTER SIDE - ADMIN</h1>
            </div>
            <nav>
                <span>Halo, <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?>!</span>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-dashboard">
            <h2>Admin Dashboard</h2>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Berita</h3>
                    <div class="stat-number"><?php echo $total_articles; ?></div>
                </div>
                <div class="stat-card united">
                    <h3>Berita MU</h3>
                    <div class="stat-number"><?php echo $mu_articles; ?></div>
                </div>
                <div class="stat-card city">
                    <h3>Berita City</h3>
                    <div class="stat-number"><?php echo $city_articles; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Jadwal Pertandingan</h3>
                    <div class="stat-number"><?php echo $total_matches; ?></div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="add-article.php" class="btn btn-primary">+ Tambah Berita</a>
                <a href="../index.php" class="btn btn-secondary">Lihat Website</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
            
            <!-- Latest Articles -->
            <div class="latest-articles">
                <h3>Berita Terbaru</h3>
                
                <?php if (empty($latest_articles)): ?>
                    <p>Belum ada berita.</p>
                <?php else: ?>
                    <div class="articles-list">
                        <?php foreach ($latest_articles as $article): ?>
                            <div class="article-item <?php echo $article['team']; ?>">
                                <h4><?php echo htmlspecialchars($article['title']); ?></h4>
                                <div class="article-meta">
                                    <span class="team-badge <?php echo $article['team']; ?>">
                                        <?php echo getTeamName($article['team']); ?>
                                    </span>
                                    <span class="date"><?php echo formatDate($article['created_at']); ?></span>
                                </div>
                                <p><?php echo substr(htmlspecialchars($article['content']), 0, 100); ?>...</p>
                                <div class="article-actions">
                                    <a href="edit-article.php?id=<?php echo $article['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="delete-article.php?id=<?php echo $article['id']; ?>" class="btn-delete" 
                                       onclick="return confirm('Yakin hapus berita ini?')">Hapus</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
