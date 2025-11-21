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

// ==================== AMBIL DATA CITY ====================
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
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE team = 'manchester-city' ORDER BY created_at DESC");
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("SELECT * FROM matches WHERE (home_team LIKE '%City%' OR away_team LIKE '%City%') ORDER BY match_date ASC LIMIT 6");
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

// Sample data jika database kosong
if (empty($articles)) {
    $articles = [
        ['id' => 2, 'title' => 'Haaland Raih Hat-trick, City Hancurkan Tottenham', 'content' => 'Erling Haaland menunjukkan kelas dunia dengan mencetak hat-trick melawan Tottenham.', 'team' => 'manchester-city', 'category' => 'news', 'created_at' => date('Y-m-d H:i:s')],
        ['id' => 4, 'title' => 'Kevin De Bruyne Kembali Cedera', 'content' => 'Playmaker andalan Manchester City Kevin De Bruyne harus kembali absen.', 'team' => 'manchester-city', 'category' => 'injury', 'created_at' => date('Y-m-d H:i:s')],
    ];
    $stats = ['total_articles' => 2, 'news_count' => 1, 'transfer_count' => 0, 'injury_count' => 1];
}

if (empty($matches)) {
    $matches = [
        ['home_team' => 'Manchester City', 'away_team' => 'Chelsea', 'match_date' => '2024-02-02', 'match_time' => '15:00:00', 'competition' => 'Premier League', 'venue' => 'Etihad Stadium', 'status' => 'upcoming'],
    ];
}

// Data ringkas
$team_stats = [
    'goals_scored' => 45,
    'goals_conceded' => 18,
    'clean_sheets' => 12,
    'possession_avg' => 68,
    'shots_per_game' => 17.2,
    'pass_accuracy' => 89
];

$key_achievements = [
    ['year' => '2023', 'title' => 'Treble Winner (Premier League, FA Cup, Champions League)'],
    ['year' => '2023', 'title' => 'UEFA Champions League Winner'],
    ['year' => '2023', 'title' => 'FA Cup Winner'],
    ['year' => '2022', 'title' => 'Premier League Champion'],
    ['year' => '2021', 'title' => 'Premier League Champion'],
    ['year' => '2019', 'title' => 'Domestic Treble (Premier League, FA Cup, League Cup)'],
    ['year' => '2019', 'title' => 'FA Cup Winner'],
    ['year' => '2018', 'title' => 'Premier League Champion (100 Poin)'],
    ['year' => '2014', 'title' => 'Premier League Champion'],
    ['year' => '2012', 'title' => 'Premier League Champion (Gol Aguero)'],
    ['year' => '2011', 'title' => 'FA Cup Winner'],
    ['year' => '1970', 'title' => 'European Cup Winners Cup'],
    ['year' => '1969', 'title' => 'FA Cup Winner'],
    ['year' => '1968', 'title' => 'First Division Champion'],
    ['year' => '1956', 'title' => 'FA Cup Winner'],
    ['year' => '1937', 'title' => 'First Division Champion'],
    ['year' => '1904', 'title' => 'FA Cup Winner'],
];

