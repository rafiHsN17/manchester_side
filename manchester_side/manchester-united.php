<?php
session_start();

// Database configuration
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

// Helper functions
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

// Get MU data
$articles = [];
$matches = [];
$stats = [
    'total_articles' => 0,
    'news_count' => 0,
    'transfer_count' => 0,
    'injury_count' => 0
];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE team = 'manchester-united' ORDER BY created_at DESC");
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("SELECT * FROM matches WHERE (home_team LIKE '%United%' OR away_team LIKE '%United%') ORDER BY match_date ASC LIMIT 6");
        $stmt->execute();
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats['total_articles'] = count($articles);
        $stats['news_count'] = count(array_filter($articles, function($article) {
            return $article['category'] == 'news';
        }));
        $stats['transfer_count'] = count(array_filter($articles, function($article) {
            return $article['category'] == 'transfer';
        }));
        $stats['injury_count'] = count(array_filter($articles, function($article) {
            return $article['category'] == 'injury';
        }));
    } catch(PDOException $e) {
    }
}

// Sample data if database is empty
if (empty($articles)) {
    $articles = [
        ['id' => 1, 'title' => 'Rashford Cetak Gol Kemenangan di Injury Time', 'content' => 'Marcus Rashford menjadi pahlawan Manchester United dengan gol kemenangan di menit-menit akhir melawan Chelsea di Old Trafford.', 'team' => 'manchester-united', 'category' => 'news', 'created_at' => date('Y-m-d H:i:s')],
        ['id' => 3, 'title' => 'Bruno Fernandes Cetak Gol Spektakuler', 'content' => 'Kapten Manchester United Bruno Fernandes mencetak gol spektakuler dari luar kotak penalti.', 'team' => 'manchester-united', 'category' => 'news', 'created_at' => date('Y-m-d H:i:s')],
    ];
    $stats = ['total_articles' => 2, 'news_count' => 2, 'transfer_count' => 0, 'injury_count' => 0];
}

if (empty($matches)) {
    $matches = [
        ['home_team' => 'Manchester United', 'away_team' => 'Liverpool', 'match_date' => '2024-02-01', 'match_time' => '16:30:00', 'competition' => 'Premier League', 'venue' => 'Old Trafford', 'status' => 'upcoming'],
    ];
}

$team_stats = [
    'goals_scored' => 38,
    'goals_conceded' => 32,
    'clean_sheets' => 8,
    'possession_avg' => 54,
    'shots_per_game' => 14.5,
    'pass_accuracy' => 82
];

$key_achievements = [
    ['year' => '2023', 'title' => 'Carabao Cup Winner'],
    ['year' => '2017', 'title' => 'UEFA Europa League Winner'],
    ['year' => '2016', 'title' => 'FA Cup Winner'],
    ['year' => '2013', 'title' => 'Premier League Champion (20th Title)'],
    ['year' => '2008', 'title' => 'UEFA Champions League Winner'],
    ['year' => '1999', 'title' => 'Treble Winner'],
];

