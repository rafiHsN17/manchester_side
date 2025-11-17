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

function getYouTubeID($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
    return $matches[1] ?? '';
}

// Get articles with videos from database
$video_articles = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM articles WHERE video_url IS NOT NULL AND video_url != '' ORDER BY created_at DESC");
        $video_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Silent error
    }
}

// Sample data jika database kosong
if (empty($video_articles)) {
    $video_articles = [
        [
            'id' => 1,
            'title' => 'Highlights: Manchester United 2-1 Liverpool',
            'content' => 'Saksikan highlight pertandingan seru Manchester United melawan Liverpool yang berakhir dengan kemenangan 2-1 untuk The Red Devils.',
            'team' => 'manchester-united',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'created_at' => date('Y-m-d H:i:s'),
            'image_url' => ''
        ],
        [
            'id' => 2,
            'title' => 'Haaland Hat-trick vs Tottenham',
            'content' => 'Erling Haaland mencetak hat-trick spektakuler melawan Tottenham. Tonton semua golnya di sini!',
            'team' => 'manchester-city',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'image_url' => ''
        ],
        [
            'id' => 3,
            'title' => 'Bruno Fernandes Best Skills & Goals',
            'content' => 'Kompilasi skill dan gol terbaik dari kapten Manchester United, Bruno Fernandes.',
            'team' => 'manchester-united',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
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
    <title>Videos - Highlights & Kompilasi | Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/videos.css">
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
                <a href="videos.php" class="active"><i class="fas fa-video"></i> Videos</a>
                <?php if(isAdminLoggedIn()): ?>
                    <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="videos-hero">
        <div class="container">
            <div class="hero-content">
                <i class="fas fa-play-circle hero-icon"></i>
                <h1 class="page-title">VIDEO GALLERY</h1>
                <p class="page-subtitle">Highlights, Goals & Kompilasi Terbaik Manchester United & Manchester City</p>
            </div>
        </div>
    </section>

    <!-- Videos Grid -->
    <section class="videos-section">
        <div class="container">
            <?php if (empty($video_articles)): ?>
                <div class="no-data">
                    <i class="fas fa-info-circle"></i>
                    <p>Belum ada video tersedia</p>
                </div>
            <?php else: ?>
                <div class="videos-grid">
                    <?php foreach ($video_articles as $article): ?>
                    <article class="video-card <?php echo $article['team']; ?>">
                        <div class="video-thumbnail">
                            <?php 
                            $video_id = getYouTubeID($article['video_url']);
                            if ($video_id): 
                            ?>
                                <img src="https://img.youtube.com/vi/<?php echo $video_id; ?>/maxresdefault.jpg" 
                                     alt="<?php echo htmlspecialchars($article['title']); ?>"
                                     onerror="this.src='https://img.youtube.com/vi/<?php echo $video_id; ?>/hqdefault.jpg'">
                                <div class="play-overlay">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                            <?php elseif (!empty($article['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($article['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($article['title']); ?>">
                                <div class="play-overlay">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                            <?php else: ?>
                                <div class="no-thumbnail">
                                    <i class="fas fa-video"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="video-content">
                            <div class="video-header">
                                <span class="team-badge <?php echo $article['team']; ?>">
                                    <i class="fas <?php echo $article['team'] == 'manchester-united' ? 'fa-fire' : 'fa-bolt'; ?>"></i>
                                    <?php echo $article['team'] == 'manchester-united' ? 'MU' : 'City'; ?>
                                </span>
                                <span class="video-date">
                                    <i class="far fa-clock"></i>
                                    <?php echo formatDate($article['created_at']); ?>
                                </span>
                            </div>

                            <h3 class="video-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p class="video-description">
                                <?php echo htmlspecialchars(substr($article['content'], 0, 100)) . '...'; ?>
                            </p>

                            <a href="article-detail.php?id=<?php echo $article['id']; ?>" class="watch-btn">
                                <i class="fas fa-play"></i> Tonton Video
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
                    <a href="transfer.php"><i class="fas fa-exchange-alt"></i> Transfer</a>
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