$history_summary = "Manchester City didirikan pada tahun 1880 sebagai St. Mark's (West Gorton) oleh Anna Connell dan para anggota Gereja St. Mark's. Klub kemudian berganti nama menjadi Ardwick A.F.C. pada tahun 1887 setelah pindah ke stadion baru di Hyde Road. Pada tahun 1894, setelah mengalami kesulitan keuangan, klub dibentuk ulang sebagai Manchester City F.C. yang kita kenal saat ini. Meraih trofi FA Cup pertama tahun 1904 dan gelar First Division pertama tahun 1937. Era modern dimulai sejak akuisisi oleh Abu Dhabi United Group tahun 2008, yang membawa transformasi besar. Puncaknya pada musim 2022-23 dengan meraih Treble Winner pertama dalam sejarah klub di bawah kepemimpinan Pep Guardiola.";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manchester City - Berita, Jadwal & Statistik | Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --united-red: #DA291C;
            --city-blue: #6CABDD;
            --city-dark: #1D4ED8;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: var(--light);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo h1 {
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--united-red), var(--city-blue));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }
        
        nav {
            display: flex;
            gap: 2rem;
        }
        
        nav a {
            color: var(--light);
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        nav a:hover {
            background: rgba(255,255,255,0.1);
        }
        
        nav a.active {
            background: var(--city-blue);
        }
        
        /* Team Hero */
        .team-hero {
            background: linear-gradient(135deg, rgba(108, 171, 221, 0.2), rgba(15, 23, 42, 0.9));
            padding: 120px 0 80px;
            text-align: center;
        }
        
        .team-badge-large {
            font-size: 5rem;
            margin-bottom: 1rem;
            color: var(--city-blue);
        }
        
        .team-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 0.5rem;
            color: var(--city-blue);
        }
        
        .team-nickname {
            font-size: 1.3rem;
            color: var(--gray);
            margin-bottom: 3rem;
        }
        
        .team-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            flex-wrap: wrap;
        }
        
        .team-stat {
            text-align: center;
        }
        
        .stat-number {
            display: block;
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--city-blue);
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        /* Matches Section */
        .matches-section {
            margin: 3rem 0;
        }
        
        .section-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 2rem;
        }
        
        .matches-table {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            overflow: hidden;
        }
        
        .match-row {
            display: grid;
            grid-template-columns: 150px 1fr 200px 150px;
            gap: 1rem;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            align-items: center;
        }
        
        .match-row:last-child {
            border-bottom: none;
        }
        
        .match-date .date {
            display: block;
            font-weight: 600;
        }
        
        .match-date .time {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .match-fixture {
            display: flex;
            align-items: center;
            gap: 1rem;
            justify-content: center;
        }
        
        .current-team {
            color: var(--city-blue);
            font-weight: 700;
        }
        
        .vs {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .match-competition .competition {
            display: block;
            font-weight: 600;
        }
        
        .match-competition .venue {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .upcoming-badge {
            background: var(--city-blue);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        /* News Section */
        .team-news {
            margin: 4rem 0;
        }
        
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .news-card {
            background: rgba(255,255,255,0.05);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255,255,255,0.1);
            transition: transform 0.3s ease;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
        }
        
        .news-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .category-badge {
            background: var(--city-blue);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 15px;
            font-size: 0.85rem;
        }
        
        .news-date {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .news-title {
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        
        .news-excerpt {
            color: var(--gray);
            margin-bottom: 1.5rem;
        }
        
        .news-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .share-btn {
            background: transparent;
            border: 1px solid var(--city-blue);
            color: var(--city-blue);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .share-btn:hover {
            background: var(--city-blue);
            color: white;
        }
        
        /* Info Cards Section */
        .info-cards {
            margin: 4rem 0;
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .info-card {
            background: rgba(255,255,255,0.05);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(108, 171, 221, 0.3);
            border-color: var(--city-blue);
        }
        
        .info-card i {
            font-size: 3rem;
            color: var(--city-blue);
            margin-bottom: 1rem;
        }
        
        .info-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .info-card p {
            color: var(--gray);
            margin-bottom: 1.5rem;
        }
        
        .info-card .btn {
            background: var(--city-blue);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .info-card .btn:hover {
            background: var(--city-dark);
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(5px);
            overflow-y: auto;
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .modal-content {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            border-radius: 20px;
            padding: 3rem;
            max-width: 900px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
            border: 1px solid rgba(255,255,255,0.1);
            animation: modalSlideIn 0.3s ease;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--city-blue);
        }
        
        .modal-header h2 {
            font-size: 2rem;
            color: var(--city-blue);
        }
        
        .close-modal {
            background: transparent;
            border: none;
            color: var(--gray);
            font-size: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .close-modal:hover {
            color: var(--city-blue);
            transform: rotate(90deg);
        }
        
        /* Stats Grid in Modal */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .stat-item {
            background: rgba(255,255,255,0.05);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .stat-value {
            display: block;
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--city-blue);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        
        /* Achievements List */
        .achievements-list {
            display: grid;
            gap: 1.5rem;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .achievement-item {
            background: rgba(255,255,255,0.05);
            padding: 1.5rem;
            border-radius: 15px;
            border-left: 4px solid var(--city-blue);
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .achievement-year {
            background: var(--city-blue);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.2rem;
            min-width: 80px;
            text-align: center;
        }
        
        .achievement-title {
            font-size: 1.2rem;
            color: var(--light);
            font-weight: 600;
        }
        
        /* History Content */
        .history-content {
            background: rgba(255,255,255,0.05);
            padding: 2rem;
            border-radius: 15px;
            border-left: 4px solid var(--city-blue);
            line-height: 1.8;
            font-size: 1.1rem;
            color: var(--light);
        }
        
        /* Competition Stats */
        .competition-stats {
            margin-top: 3rem;
        }
        
        .competition-title {
            color: var(--city-blue);
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        /* Footer */
        footer {
            background: rgba(15, 23, 42, 0.95);
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 3rem 0 2rem;
            margin-top: 4rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }
        
        .footer-logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--united-red), var(--city-blue));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        
        .footer-description {
            color: var(--gray);
            line-height: 1.6;
        }
        
        .footer-links h4 {
            color: var(--light);
            margin-bottom: 1rem;
        }
        
        .footer-links a {
            display: block;
            color: var(--gray);
            text-decoration: none;
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: var(--city-blue);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: var(--gray);
        }
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
        /* Responsive */
        @media (max-width: 768px) {
            .team-hero h1 { font-size: 2.5rem; }
            .team-stats { flex-direction: column; gap: 1.5rem; }
            .match-row { grid-template-columns: 1fr; gap: 0.5rem; }
            .news-grid, .cards-grid { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr; }
            nav { display: none; }
            .modal-content { padding: 2rem 1.5rem; }
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

    <!-- Team Hero -->
    <section class="team-hero">
        <div class="container">
            <div class="team-badge-large">
                <i class="fas fa-bolt"></i>
            </div>
            <h1>Manchester City</h1>
            <p class="team-nickname">The Citizens • City</p>
            
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
                        <span class="home-team <?php echo strpos($match['home_team'], 'City') !== false ? 'current-team' : ''; ?>">
                            <?php echo $match['home_team']; ?>
                        </span>
                        <span class="vs">vs</span>
                        <span class="away-team <?php echo strpos($match['away_team'], 'City') !== false ? 'current-team' : ''; ?>">
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
            <h2 class="section-title"><i class="fas fa-newspaper"></i> Berita Manchester City</h2>
            
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
                        <span class="read-time"><i class="far fa-eye"></i> 2.1k views</span>
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
                    <p>Lihat statistik performa Manchester City musim ini</p>
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
                    <p>Perjalanan Manchester City dari awal hingga kini</p>
                    <span class="btn">Lihat Detail</span>
                </div>
            </div>
        </section>
    </main>

    <!-- Modals -->
    <!-- Statistics Modal -->
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
                <div class="stat-item">
                    <span class="stat-value"><?php echo $team_stats['shots_per_game']; ?></span>
                    <span class="stat-label">Tembakan per Game</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo $team_stats['pass_accuracy']; ?>%</span>
                    <span class="stat-label">Akurasi Umpan</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">9</span>
                    <span class="stat-label">Kemenangan Beruntun</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">85%</span>
                    <span class="stat-label">Menang di Kandang</span>
                </div>
            </div>
            
            <div class="competition-stats">
                <h3 class="competition-title">Statistik Kompetisi</h3>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-value">1st</span>
                        <span class="stat-label">Posisi Premier League</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">8x</span>
                        <span class="stat-label">Premier League Winner</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">7x</span>
                        <span class="stat-label">FA Cup Winner</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">1x</span>
                        <span class="stat-label">UCL Winner</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Achievements Modal -->
    <div id="achievementsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-trophy"></i> Prestasi & Gelar Manchester City</h2>
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

    <!-- History Modal -->
    <div id="historyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-landmark"></i> Sejarah Manchester City</h2>
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
                    <h4>Manchester City</h4>
                    <a href="#"><i class="fas fa-users"></i> Skuad Pemain</a>
                    <a href="#"><i class="fas fa-calendar"></i> Jadwal</a>
                    <a href="#"><i class="fas fa-trophy"></i> Pencapaian</a>
                    <a href="#"><i class="fas fa-history"></i> Sejarah</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Manchester Side. All rights reserved. | Come On City! <i class="fas fa-heart" style="color: var(--city-blue);"></i></p>
            </div>
        </div>
    </footer>

    <script src="js/team-page.js"></script>
    <script>
        // Animation on scroll untuk elemen spesifik Manchester City
        document.addEventListener('DOMContentLoaded', function() {
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

            const elementsToAnimate = document.querySelectorAll('.news-card, .team-stat, .match-row, .info-card');
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
