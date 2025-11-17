<?php
session_start();

// Database connection
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

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

// Get transfer news from database
$transfer_articles = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM articles WHERE category = 'transfer' ORDER BY created_at DESC");
        $transfer_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Silent error
    }
}

// Sample data jika database kosong
if (empty($transfer_articles)) {
    $transfer_articles = [
        [
            'id' => 1,
            'title' => 'Manchester United Dekati Kesepakatan dengan Striker Bintang',
            'content' => 'Manchester United dilaporkan semakin dekat dengan kesepakatan transfer untuk mendatangkan striker bintang Serie A. Negosiasi sudah memasuki tahap akhir dengan nilai transfer mencapai €80 juta.',
            'team' => 'manchester-united',
            'created_at' => date('Y-m-d H:i:s'),
            'image_url' => ''
        ],
        [
            'id' => 2,
            'title' => 'Manchester City Resmi Datangkan Gelandang Muda Brasil',
            'content' => 'Manchester City mengumumkan transfer gelandang muda Brasil dari Palmeiras. Pemain berusia 20 tahun ini dikontrak dengan durasi 5 tahun.',
            'team' => 'manchester-city',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'image_url' => ''
        ],
        [
            'id' => 3,
            'title' => 'Bek Manchester United Diminati Klub Spanyol',
            'content' => 'Salah satu bek Manchester United dilaporkan menjadi target transfer klub besar La Liga. Tawaran pertama senilai €40 juta sudah diajukan.',
            'team' => 'manchester-united',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'image_url' => ''
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer News - Berita Transfer Terkini | Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/transfer.css">
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
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="manchester-united.php"><i class="fas fa-fire"></i> Manchester United</a>
                <a href="manchester-city.php"><i class="fas fa-bolt"></i> Manchester City</a>
                <a href="transfer.php" class="active"><i class="fas fa-exchange-alt"></i> Transfer</a>
                <?php if(isAdminLoggedIn()): ?>
                    <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="transfer-hero">
        <div class="container">
            <div class="hero-content">
                <i class="fas fa-exchange-alt hero-icon"></i>
                <h1 class="page-title">TRANSFER NEWS</h1>
                <p class="page-subtitle">Berita Transfer & Rumor Terkini Manchester United & Manchester City</p>
            </div>
        </div>
    </section>

    <!-- Transfer List -->
    <section class="transfer-list">
        <div class="container">
            <?php if (empty($transfer_articles)): ?>
                <div class="no-data">
                    <i class="fas fa-info-circle"></i>
                    <p>Belum ada berita transfer terbaru</p>
                </div>
            <?php else: ?>
                <div class="transfers-grid">
                    <?php foreach ($transfer_articles as $article): ?>
                    <article class="transfer-card <?php echo $article['team']; ?>">
                        <div class="transfer-badge">
                            <i class="fas fa-exchange-alt"></i>
                        </div>

                        <div class="transfer-header">
                            <span class="team-badge <?php echo $article['team']; ?>">
                                <i class="fas <?php echo $article['team'] == 'manchester-united' ? 'fa-fire' : 'fa-bolt'; ?>"></i>
                                <?php echo $article['team'] == 'manchester-united' ? 'Manchester United' : 'Manchester City'; ?>
                            </span>
                            <span class="transfer-date">
                                <i class="far fa-clock"></i>
                                <?php echo formatDate($article['created_at']); ?>
                            </span>
                        </div>

                        <?php if (!empty($article['image_url'])): ?>
                        <div class="transfer-image">
                            <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        </div>
                        <?php endif; ?>

                        <div class="transfer-content">
                            <h3 class="transfer-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p class="transfer-description">
                                <?php echo htmlspecialchars(substr($article['content'], 0, 150)) . '...'; ?>
                            </p>
                        </div>

                        <div class="transfer-footer">
                            <a href="article-detail.php?id=<?php echo $article['id']; ?>" class="read-more-btn">
                                Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="container">
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <h3>Tentang Transfer News</h3>
                <p>Halaman ini menyediakan berita transfer terkini, rumor, dan update pasar transfer untuk Manchester United dan Manchester City. Dapatkan informasi terbaru tentang pemain masuk, keluar, dan negosiasi transfer.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">MANCHESTER SIDE</div>
                    <p class="footer-description">Portal berita eksklusif untuk Manchester United dan Manchester City.</p>
                </div>
                
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="matches.php"><i class="fas fa-calendar"></i> Jadwal</a>
                    <a href="injury-news.php"><i class="fas fa-medkit"></i> Injury</a>
                    <a href="videos.php"><i class="fas fa-video"></i> Videos</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Manchester Side. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
