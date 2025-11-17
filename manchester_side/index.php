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
    $pdo = null;
}

// ==================== FUNGSI HELPER ====================
function getTeamBadge($team) {
    return $team == 'manchester-united' ? '🔴' : '🔵';
}

function getTeamName($team) {
    return $team == 'manchester-united' ? 'Manchester United' : 'Manchester City';
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function truncateText($text, $length = 100) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// ==================== AMBIL DATA ====================
$articles = [];
$matches = [];
$mu_count = 0;
$city_count = 0;

if ($pdo) {
    try {
        // Get articles
        $stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 6");
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Count articles by team
        $mu_count = $pdo->query("SELECT COUNT(*) FROM articles WHERE team = 'manchester-united'")->fetchColumn();
        $city_count = $pdo->query("SELECT COUNT(*) FROM articles WHERE team = 'manchester-city'")->fetchColumn();
        
        // Get matches
        $stmt = $pdo->query("SELECT * FROM matches WHERE status = 'upcoming' ORDER BY match_date ASC LIMIT 3");
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Silent error
    }
}

// Sample data jika database kosong
if (empty($articles)) {
    $articles = [
        ['id' => 1, 'title' => 'Rashford Cetak Gol Kemenangan di Injury Time', 'content' => 'Marcus Rashford menjadi pahlawan Manchester United dengan gol kemenangan di menit-menit akhir melawan Chelsea di Old Trafford. Performa impresif Rashford membawa MU meraih 3 poin penting dalam perburuan posisi 4 besar.', 'team' => 'manchester-united', 'created_at' => date('Y-m-d H:i:s')],
        ['id' => 2, 'title' => 'Haaland Raih Hat-trick, City Hancurkan Tottenham', 'content' => 'Erling Haaland menunjukkan kelas dunia dengan mencetak hat-trick melawan Tottenham. Striker Norwegia ini semakin memantapkan diri sebagai top scorer Premier League dengan 14 gol sejauh ini.', 'team' => 'manchester-city', 'created_at' => date('Y-m-d H:i:s')],
        ['id' => 3, 'title' => 'Bruno Fernandes Cetak Gol Spektakuler dari Luar Kotak', 'content' => 'Kapten Manchester United Bruno Fernandes mencetak gol spektakuler dari luar kotak penalti yang mengantarkan timnya meraih kemenangan penting atas Aston Villa.', 'team' => 'manchester-united', 'created_at' => date('Y-m-d H:i:s')],
        ['id' => 4, 'title' => 'Kevin De Bruyne Kembali Cedera, Absen 3 Minggu', 'content' => 'Playmaker andalan Manchester City Kevin De Bruyne harus kembali absen selama 3 minggu akibat cedera hamstring yang dialaminya saat latihan.', 'team' => 'manchester-city', 'created_at' => date('Y-m-d H:i:s')]
    ];
    $mu_count = 2;
    $city_count = 2;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manchester Side - Berita Terkini MU & Manchester City</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-content">
            <div class="logo">
                <i class="fas fa-futbol"></i>
                <h1>MANCHESTER SIDE</h1>
            </div>
            <nav>
                <a href="index.php" class="active"><i class="fas fa-home"></i> Home</a>
                
                <!-- Dropdown Tim -->
                <div class="dropdown">
                    <span class="dropdown-toggle">
                        <i class="fas fa-shield-alt"></i> Tim <i class="fas fa-chevron-down"></i>
                    </span>
                    <div class="dropdown-menu">
                        <a href="manchester-united.php" class="mu">🔴 Manchester United</a>
                        <a href="manchester-city.php" class="city">🔵 Manchester City</a>
                        <a href="head-to-head.php" class="h2h">⚔️ Head to Head</a>
                    </div>
                </div>
                
                <?php if(isAdminLoggedIn()): ?>
                    <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>MANCHESTER SIDE</h1>
            <p class="hero-subtitle">Portal berita eksklusif untuk Manchester United dan Manchester City. Update terkini, analisis mendalam, dan statistik lengkap.</p>
            
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number"><?php echo count($articles); ?></span>
                    <span class="stat-label">Total Berita</span>
                </div>
                <div class="stat">
                    <span class="stat-number"><?php echo $mu_count + $city_count; ?></span>
                    <span class="stat-label">Artikel Tim</span>
                </div>
                <div class="stat">
                    <span class="stat-number"><?php echo count($matches); ?></span>
                    <span class="stat-label">Pertandingan</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <!-- Latest News -->
        <section class="latest-news">
            <h2 class="section-title"><i class="fas fa-newspaper"></i> Berita Terkini</h2>
            
            <div class="news-grid">
                <?php foreach ($articles as $article): ?>
                <article class="news-card <?php echo $article['team']; ?>">
                    <div class="news-header">
                        <span class="team-badge <?php echo $article['team'] == 'manchester-united' ? 'badge-united' : 'badge-city'; ?>">
                            <i class="fas <?php echo $article['team'] == 'manchester-united' ? 'fa-fire' : 'fa-bolt'; ?>"></i>
                            <?php echo getTeamName($article['team']); ?>
                        </span>
                        <span class="news-date"><i class="far fa-clock"></i> <?php echo formatDate($article['created_at']); ?></span>
                    </div>
                    
                    <h3 class="news-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                    <p class="news-excerpt"><?php echo truncateText(htmlspecialchars($article['content']), 120); ?></p>
                    
                    <div class="news-meta">
                        <a href="article-detail.php?id=<?php echo $article['id']; ?>" class="read-more">
                            Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Team Quick Links -->
        <section class="team-quick-links">
            <div class="team-link united">
                <h3><i class="fas fa-fire"></i> Manchester United</h3>
                <p>Dapatkan berita terkini, analisis mendalam, dan statistik lengkap tentang Setan Merah. Semua tentang MU di satu tempat.</p>
                <a href="manchester-united.php" class="btn btn-united">
                    <i class="fas fa-arrow-right"></i> Jelajahi MU
                </a>
            </div>
            
            <div class="team-link city">
                <h3><i class="fas fa-bolt"></i> Manchester City</h3>
                <p>Update terbaru tentang The Citizens, performa pemain, strategi Guardiola, dan perjalanan menuju trofi.</p>
                <a href="manchester-city.php" class="btn btn-city">
                    <i class="fas fa-arrow-right"></i> Jelajahi City
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">MANCHESTER SIDE</div>
                    <p class="footer-description">
                        Portal berita eksklusif untuk Manchester United dan Manchester City. 
                        Menyajikan berita terkini, analisis mendalam, dan statistik lengkap 
                        tentang dua raksasa Manchester.
                    </p>
                </div>
                
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="manchester-united.php"><i class="fas fa-fire"></i> Manchester United</a>
                    <a href="manchester-city.php"><i class="fas fa-bolt"></i> Manchester City</a>
                    <a href="admin/login.php"><i class="fas fa-cog"></i> Admin</a>
                </div>
                
                <div class="footer-links">
                    <h4>Teams</h4>
                    <a href="manchester-united.php"><i class="fas fa-shield-alt"></i> Manchester United</a>
                    <a href="manchester-city.php"><i class="fas fa-shield-alt"></i> Manchester City</a>
                    <a href="head-to-head.php"><i class="fas fa-trophy"></i> Head to Head</a>
                    <a href="#"><i class="fas fa-futbol"></i> Premier League</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Manchester Side. All rights reserved. | Built with <i class="fas fa-heart" style="color: var(--united-red);"></i> for Football Fans</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/index.js"></script>
</body>
</html>
