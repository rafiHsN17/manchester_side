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

function getTeamName($team) {
    return $team == 'manchester-united' ? 'Manchester United' : 'Manchester City';
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
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
$stmt = $pdo->prepare("SELECT * FROM articles WHERE team = ? AND id != ? ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$article['team'], $article_id]);
$related_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> | Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/article-detail.css">
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
                <a href="manchester-united.php"><i class="fas fa-fire"></i> Manchester United</a>
                <a href="manchester-city.php"><i class="fas fa-bolt"></i> Manchester City</a>
                <?php if(isAdminLoggedIn()): ?>
                    <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Article Content -->
    <main class="container">
        <article class="article-detail">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span>/</span>
                <a href="<?php echo $article['team']; ?>.php"><?php echo getTeamName($article['team']); ?></a>
                <span>/</span>
                <span><?php echo htmlspecialchars($article['title']); ?></span>
            </div>

            <!-- Article Header -->
            <header class="article-header">
                <div class="article-meta">
                    <span class="team-badge <?php echo $article['team']; ?>">
                        <i class="fas <?php echo $article['team'] == 'manchester-united' ? 'fa-fire' : 'fa-bolt'; ?>"></i>
                        <?php echo getTeamName($article['team']); ?>
                    </span>
                    <span class="category-badge">
                        <?php echo ucfirst($article['category'] ?? 'news'); ?>
                    </span>
                    <span class="date">
                        <i class="far fa-clock"></i> <?php echo formatDate($article['created_at']); ?>
                    </span>
                </div>
                
                <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
            </header>

            <!-- Article Image -->
            <?php if (!empty($article['image_url'])): ?>
            <div class="article-image">
                <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
            </div>
            <?php endif; ?>

            <!-- Article Video -->
            <?php if (!empty($article['video_url'])): ?>
            <div class="article-video">
                <?php
                // Check if it's a YouTube URL
                if (strpos($article['video_url'], 'youtube.com') !== false || strpos($article['video_url'], 'youtu.be') !== false) {
                    // Extract YouTube video ID
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $article['video_url'], $matches);
                    $video_id = $matches[1] ?? '';
                    if ($video_id) {
                        echo '<iframe width="100%" height="500" src="https://www.youtube.com/embed/' . htmlspecialchars($video_id) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                    }
                } else {
                    // Local video file
                    echo '<video controls width="100%">';
                    echo '<source src="' . htmlspecialchars($article['video_url']) . '" type="video/mp4">';
                    echo 'Your browser does not support the video tag.';
                    echo '</video>';
                }
                ?>
            </div>
            <?php endif; ?>

            <!-- Article Body -->
            <div class="article-body">
                <?php echo nl2br(htmlspecialchars($article['content'])); ?>
            </div>

            <!-- Article Footer -->
            <footer class="article-footer">
                <div class="share-section">
                    <h3>Bagikan Artikel</h3>
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
                        <button class="share-btn copy" onclick="copyLink()">
                            <i class="fas fa-link"></i> Copy Link
                        </button>
                    </div>
                </div>
            </footer>
        </article>

        <!-- Related Articles -->
        <?php if (!empty($related_articles)): ?>
        <section class="related-articles">
            <h2 class="section-title">Berita Terkait</h2>
            <div class="related-grid">
                <?php foreach ($related_articles as $related): ?>
                <article class="related-card">
                    <?php if (!empty($related['image_url'])): ?>
                    <div class="related-image">
                        <img src="<?php echo htmlspecialchars($related['image_url']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                    </div>
                    <?php endif; ?>
                    <div class="related-content">
                        <span class="related-date"><?php echo formatDate($related['created_at']); ?></span>
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
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Manchester Side. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/article-detail.js"></script>
</body>
</html>
