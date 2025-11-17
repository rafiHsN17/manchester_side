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

// Get injury news from database
$injury_articles = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM articles WHERE category = 'injury' ORDER BY created_at DESC");
        $injury_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Silent error
    }
}

// Sample data jika database kosong
if (empty($injury_articles)) {
    $injury_articles = [
        [
            'id' => 1,
            'title' => 'Lisandro Martinez Absen 6 Minggu',
            'content' => 'Bek tengah Manchester United, Lisandro Martinez, dipastikan absen selama 6 minggu akibat cedera hamstring yang dialaminya saat pertandingan melawan Arsenal.',
            'team' => 'manchester-united',
            'created_at' => date('Y-m-d H:i:s'),
            'image_url' => ''
        ],
        [
            'id' => 2,
            'title' => 'Kevin De Bruyne Kembali Cedera',
            'content' => 'Playmaker Manchester City Kevin De Bruyne harus kembali absen selama 3 minggu akibat cedera hamstring yang dialaminya saat latihan.',
            'team' => 'manchester-city',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'image_url' => ''
        ],
        [
            'id' => 3,
            'title' => 'Luke Shaw Menjalani Operasi',
            'content' => 'Bek kiri Manchester United Luke Shaw akan menjalani operasi untuk mengatasi cedera yang sudah lama mengganggunya. Diperkirakan absen hingga akhir musim.',
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
    <title>Injury News - Update Cedera Pemain | Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/injury-news.css">
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
                <a href="injury-news.php" class="active"><i class="fas fa-medkit"></i> Injury</a>
                <?php if(isAdminLoggedIn()): ?>
                    <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="injury-hero">
        <div class="container">
            <div class="hero-content">
                <i class="fas fa-medkit hero-icon"></i>
                <h1 class="page-title">INJURY NEWS</h1>
                <p class="page-subtitle">Update Terkini Cedera Pemain Manchester United & Manchester City</p>
            </div>
        </div>
    </section>

    <!-- Injury List -->
    <section class="injury-list">
        <div class="container">
            <?php if (empty($injury_articles)): ?>
                <div class="no-data">
                    <i class="fas fa-info-circle"></i>
                    <p>Belum ada update cedera terbaru</p>
                </div>
            <?php else: ?>
                <div class="injuries-grid">
                    <?php foreach ($injury_articles as $article): ?>
                    <article class="injury-card <?php echo $article['team']; ?>">
                        <div class="injury-header">
                            <span class="team-badge <?php echo $article['team']; ?>">
                                <i class="fas <?php echo $article['team'] == 'manchester-united' ? 'fa-fire' : 'fa-bolt'; ?>"></i>
                                <?php echo $article['team'] == 'manchester-united' ? 'Manchester United' : 'Manchester City'; ?>
                            </span>
                            <span class="injury-date">
                                <i class="far fa-clock"></i>
                                <?php echo formatDate($article['created_at']); ?>
                            </span>
                        </div>

                        <?php if (!empty($article['image_url'])): ?>
                        <div class="injury-image">
                            <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        </div>
                        <?php endif; ?>

                        <div class="injury-content">
                            <h3 class="injury-title">
                                <i class="fas fa-medkit"></i>
                                <?php echo htmlspecialchars($article['title']); ?>
                            </h3>
                            <p class="injury-description">
                                <?php echo htmlspecialchars(substr($article['content'], 0, 150)) . '...'; ?>
                            </p>
                        </div>

                        <div class="injury-footer">
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
                <h3>Tentang Injury News</h3>
                <p>Halaman ini menyediakan update terkini mengenai cedera pemain dari Manchester United dan Manchester City. Informasi meliputi jenis cedera, estimasi waktu pemulihan, dan status terkini pemain.</p>
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
                    <a href="transfer.php"><i class="fas fa-exchange-alt"></i> Transfer</a>
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
