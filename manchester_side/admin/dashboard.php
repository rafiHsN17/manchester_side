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
    die("Koneksi database gagal: " . $e->getMessage());
}

// ==================== FUNGSI HELPER ====================
function redirect($url) {
    header("Location: $url");
    exit();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function getTeamName($team) {
    return $team == 'manchester-united' ? 'Manchester United' : 'Manchester City';
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

// ==================== CEK LOGIN ====================
if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// ==================== AMBIL DATA ====================
// Statistik
$total_articles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$mu_articles = $pdo->query("SELECT COUNT(*) FROM articles WHERE team = 'manchester-united'")->fetchColumn();
$city_articles = $pdo->query("SELECT COUNT(*) FROM articles WHERE team = 'manchester-city'")->fetchColumn();
$total_matches = $pdo->query("SELECT COUNT(*) FROM matches")->fetchColumn();

// Berita terbaru
$stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 5");
$latest_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manchester Side</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background: linear-gradient(135deg, #DA291C, #6CABDD);
            color: white;
            padding: 1rem 0;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo h1 {
            font-size: 1.5rem;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: bold;
        }
        
        /* Admin Dashboard */
        .admin-dashboard {
            margin: 2rem 0;
        }
        .admin-dashboard h2 {
            margin-bottom: 2rem;
            color: #1e293b;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            border-left: 4px solid #1e293b;
        }
        .stat-card.united {
            border-left-color: #DA291C;
        }
        .stat-card.city {
            border-left-color: #6CABDD;
        }
        .stat-card h3 {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #1e293b;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-primary {
            background: #DA291C;
            color: white;
        }
        .btn-secondary {
            background: #6CABDD;
            color: white;
        }
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        
        /* Articles List */
        .latest-articles {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .articles-list {
            margin-top: 1rem;
        }
        .article-item {
            background: #f8fafc;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            border-left: 4px solid #DA291C;
        }
        .article-item.city {
            border-left-color: #6CABDD;
        }
        .article-item h4 {
            margin-bottom: 0.5rem;
            color: #1e293b;
        }
        .article-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #64748b;
        }
        .team-badge {
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            color: white;
            font-size: 0.8rem;
        }
        .team-badge.united {
            background: #DA291C;
        }
        .team-badge.city {
            background: #6CABDD;
        }
        .article-actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn-edit, .btn-delete {
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-edit {
            background: #10b981;
            color: white;
        }
        .btn-delete {
            background: #ef4444;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">
                <h1>⚽ MANCHESTER SIDE - ADMIN</h1>
            </div>
            <nav>
                <span>Halo, <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?>!</span>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-dashboard">
            <h2>Admin Dashboard</h2>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Berita</h3>
                    <div class="stat-number"><?php echo $total_articles; ?></div>
                </div>
                <div class="stat-card united">
                    <h3>Berita MU</h3>
                    <div class="stat-number"><?php echo $mu_articles; ?></div>
                </div>
                <div class="stat-card city">
                    <h3>Berita City</h3>
                    <div class="stat-number"><?php echo $city_articles; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Jadwal Pertandingan</h3>
                    <div class="stat-number"><?php echo $total_matches; ?></div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="add-article.php" class="btn btn-primary">+ Tambah Berita</a>
                <a href="../index.php" class="btn btn-secondary">Lihat Website</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
            
            <!-- Latest Articles -->
            <div class="latest-articles">
                <h3>Berita Terbaru</h3>
                
                <?php if (empty($latest_articles)): ?>
                    <p>Belum ada berita.</p>
                <?php else: ?>
                    <div class="articles-list">
                        <?php foreach ($latest_articles as $article): ?>
                            <div class="article-item <?php echo $article['team']; ?>">
                                <h4><?php echo htmlspecialchars($article['title']); ?></h4>
                                <div class="article-meta">
                                    <span class="team-badge <?php echo $article['team']; ?>">
                                        <?php echo getTeamName($article['team']); ?>
                                    </span>
                                    <span class="date"><?php echo formatDate($article['created_at']); ?></span>
                                </div>
                                <p><?php echo substr(htmlspecialchars($article['content']), 0, 100); ?>...</p>
                                <div class="article-actions">
                                    <a href="edit-article.php?id=<?php echo $article['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="delete-article.php?id=<?php echo $article['id']; ?>" class="btn-delete" 
                                       onclick="return confirm('Yakin hapus berita ini?')">Hapus</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>