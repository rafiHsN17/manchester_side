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

// ==================== AMBIL DATA MU ====================
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
        // Get MU articles
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE team = 'manchester-united' ORDER BY created_at DESC");
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get MU matches
        $stmt = $pdo->prepare("SELECT * FROM matches WHERE (home_team LIKE '%United%' OR away_team LIKE '%United%') ORDER BY match_date ASC LIMIT 6");
        $stmt->execute();
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Count articles by category
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
        // Silent error
    }
}

// Sample data jika database kosong
if (empty($articles)) {
    $articles = [
        ['id' => 1, 'title' => 'Rashford Cetak Gol Kemenangan di Injury Time', 'content' => 'Marcus Rashford menjadi pahlawan Manchester United dengan gol kemenangan di menit-menit akhir melawan Chelsea di Old Trafford. Performa impresif Rashford membawa MU meraih 3 poin penting dalam perburuan posisi 4 besar.', 'team' => 'manchester-united', 'category' => 'news', 'created_at' => date('Y-m-d H:i:s')],
        ['id' => 3, 'title' => 'Bruno Fernandes Cetak Gol Spektakuler dari Luar Kotak', 'content' => 'Kapten Manchester United Bruno Fernandes mencetak gol spektakuler dari luar kotak penalti yang mengantarkan timnya meraih kemenangan penting atas Aston Villa.', 'team' => 'manchester-united', 'category' => 'news', 'created_at' => date('Y-m-d H:i:s')],
        ['id' => 5, 'title' => 'MU Incar Striker Muda dari Bundesliga', 'content' => 'Manchester United dikabarkan sedang memantau striker muda dari Bundesliga sebagai bagian dari rencana rekrutmen musim panas mendatang.', 'team' => 'manchester-united', 'category' => 'transfer', 'created_at' => date('Y-m-d H:i:s')]
    ];
    $stats = ['total_articles' => 3, 'news_count' => 2, 'transfer_count' => 1, 'injury_count' => 0];
}

if (empty($matches)) {
    $matches = [
        ['home_team' => 'Manchester United', 'away_team' => 'Liverpool', 'match_date' => '2024-02-01', 'match_time' => '16:30:00', 'competition' => 'Premier League', 'venue' => 'Old Trafford', 'status' => 'upcoming'],
        ['home_team' => 'Manchester United', 'away_team' => 'Arsenal', 'match_date' => '2024-02-08', 'match_time' => '15:00:00', 'competition' => 'Premier League', 'venue' => 'Emirates Stadium', 'status' => 'upcoming'],
        ['home_team' => 'Manchester United', 'away_team' => 'Manchester City', 'match_date' => '2024-03-05', 'match_time' => '17:30:00', 'competition' => 'Premier League', 'venue' => 'Old Trafford', 'status' => 'upcoming']
    ];
}

// ==================== DATA STATISTIK MU ====================
$team_stats = [
    'goals_scored' => 38,
    'goals_conceded' => 32,
    'clean_sheets' => 8,
    'possession_avg' => 54,
    'shots_per_game' => 14.5,
    'pass_accuracy' => 82,
    'big_chances_created' => 45,
    'tackles_success' => 72
];

// ==================== DATA PRESTASI MU ====================
$achievements = [
    ['era' => 'Era Modern', 'achievements' => [
        ['year' => '2023', 'title' => 'Carabao Cup Winner', 'description' => 'Gelar Carabao Cup ke-6'],
        ['year' => '2021', 'title' => 'UEFA Europa League Runner-up', 'description' => 'Final melawan Villarreal'],
        ['year' => '2017', 'title' => 'UEFA Europa League Winner', 'description' => 'Gelar Eropa pertama Mourinho'],
        ['year' => '2016', 'title' => 'FA Cup Winner', 'description' => 'Gelar FA Cup ke-12']
    ]],
    ['era' => 'Era Alex Ferguson', 'achievements' => [
        ['year' => '2013', 'title' => 'Premier League Champion', 'description' => 'Gelar liga ke-20 (era Ferguson)'],
        ['year' => '2011', 'title' => 'Premier League Champion', 'description' => 'Gelar liga ke-19'],
        ['year' => '2008', 'title' => 'UEFA Champions League Winner', 'description' => 'Final dramatis melawan Chelsea'],
        ['year' => '2008', 'title' => 'Premier League Champion', 'description' => 'Gelar liga ke-10 Premier League'],
        ['year' => '1999', 'title' => 'Treble Winner', 'description' => 'Premier League, FA Cup, Champions League'],
        ['year' => '1993', 'title' => 'Premier League Champion', 'description' => 'Gelar liga pertama era Premier League']
    ]],
    ['era' => 'Era Klasik', 'achievements' => [
        ['year' => '1968', 'title' => 'European Cup Winner', 'description' => 'Gelar Eropa pertama (Busby Babes)'],
        ['year' => '1957', 'title' => 'First Division Champion', 'description' => 'Gelar liga pertama pasca-perang']
    ]]
];

