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

function formatTime($time) {
    return date('H:i', strtotime($time));
}

// Get matches from database
$matches = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM matches ORDER BY match_date ASC, match_time ASC");
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Silent error
    }
}

// Sample data jika database kosong
if (empty($matches)) {
    $matches = [
        ['id' => 1, 'home_team' => 'Manchester United', 'away_team' => 'Liverpool', 'match_date' => '2024-12-15', 'match_time' => '16:30:00', 'competition' => 'Premier League', 'venue' => 'Old Trafford', 'status' => 'upcoming', 'score' => null],
        ['id' => 2, 'home_team' => 'Manchester City', 'away_team' => 'Chelsea', 'match_date' => '2024-12-18', 'match_time' => '20:00:00', 'competition' => 'Premier League', 'venue' => 'Etihad Stadium', 'status' => 'upcoming', 'score' => null],
        ['id' => 3, 'home_team' => 'Arsenal', 'away_team' => 'Manchester United', 'match_date' => '2024-12-22', 'match_time' => '15:00:00', 'competition' => 'Premier League', 'venue' => 'Emirates Stadium', 'status' => 'upcoming', 'score' => null],
        ['id' => 4, 'home_team' => 'Manchester City', 'away_team' => 'Everton', 'match_date' => '2024-12-26', 'match_time' => '17:30:00', 'competition' => 'Premier League', 'venue' => 'Etihad Stadium', 'status' => 'upcoming', 'score' => null],
        ['id' => 5, 'home_team' => 'Newcastle', 'away_team' => 'Manchester United', 'match_date' => '2024-12-30', 'match_time' => '20:00:00', 'competition' => 'Premier League', 'venue' => 'St James Park', 'status' => 'upcoming', 'score' => null],
    ];
}

