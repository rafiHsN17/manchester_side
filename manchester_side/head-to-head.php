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

// Sample H2H data (bisa diganti dengan data dari database)
$h2h_stats = [
    'total_matches' => 189,
    'mu_wins' => 77,
    'city_wins' => 57,
    'draws' => 55,
    'mu_goals' => 283,
    'city_goals' => 249
];

$recent_matches = [
    ['date' => '2024-10-26', 'home' => 'Manchester United', 'away' => 'Manchester City', 'score' => '2-1', 'competition' => 'Premier League'],
    ['date' => '2024-05-25', 'home' => 'Manchester City', 'away' => 'Manchester United', 'score' => '1-1', 'competition' => 'FA Cup Final'],
    ['date' => '2024-03-03', 'home' => 'Manchester City', 'away' => 'Manchester United', 'score' => '3-1', 'competition' => 'Premier League'],
    ['date' => '2023-10-29', 'home' => 'Manchester United', 'away' => 'Manchester City', 'score' => '0-3', 'competition' => 'Premier League'],
    ['date' => '2023-01-14', 'home' => 'Manchester United', 'away' => 'Manchester City', 'score' => '2-1', 'competition' => 'Premier League']
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Head to Head - Manchester United vs Manchester City | Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/head-to-head.css">
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
                <a href="head-to-head.php" class="active"><i class="fas fa-trophy"></i> H2H</a>
                <?php if(isAdminLoggedIn()): ?>
                    <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="h2h-hero">
        <div class="container">
            <h1 class="page-title">HEAD TO HEAD</h1>
            <p class="page-subtitle">Manchester Derby - Rivalitas Terbesar Manchester</p>
            
            <div class="vs-section">
                <div class="team-side united">
                    <div class="team-badge">🔴</div>
                    <h2>Manchester United</h2>
                </div>
                
                <div class="vs-divider">
                    <span class="vs-text">VS</span>
                </div>
                
                <div class="team-side city">
                    <div class="team-badge">🔵</div>
                    <h2>Manchester City</h2>
                </div>
            </div>
        </div>
    </section>

    <!-- Overall Stats -->
    <section class="overall-stats">
        <div class="container">
            <h2 class="section-title">Statistik Keseluruhan</h2>
            
            <div class="stats-grid">
                <div class="stat-card united">
                    <div class="stat-number"><?php echo $h2h_stats['mu_wins']; ?></div>
                    <div class="stat-label">Kemenangan MU</div>
                </div>
                
                <div class="stat-card draw">
                    <div class="stat-number"><?php echo $h2h_stats['draws']; ?></div>
                    <div class="stat-label">Seri</div>
                </div>
                
                <div class="stat-card city">
                    <div class="stat-number"><?php echo $h2h_stats['city_wins']; ?></div>
                    <div class="stat-label">Kemenangan City</div>
                </div>
            </div>

            <div class="stats-comparison">
                <div class="comparison-item">
                    <div class="comparison-label">Total Pertandingan</div>
                    <div class="comparison-value"><?php echo $h2h_stats['total_matches']; ?></div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-label">Total Gol MU</div>
                    <div class="comparison-value united-color"><?php echo $h2h_stats['mu_goals']; ?></div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-label">Total Gol City</div>
                    <div class="comparison-value city-color"><?php echo $h2h_stats['city_goals']; ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Matches -->
    <section class="recent-matches">
        <div class="container">
            <h2 class="section-title">5 Pertandingan Terakhir</h2>
            
            <div class="matches-list">
                <?php foreach ($recent_matches as $match): ?>
                <div class="match-card">
                    <div class="match-date">
                        <i class="far fa-calendar"></i>
                        <?php echo date('d M Y', strtotime($match['date'])); ?>
                    </div>
                    
                    <div class="match-details">
                        <div class="team home <?php echo $match['home'] == 'Manchester United' ? 'united' : 'city'; ?>">
                            <span class="team-badge"><?php echo $match['home'] == 'Manchester United' ? '🔴' : '🔵'; ?></span>
                            <span class="team-name"><?php echo $match['home']; ?></span>
                        </div>
                        
                        <div class="match-score">
                            <span class="score"><?php echo $match['score']; ?></span>
                        </div>
                        
                        <div class="team away <?php echo $match['away'] == 'Manchester United' ? 'united' : 'city'; ?>">
                            <span class="team-name"><?php echo $match['away']; ?></span>
                            <span class="team-badge"><?php echo $match['away'] == 'Manchester United' ? '🔴' : '🔵'; ?></span>
                        </div>
                    </div>
                    
                    <div class="match-competition">
                        <i class="fas fa-trophy"></i>
                        <?php echo $match['competition']; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Fun Facts -->
    <section class="fun-facts">
        <div class="container">
            <h2 class="section-title">Fakta Menarik</h2>
            
            <div class="facts-grid">
                <div class="fact-card">
                    <i class="fas fa-star"></i>
                    <h3>Pertandingan Pertama</h3>
                    <p>12 November 1881 - MU 3-0 City</p>
                </div>
                
                <div class="fact-card">
                    <i class="fas fa-fire"></i>
                    <h3>Kemenangan Terbesar MU</h3>
                    <p>6-1 (September 1926)</p>
                </div>
                
                <div class="fact-card">
                    <i class="fas fa-bolt"></i>
                    <h3>Kemenangan Terbesar City</h3>
                    <p>6-1 (Oktober 2011)</p>
                </div>
                
                <div class="fact-card">
                    <i class="fas fa-futbol"></i>
                    <h3>Top Scorer Derby</h3>
                    <p>Wayne Rooney (11 gol)</p>
                </div>
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