$history_summary = "Manchester United didirikan pada tahun 1878 sebagai Newton Heath LYR F.C. oleh pekerja Lancashire and Yorkshire Railway. Pada tahun 1902, klub mengalami kebangkrutan dan dibeli oleh J.H. Davies, yang mengganti nama menjadi Manchester United. Era keemasan pertama dimulai dengan Sir Matt Busby (1945-1969) yang membangun 'Busby Babes' dan membawa gelar European Cup pertama tahun 1968. Era dominasi modern dimulai dengan Sir Alex Ferguson (1986-2013) yang membawa 13 gelar Premier League dan Treble historis tahun 1999.";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manchester United - Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/team-page.css">
    <style>
        /* Dropdown Menu Styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-toggle {
            color: var(--light);
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .dropdown-toggle:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: rgba(15, 23, 42, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            min-width: 250px;
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-menu a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: var(--light);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
        }
        
        .dropdown-menu a:hover {
            background: rgba(255,255,255,0.1);
            padding-left: 2rem;
        }
        
        .dropdown-menu a.mu {
            border-left: 3px solid var(--united-red);
        }
        
        .dropdown-menu a.city {
            border-left: 3px solid var(--city-blue);
        }
        
        .dropdown-menu a.h2h {
            border-left: 3px solid #FBB024;
        }
    </style>
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

                <!-- Dropdown Berita -->
                <div class="dropdown">
                    <span class="dropdown-toggle">
                        <i class="fas fa-newspaper"></i> Berita <i class="fas fa-chevron-down"></i>
                    </span>
                    <div class="dropdown-menu">
                        <a href="/manchester_side/pages/injury-news.php" class="mu">🏥 Cedera</a>
                        <a href="/manchester_side/pages/transfers.php" class="city">🔄 Transfer</a>
                    </div>
                </div>
                
                <?php if(isAdminLoggedIn()): ?>
                    <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin Panel</a>
                    <a href="admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="admin/login.php"><i class="fas fa-sign-in-alt"></i> Admin Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Team Hero - NO STATS -->
    <section class="team-hero">
        <div class="container">
            <div class="team-badge-large">
                <i class="fas fa-fire"></i>
            </div>
            <h1>Manchester United</h1>
            <p class="team-nickname">The Red Devils • Setan Merah</p>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <!-- Matches Section -->
        <?php if (!empty($matches)): ?>
        <section class="matches-section">
            <h2 class="section-title"><i class="fas fa-calendar-alt"></i> Jadwal Pertandingan</h2>
            <div class="matches-table">
                <?php foreach ($matches as $match): ?>
                <div class="match-row <?php echo $match['status']; ?>">
                    <div class="match-date">
                        <span class="date"><?php echo formatDate($match['match_date']); ?></span>
                        <span class="time"><?php echo $match['match_time']; ?></span>
                    </div>
                    <div class="match-fixture">
                        <span class="home-team <?php echo strpos($match['home_team'], 'United') !== false ? 'current-team' : ''; ?>">
                            <?php echo $match['home_team']; ?>
                        </span>
                        <span class="vs">vs</span>
                        <span class="away-team <?php echo strpos($match['away_team'], 'United') !== false ? 'current-team' : ''; ?>">
                            <?php echo $match['away_team']; ?>
                        </span>
                    </div>
                    <div class="match-competition">
                        <span class="competition"><?php echo $match['competition']; ?></span>
                        <span class="venue"><i class="fas fa-stadium"></i> <?php echo $match['venue']; ?></span>
                    </div>
                    <div class="match-result">
                        <?php if ($match['status'] == 'upcoming'): ?>
                            <span class="upcoming-badge">Akan Datang</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- News Section -->
        <section class="team-news">
            <h2 class="section-title"><i class="fas fa-newspaper"></i> Berita Manchester United</h2>
            
            <div class="news-grid">
                <?php foreach ($articles as $article): ?>
                <article class="news-card">
                    <div class="news-header">
                        <span class="category-badge">
                            <i class="fas fa-newspaper"></i>
                            <?php echo ucfirst($article['category'] ?? 'news'); ?>
                        </span>
                        <span class="news-date"><i class="far fa-clock"></i> <?php echo formatDate($article['created_at']); ?></span>
                    </div>
                    
                    <h3 class="news-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                    <p class="news-excerpt"><?php echo truncateText(htmlspecialchars($article['content']), 150); ?></p>
                    
                    <div class="news-meta">
                        <span class="read-time"><i class="far fa-eye"></i> 2.4k views</span>
                        <button class="share-btn" onclick="shareArticle('<?php echo htmlspecialchars($article['title']); ?>')">
                            <i class="fas fa-share-alt"></i> Share
                        </button>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Info Cards Section -->
        <section class="info-cards">
            <h2 class="section-title"><i class="fas fa-info-circle"></i> Informasi Klub</h2>
            
            <div class="cards-grid">
                <div class="info-card" onclick="openModal('statsModal')">
                    <i class="fas fa-chart-line"></i>
                    <h3>Statistik Tim</h3>
                    <p>Lihat statistik performa Manchester United musim ini</p>
                    <span class="btn">Lihat Detail</span>
                </div>
                
                <div class="info-card" onclick="openModal('achievementsModal')">
                    <i class="fas fa-trophy"></i>
                    <h3>Prestasi & Gelar</h3>
                    <p>Pencapaian terbesar dalam sejarah klub</p>
                    <span class="btn">Lihat Detail</span>
                </div>
                
                <div class="info-card" onclick="openModal('historyModal')">
                    <i class="fas fa-landmark"></i>
                    <h3>Sejarah Klub</h3>
                    <p>Perjalanan Manchester United dari awal hingga kini</p>
                    <span class="btn">Lihat Detail</span>
                </div>
            </div>
        </section>
    </main>

    <!-- Modals -->
    <div id="statsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-chart-line"></i> Statistik Tim Musim Ini</h2>
                <button class="close-modal" onclick="closeModal('statsModal')">&times;</button>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-value"><?php echo $team_stats['goals_scored']; ?></span>
                    <span class="stat-label">Gol Dicetak</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo $team_stats['goals_conceded']; ?></span>
                    <span class="stat-label">Gol Kemasukan</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo $team_stats['clean_sheets']; ?></span>
                    <span class="stat-label">Clean Sheets</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo $team_stats['possession_avg']; ?>%</span>
                    <span class="stat-label">Rata-rata Possesi</span>
                </div>
            </div>
        </div>
    </div>

    <div id="achievementsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-trophy"></i> Prestasi & Gelar</h2>
                <button class="close-modal" onclick="closeModal('achievementsModal')">&times;</button>
            </div>
            <div class="achievements-list">
                <?php foreach ($key_achievements as $achievement): ?>
                <div class="achievement-item">
                    <div class="achievement-year"><?php echo $achievement['year']; ?></div>
                    <div class="achievement-title"><?php echo $achievement['title']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div id="historyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-landmark"></i> Sejarah Manchester United</h2>
                <button class="close-modal" onclick="closeModal('historyModal')">&times;</button>
            </div>
            <div class="history-content">
                <?php echo $history_summary; ?>
            </div>
        </div>
    </div>

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
                    <a href="manchester-united.php"><i class="fas fa-fire"></i> Manchester United</a>
                    <a href="manchester-city.php"><i class="fas fa-bolt"></i> Manchester City</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Manchester Side. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/team-page.js"></script>
</body>
</html>