// Separate matches by status
$upcoming_matches = array_filter($matches, function($m) { return $m['status'] == 'upcoming'; });
$completed_matches = array_filter($matches, function($m) { return $m['status'] == 'completed'; });
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pertandingan - Manchester United & Manchester City | Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/matches.css">
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
                <a href="matches.php" class="active"><i class="fas fa-calendar"></i> Matches</a>
                <?php if(isAdminLoggedIn()): ?>
                    <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="matches-hero">
        <div class="container">
            <div class="hero-content">
                <i class="fas fa-calendar-alt hero-icon"></i>
                <h1 class="page-title">JADWAL PERTANDINGAN</h1>
                <p class="page-subtitle">Jadwal Lengkap Manchester United & Manchester City</p>
            </div>
        </div>
    </section>

    <!-- Filter Tabs -->
    <section class="filter-section">
        <div class="container">
            <div class="filter-tabs">
                <button class="tab-btn active" data-tab="upcoming">
                    <i class="fas fa-clock"></i> Mendatang
                </button>
                <button class="tab-btn" data-tab="completed">
                    <i class="fas fa-check-circle"></i> Selesai
                </button>
            </div>
        </div>
    </section>

    <!-- Upcoming Matches -->
    <section class="matches-section" id="upcoming-section">
        <div class="container">
            <h2 class="section-title">Pertandingan Mendatang</h2>
            
            <?php if (empty($upcoming_matches)): ?>
                <div class="no-data">
                    <i class="fas fa-info-circle"></i>
                    <p>Belum ada jadwal pertandingan mendatang</p>
                </div>
            <?php else: ?>
                <div class="matches-list">
                    <?php foreach ($upcoming_matches as $match): ?>
                    <div class="match-card">
                        <div class="match-header">
                            <div class="match-date">
                                <i class="far fa-calendar"></i>
                                <?php echo formatDate($match['match_date']); ?>
                            </div>
                            <div class="match-time">
                                <i class="far fa-clock"></i>
                                <?php echo formatTime($match['match_time']); ?> WIB
                            </div>
                        </div>

                        <div class="match-body">
                            <div class="team home-team <?php echo (strpos(strtolower($match['home_team']), 'united') !== false) ? 'mu' : ((strpos(strtolower($match['home_team']), 'city') !== false) ? 'city' : ''); ?>">
                                <span class="team-badge">
                                    <?php 
                                    if (strpos(strtolower($match['home_team']), 'united') !== false) echo '🔴';
                                    elseif (strpos(strtolower($match['home_team']), 'city') !== false) echo '🔵';
                                    else echo '⚪';
                                    ?>
                                </span>
                                <span class="team-name"><?php echo htmlspecialchars($match['home_team']); ?></span>
                            </div>

                            <div class="vs-divider">
                                <span class="vs-text">VS</span>
                            </div>

                            <div class="team away-team <?php echo (strpos(strtolower($match['away_team']), 'united') !== false) ? 'mu' : ((strpos(strtolower($match['away_team']), 'city') !== false) ? 'city' : ''); ?>">
                                <span class="team-name"><?php echo htmlspecialchars($match['away_team']); ?></span>
                                <span class="team-badge">
                                    <?php 
                                    if (strpos(strtolower($match['away_team']), 'united') !== false) echo '🔴';
                                    elseif (strpos(strtolower($match['away_team']), 'city') !== false) echo '🔵';
                                    else echo '⚪';
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="match-footer">
                            <div class="competition">
                                <i class="fas fa-trophy"></i>
                                <?php echo htmlspecialchars($match['competition']); ?>
                            </div>
                            <div class="venue">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($match['venue']); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Completed Matches -->
    <section class="matches-section" id="completed-section" style="display: none;">
        <div class="container">
            <h2 class="section-title">Hasil Pertandingan</h2>
            
            <?php if (empty($completed_matches)): ?>
                <div class="no-data">
                    <i class="fas fa-info-circle"></i>
                    <p>Belum ada hasil pertandingan</p>
                </div>
            <?php else: ?>
                <div class="matches-list">
                    <?php foreach ($completed_matches as $match): ?>
                    <div class="match-card completed">
                        <div class="match-header">
                            <div class="match-date">
                                <i class="far fa-calendar"></i>
                                <?php echo formatDate($match['match_date']); ?>
                            </div>
                            <span class="status-badge completed">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>
                        </div>

                        <div class="match-body">
                            <div class="team home-team <?php echo (strpos(strtolower($match['home_team']), 'united') !== false) ? 'mu' : ((strpos(strtolower($match['home_team']), 'city') !== false) ? 'city' : ''); ?>">
                                <span class="team-badge">
                                    <?php 
                                    if (strpos(strtolower($match['home_team']), 'united') !== false) echo '🔴';
                                    elseif (strpos(strtolower($match['home_team']), 'city') !== false) echo '🔵';
                                    else echo '⚪';
                                    ?>
                                </span>
                                <span class="team-name"><?php echo htmlspecialchars($match['home_team']); ?></span>
                            </div>

                            <div class="score-display">
                                <span class="final-score"><?php echo htmlspecialchars($match['score'] ?? '0-0'); ?></span>
                            </div>

                            <div class="team away-team <?php echo (strpos(strtolower($match['away_team']), 'united') !== false) ? 'mu' : ((strpos(strtolower($match['away_team']), 'city') !== false) ? 'city' : ''); ?>">
                                <span class="team-name"><?php echo htmlspecialchars($match['away_team']); ?></span>
                                <span class="team-badge">
                                    <?php 
                                    if (strpos(strtolower($match['away_team']), 'united') !== false) echo '🔴';
                                    elseif (strpos(strtolower($match['away_team']), 'city') !== false) echo '🔵';
                                    else echo '⚪';
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="match-footer">
                            <div class="competition">
                                <i class="fas fa-trophy"></i>
                                <?php echo htmlspecialchars($match['competition']); ?>
                            </div>
                            <div class="venue">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($match['venue']); ?>
                            </div>
                        </div>
                    </div>
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
                    <a href="head-to-head.php"><i class="fas fa-trophy"></i> H2H</a>
                    <a href="injury-news.php"><i class="fas fa-medkit"></i> Injury</a>
                    <a href="transfer.php"><i class="fas fa-exchange-alt"></i> Transfer</a>
                    <a href="videos.php"><i class="fas fa-video"></i> Videos</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Manchester Side. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/matches.js"></script>
</body>
</html>