// ==================== DATA SEJARAH MU ====================
$history = [
    ['period' => 'Pendirian & Awal Tahun', 'events' => [
        ['year' => '1878', 'event' => 'Didirikan sebagai Newton Heath LYR F.C.'],
        ['year' => '1902', 'event' => 'Berganti nama menjadi Manchester United'],
        ['year' => '1908', 'event' => 'Juara First Division pertama'],
        ['year' => '1948', 'event' => 'Memenangi Piala FA pertama']
    ]],
    ['period' => 'Tragedi & Kebangkitan', 'events' => [
        ['year' => '1958', 'event' => 'Tragedi Munich - 8 pemain tewas'],
        ['year' => '1963', 'event' => 'Piala FA pertama pasca-tragedi'],
        ['year' => '1968', 'event' => 'European Cup pertama (Busby Babes)']
    ]],
    ['period' => 'Era Dominasi', 'events' => [
        ['year' => '1986', 'event' => 'Alex Ferguson menjadi manager'],
        ['year' => '1990', 'event' => 'Piala FA - awal era dominasi'],
        ['year' => '1999', 'event' => 'Treble Winner (legendaris)'],
        ['year' => '2013', 'event' => 'Alex Ferguson pensiun'],
        ['year' => '2022', 'event' => 'Erik ten Hag menjadi manager']
    ]]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manchester United - Berita, Jadwal & Statistik | Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS SAMA DENGAN SEBELUMNYA - TAMBAHKAN STYLE UNTUK TAB SYSTEM */
        
        /* Tab System */
        .info-tabs {
            margin: 4rem 0;
        }
        
        .tab-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--gray);
            padding: 1rem 2rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tab-btn:hover {
            background: rgba(255,255,255,0.1);
            color: var(--light);
        }
        
        .tab-btn.active {
            background: var(--united-red);
            color: white;
            border-color: var(--united-red);
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .stat-item {
            background: rgba(255,255,255,0.05);
            padding: 2rem 1.5rem;
            border-radius: 15px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(218, 41, 28, 0.2);
        }
        
        .stat-value {
            display: block;
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--united-red);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Achievements Section */
        .era-section {
            margin-bottom: 3rem;
        }
        
        .era-title {
            color: var(--united-red);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--united-red);
        }
        
        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .achievement-card {
            background: rgba(255,255,255,0.05);
            padding: 1.5rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .achievement-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--united-red);
        }
        
        .achievement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(218, 41, 28, 0.2);
        }
        
        .achievement-year {
            background: var(--united-red);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .achievement-title {
            font-size: 1.2rem;
            color: var(--light);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .achievement-description {
            color: var(--gray);
            line-height: 1.5;
            font-size: 0.9rem;
        }
        
        /* History Section */
        .period-section {
            margin-bottom: 3rem;
        }
        
        .period-title {
            color: var(--united-red);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--united-red);
        }
        
        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--united-red);
        }
        
        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .timeline-year {
            background: var(--united-red);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 15px;
            font-weight: 700;
            font-size: 0.9rem;
            min-width: 80px;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        
        .timeline-content {
            background: rgba(255,255,255,0.05);
            padding: 1.5rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            margin-left: 2rem;
            flex: 1;
            transition: all 0.3s ease;
        }
        
        .timeline-content:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(218, 41, 28, 0.2);
        }
        
        .timeline-event {
            color: var(--light);
            font-weight: 500;
            line-height: 1.5;
        }
        
        /* Quick Facts */
        .quick-facts {
            margin: 4rem 0;
        }
        
        .facts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .fact-card {
            background: rgba(255,255,255,0.05);
            padding: 2rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .fact-card:hover {
            transform: translateY(-5px);
        }
        
        .fact-card h4 {
            color: var(--united-red);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        
        .fact-card p {
            color: var(--light);
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .tab-buttons { flex-direction: column; align-items: center; }
            .tab-btn { width: 100%; max-width: 300px; justify-content: center; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .achievements-grid { grid-template-columns: 1fr; }
            .timeline::before { left: 20px; }
            .timeline-year { min-width: 60px; font-size: 0.8rem; }
            .timeline-content { margin-left: 1.5rem; }
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
                <a href="manchester-united.php" class="active"><i class="fas fa-fire"></i> Manchester United</a>
                <a href="manchester-city.php"><i class="fas fa-bolt"></i> Manchester City</a>
                <?php if(isAdminLoggedIn()): ?>
                    <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin Panel</a>
                    <a href="admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="admin/login.php"><i class="fas fa-sign-in-alt"></i> Admin Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Team Hero -->
    <section class="team-hero">
        <div class="container">
            <div class="team-badge-large">
                <i class="fas fa-fire"></i>
            </div>
            <h1>Manchester United</h1>
            <p class="team-nickname">The Red Devils • Setan Merah</p>
            
            <div class="team-stats">
                <div class="team-stat">
                    <span class="stat-number"><?php echo $stats['total_articles']; ?></span>
                    <span class="stat-label">Total Berita</span>
                </div>
                <div class="team-stat">
                    <span class="stat-number"><?php echo $stats['news_count']; ?></span>
                    <span class="stat-label">Berita Tim</span>
                </div>
                <div class="team-stat">
                    <span class="stat-number"><?php echo $stats['transfer_count']; ?></span>
                    <span class="stat-label">Transfer</span>
                </div>
                <div class="team-stat">
                    <span class="stat-number"><?php echo $stats['injury_count']; ?></span>
                    <span class="stat-label">Update Cedera</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container">
        <!-- Upcoming Matches -->
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
                        <?php if ($match['status'] == 'completed'): ?>
                            <span class="score"><?php echo $match['score'] ?? '2-1'; ?></span>
                        <?php elseif ($match['status'] == 'upcoming'): ?>
                            <span class="upcoming-badge">Akan Datang</span>
                        <?php else: ?>
                            <span class="upcoming-badge" style="background: #EF4444;">🔴 LIVE</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- MU News -->
        <section class="team-news">
            <h2 class="section-title"><i class="fas fa-newspaper"></i> Berita Manchester United</h2>
            
            <?php if (empty($articles)): ?>
                <div style="text-align: center; padding: 3rem; background: rgba(255,255,255,0.05); border-radius: 15px;">
                    <i class="fas fa-newspaper" style="font-size: 3rem; color: var(--gray); margin-bottom: 1rem;"></i>
                    <p style="color: var(--gray); font-size: 1.1rem;">Belum ada berita untuk Manchester United.</p>
                    <?php if (isAdminLoggedIn()): ?>
                        <a href="admin/add-article.php" class="btn" style="background: var(--united-red); color: white; padding: 1rem 2rem; border-radius: 25px; text-decoration: none; margin-top: 1rem; display: inline-block;">
                            <i class="fas fa-plus"></i> Tambah Berita MU
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="news-grid">
                    <?php foreach ($articles as $article): ?>
                    <article class="news-card">
                        <div class="news-header">
                            <span class="category-badge">
                                <i class="fas fa-<?php 
                                    switch($article['category'] ?? 'news') {
                                        case 'transfer': echo 'exchange-alt'; break;
                                        case 'injury': echo 'first-aid'; break;
                                        case 'match': echo 'futbol'; break;
                                        default: echo 'newspaper';
                                    }
                                ?>"></i>
                                <?php 
                                    switch($article['category'] ?? 'news') {
                                        case 'news': echo 'Berita'; break;
                                        case 'transfer': echo 'Transfer'; break;
                                        case 'injury': echo 'Cedera'; break;
                                        case 'match': echo 'Pertandingan'; break;
                                        default: echo 'Berita';
                                    }
                                ?>
                            </span>
                            <span class="news-date"><i class="far fa-clock"></i> <?php echo formatDate($article['created_at']); ?></span>
                        </div>
                        
                        <h3 class="news-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                        <p class="news-excerpt"><?php echo truncateText(htmlspecialchars($article['content']), 150); ?></p>
                        
                        <div class="news-meta">
                            <span class="read-time"><i class="far fa-eye"></i> 2.4k views • ⏱️ 3 min read</span>
                            <button class="share-btn" onclick="shareArticle('<?php echo htmlspecialchars($article['title']); ?>')">
                                <i class="fas fa-share-alt"></i> Share
                            </button>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Info Tabs -->
        <section class="info-tabs">
            <h2 class="section-title"><i class="fas fa-info-circle"></i> Informasi Klub</h2>
            
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="statistics">
                    <i class="fas fa-chart-line"></i> Statistik
                </button>
                <button class="tab-btn" data-tab="achievements">
                    <i class="fas fa-trophy"></i> Prestasi
                </button>
                <button class="tab-btn" data-tab="history">
                    <i class="fas fa-landmark"></i> Sejarah
                </button>
                <button class="tab-btn" data-tab="facts">
                    <i class="fas fa-star"></i> Fakta Cepat
                </button>
            </div>
            
            <!-- Statistics Tab -->
            <div class="tab-content active" id="statistics">
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
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $team_stats['shots_per_game']; ?></span>
                        <span class="stat-label">Tembakan per Game</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $team_stats['pass_accuracy']; ?>%</span>
                        <span class="stat-label">Akurasi Umpan</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $team_stats['big_chances_created']; ?></span>
                        <span class="stat-label">Peluang Emas</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $team_stats['tackles_success']; ?>%</span>
                        <span class="stat-label">Tackle Berhasil</span>
                    </div>
                </div>
            </div>
            
            <!-- Achievements Tab -->
            <div class="tab-content" id="achievements">
                <?php foreach ($achievements as $era): ?>
                <div class="era-section">
                    <h3 class="era-title"><?php echo $era['era']; ?></h3>
                    <div class="achievements-grid">
                        <?php foreach ($era['achievements'] as $achievement): ?>
                        <div class="achievement-card">
                            <div class="achievement-year"><?php echo $achievement['year']; ?></div>
                            <h4 class="achievement-title"><?php echo $achievement['title']; ?></h4>
                            <p class="achievement-description"><?php echo $achievement['description']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- History Tab -->
            <div class="tab-content" id="history">
                <?php foreach ($history as $period): ?>
                <div class="period-section">
                    <h3 class="period-title"><?php echo $period['period']; ?></h3>
                    <div class="timeline">
                        <?php foreach ($period['events'] as $event): ?>
                        <div class="timeline-item">
                            <div class="timeline-year"><?php echo $event['year']; ?></div>
                            <div class="timeline-content">
                                <p class="timeline-event"><?php echo $event['event']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Quick Facts Tab -->
            <div class="tab-content" id="facts">
                <div class="facts-grid">
                    <div class="fact-card">
                        <h4><i class="fas fa-trophy"></i> Total Gelar Liga</h4>
                        <p>20 Titles</p>
                    </div>
                    <div class="fact-card">
                        <h4><i class="fas fa-crown"></i> Top Scorer</h4>
                        <p>Wayne Rooney (253)</p>
                    </div>
                    <div class="fact-card">
                        <h4><i class="fas fa-stadium"></i> Stadium</h4>
                        <p>Old Trafford (74,140)</p>
                    </div>
                    <div class="fact-card">
                        <h4><i class="fas fa-user-tie"></i> Manager</h4>
                        <p>Erik ten Hag</p>
                    </div>
                    <div class="fact-card">
                        <h4><i class="fas fa-tshirt"></i> Warna Kandang</h4>
                        <p>Merah, Putih, Hitam</p>
                    </div>
                    <div class="fact-card">
                        <h4><i class="fas fa-futbol"></i> Julukan</h4>
                        <p>The Red Devils</p>
                    </div>
                </div>
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
                    <h4>Manchester United</h4>
                    <a href="#"><i class="fas fa-users"></i> Skuad Pemain</a>
                    <a href="#"><i class="fas fa-calendar"></i> Jadwal</a>
                    <a href="#"><i class="fas fa-trophy"></i> Pencapaian</a>
                    <a href="#"><i class="fas fa-history"></i> Sejarah</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Manchester Side. All rights reserved. | Glory Glory Man United! <i class="fas fa-heart" style="color: var(--united-red);"></i></p>
            </div>
        </div>
    </footer>

    <script>
        // Tab System
        document.addEventListener('DOMContentLoaded', function() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all buttons and contents
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(15, 23, 42, 0.98)';
            } else {
                header.style.background = 'rgba(15, 23, 42, 0.95)';
            }
        });

        // Share function
        function shareArticle(title) {
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: window.location.href
                });
            } else {
                alert('Share: ' + title);
            }
        }

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.addEventListener('DOMContentLoaded', function() {
            const elementsToAnimate = document.querySelectorAll('.news-card, .team-stat, .stat-item, .match-row, .achievement-card, .timeline-content, .fact-card');
            elementsToAnimate.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'all 0.6s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>