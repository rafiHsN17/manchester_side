<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Database configuration
$host = 'localhost';
$dbname = 'manchester_side';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $team = $_POST['team'];
    $category = $_POST['category'];
    $image_url = '';
    
    // Validate input
    if (empty($title) || empty($content) || empty($team) || empty($category)) {
        $error = "Semua field wajib diisi!";
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = '../uploads/';
            
            // Create uploads directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;
            
            // Check file size (max 2MB)
            if ($_FILES['image']['size'] > 2097152) {
                $error = "Ukuran file terlalu besar. Maksimal 2MB.";
            } else {
                // Check file type
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                $fileExtension = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
                
                if (in_array($fileExtension, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $image_url = 'uploads/' . $fileName;
                    } else {
                        $error = "Gagal upload gambar.";
                    }
                } else {
                    $error = "Hanya file JPG, JPEG, PNG, GIF yang diizinkan.";
                }
            }
        }
        
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO articles (title, content, team, category, image_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $content, $team, $category, $image_url]);
                $success = "Berita berhasil ditambahkan!";
                
                // Clear form
                $_POST = array();
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita - Manchester Side Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-newspaper"></i> Tambah Berita Baru</h1>
            <a href="dashboard.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        
        <div class="admin-form">
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> Judul Berita:</label>
                    <input type="text" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" placeholder="Masukkan judul berita..." required>
                </div>
                
                <div class="form-group">
                    <label for="team"><i class="fas fa-shield-alt"></i> Tim:</label>
                    <select id="team" name="team" required>
                        <option value="">-- Pilih Tim --</option>
                        <option value="manchester-united" <?php echo (isset($_POST['team']) && $_POST['team'] == 'manchester-united') ? 'selected' : ''; ?>>🔴 Manchester United</option>
                        <option value="manchester-city" <?php echo (isset($_POST['team']) && $_POST['team'] == 'manchester-city') ? 'selected' : ''; ?>>🔵 Manchester City</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category"><i class="fas fa-tag"></i> Kategori:</label>
                    <select id="category" name="category" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="news" <?php echo (isset($_POST['category']) && $_POST['category'] == 'news') ? 'selected' : ''; ?>>📰 Berita</option>
                        <option value="transfer" <?php echo (isset($_POST['category']) && $_POST['category'] == 'transfer') ? 'selected' : ''; ?>>🔄 Transfer</option>
                        <option value="injury" <?php echo (isset($_POST['category']) && $_POST['category'] == 'injury') ? 'selected' : ''; ?>>🏥 Cedera</option>
                        <option value="match" <?php echo (isset($_POST['category']) && $_POST['category'] == 'match') ? 'selected' : ''; ?>>⚽ Pertandingan</option>
                        <option value="analysis" <?php echo (isset($_POST['category']) && $_POST['category'] == 'analysis') ? 'selected' : ''; ?>>📊 Analisis</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="content"><i class="fas fa-align-left"></i> Konten Berita:</label>
                    <textarea id="content" name="content" placeholder="Tulis konten berita di sini..." required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image"><i class="fas fa-image"></i> Upload Gambar (Opsional):</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small>Format: JPG, JPEG, PNG, GIF | Maksimal: 2MB</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Tambah Berita
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
