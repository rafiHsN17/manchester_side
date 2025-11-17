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
    die("Database connection failed");
}

// Helper functions
function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function formatDateLong($date) {
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    $timestamp = strtotime($date);
    $day = $days[date('w', $timestamp)];
    $date_num = date('d', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp);
    $time = date('H:i', $timestamp);
    
    return "$day, $date_num $month $year - $time WIB";
}

function getTeamName($team) {
    return $team == 'manchester-united' ? 'Manchester United' : 'Manchester City';
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function getReadingTime($content) {
    $word_count = str_word_count(strip_tags($content));
    $minutes = ceil($word_count / 200);
    return $minutes;
}

// Get article ID
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($article_id <= 0) {
    header('Location: index.php');
    exit();
}

// Fetch article
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: index.php');
    exit();
}

// Get related articles (same team, different article)
$stmt = $pdo->prepare("SELECT * FROM articles WHERE team = ? AND id != ? ORDER BY created_at DESC LIMIT 4");
$stmt->execute([$article['team'], $article_id]);
$related_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get popular articles (latest 5)
$stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 5");
$popular_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$reading_time = getReadingTime($article['content']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> | Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/article-detail.css">
</head>
<body class="<?php echo $article['team']; ?>">
    <!-- Header -->
    <header>
        <div class="container header-content">
            <div class="logo">
                <i class="fas fa-futbol"></i>
                <h1>MANCHESTER SIDE</h1>
            </div>
            <nav>
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                
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

    <!-- Article Content -->
    <main class="article-container">
        <div class="content-wrapper">
            <!-- Left Column: Article -->
            <article class="article-main">
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="<?php echo $article['team']; ?>.php"><?php echo getTeamName($article['team']); ?></a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Artikel</span>
                </div>

                <!-- Article Header -->
                <header class="article-header">
                    <div class="article-category">
                        <span class="team-badge <?php echo $article['team']; ?>">
                            <i class="fas <?php echo $article['team'] == 'manchester-united' ? 'fa-fire' : 'fa-bolt'; ?>"></i>
                            <?php echo getTeamName($article['team']); ?>
                        </span>
                        <span class="category-badge">
                            <?php 
                                $categories = [
                                    'news' => '📰 Berita',
                                    'transfer' => '🔄 Transfer',
                                    'injury' => '🏥 Cedera',
                                    'match' => '⚽ Pertandingan',
                                    'analysis' => '📊 Analisis'
                                ];
                                echo $categories[$article['category']] ?? '📰 Berita';
                            ?>
                        </span>
                    </div>
                    
                    <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                    
                    <div class="article-meta-top">
                        <div class="meta-item">
                            <i class="far fa-calendar"></i>
                            <span><?php echo formatDateLong($article['created_at']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="far fa-clock"></i>
                            <span><?php echo $reading_time; ?> menit baca</span>
                        </div>
                        <div class="meta-item">
                            <i class="far fa-eye"></i>
                            <span><?php echo rand(1000, 9999); ?> views</span>
                        </div>
                    </div>
                </header>

                <!-- Article Image -->
                <?php if (!empty($article['image_url'])): ?>
                <figure class="article-featured-image">
                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                    <figcaption><?php echo htmlspecialchars($article['title']); ?></figcaption>
                </figure>
                <?php endif; ?>

                <!-- Article Body -->
                <div class="article-body">
                    <?php 
                    $paragraphs = explode("\n", $article['content']);
                    foreach ($paragraphs as $index => $paragraph) {
                        if (trim($paragraph)) {
                            echo '<p>' . nl2br(htmlspecialchars($paragraph)) . '</p>';
                            
                            // Add ad placeholder after 2nd paragraph
                            if ($index == 1) {
                                echo '<div class="article-ad">
                                    <span class="ad-label">ADVERTISEMENT</span>
                                    <div class="ad-placeholder">
                                        <i class="fas fa-ad"></i>
                                        <p>Space for Advertisement</p>
                                    </div>
                                </div>';
                            }
                        }
                    }
                    ?>
                </div>

                <!-- Article Tags -->
                <div class="article-tags">
                    <i class="fas fa-tags"></i>
                    <span class="tag"><?php echo getTeamName($article['team']); ?></span>
                    <span class="tag">Premier League</span>
                    <span class="tag">Football</span>
                    <span class="tag">Berita Bola</span>
                </div>

                <!-- Social Share -->
                <div class="article-share">
                    <h3><i class="fas fa-share-alt"></i> Bagikan Artikel</h3>
                    <div class="share-buttons">
                        <button class="share-btn facebook" onclick="shareToFacebook()">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </button>
                        <button class="share-btn twitter" onclick="shareToTwitter()">
                            <i class="fab fa-twitter"></i> Twitter
                        </button>
                        <button class="share-btn whatsapp" onclick="shareToWhatsApp()">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </button>
                        <button class="share-btn telegram" onclick="shareToTelegram()">
                            <i class="fab fa-telegram"></i> Telegram
                        </button>
                        <button class="share-btn copy" onclick="copyLink()">
                            <i class="fas fa-link"></i> Copy
                        </button>
                    </div>
                </div>

                <!-- Related Articles -->
                <?php if (!empty($related_articles)): ?>
                <section class="related-section">
                    <h2 class="section-title"><i class="fas fa-newspaper"></i> Berita Terkait</h2>
                    <div class="related-grid">
                        <?php foreach ($related_articles as $related): ?>
                        <article class="related-card">
                            <?php if (!empty($related['image_url'])): ?>
                            <div class="related-image">
                                <img src="<?php echo htmlspecialchars($related['image_url']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                <span class="related-overlay"><?php echo getTeamName($related['team']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="related-content">
                                <span class="related-date"><i class="far fa-clock"></i> <?php echo formatDate($related['created_at']); ?></span>
                                <h3 class="related-title">
                                    <a href="article-detail.php?id=<?php echo $related['id']; ?>">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </a>
                                </h3>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            </article>

            <!-- Right Sidebar -->
            <aside class="sidebar">
                <!-- Popular Articles -->
                <div class="sidebar-widget">
                    <h3 class="widget-title"><i class="fas fa-fire"></i> Berita Populer</h3>
                    <div class="popular-list">
                        <?php foreach ($popular_articles as $index => $popular): ?>
                        <article class="popular-item">
                            <div class="popular-number"><?php echo $index + 1; ?></div>
                            <div class="popular-content">
                                <a href="article-detail.php?id=<?php echo $popular['id']; ?>" class="popular-title">
                                    <?php echo htmlspecialchars($popular['title']); ?>
                                </a>
                                <div class="popular-meta">
                                    <span class="popular-team"><?php echo getTeamName($popular['team']); ?></span>
                                    <span class="popular-date"><?php echo formatDate($popular['created_at']); ?></span>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Team Info Widget -->
                <div class="sidebar-widget team-widget <?php echo $article['team']; ?>">
                    <h3 class="widget-title">
                        <i class="fas <?php echo $article['team'] == 'manchester-united' ? 'fa-fire' : 'fa-bolt'; ?>"></i>
                        <?php echo getTeamName($article['team']); ?>
                    </h3>
                    <div class="team-info">
                        <p>Ikuti berita terbaru dan update terkini dari <?php echo getTeamName($article['team']); ?>.</p>
                        <a href="<?php echo $article['team']; ?>.php" class="widget-btn">
                            <i class="fas fa-arrow-right"></i> Lihat Semua Berita
                        </a>
                    </div>
                </div>

                <!-- Ad Widget -->
                <div class="sidebar-widget ad-widget">
                    <span class="ad-label">ADVERTISEMENT</span>
                    <div class="ad-placeholder">
                        <i class="fas fa-ad"></i>
                        <p>300x250</p>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">MANCHESTER SIDE</div>
                    <p class="footer-description">
                        Portal berita eksklusif untuk Manchester United dan Manchester City.
                    </p>
                </div>
                
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="manchester-united.php"><i class="fas fa-fire"></i> Manchester United</a>
                    <a href="manchester-city.php"><i class="fas fa-bolt"></i> Manchester City</a>
                </div>

                <div class="footer-links">
                    <h4>Follow Us</h4>
                    <a href="#"><i class="fab fa-facebook"></i> Facebook</a>
                    <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                    <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Manchester Side. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/article-detail.js"></script>
</body>
</html>
