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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --united-red: #DA291C;
            --city-blue: #6CABDD;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
            --success: #10B981;
            --error: #EF4444;
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
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .admin-header {
            background: rgba(255,255,255,0.05);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-header h1 {
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--united-red), var(--city-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .btn-back {
            background: rgba(255,255,255,0.1);
            color: var(--light);
            padding: 0.7rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-back:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .admin-form {
            background: rgba(255,255,255,0.05);
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        
        .admin-form h2 {
            margin-bottom: 2rem;
            font-size: 2rem;
            text-align: center;
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid var(--error);
            color: var(--error);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .success-message {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid var(--success);
            color: var(--success);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--light);
        }
        
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: var(--light);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input[type="text"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--city-blue);
            background: rgba(255,255,255,0.08);
        }
        
        .form-group textarea {
            resize: vertical;
            font-family: inherit;
            min-height: 200px;
        }
        
        .form-group input[type="file"] {
            width: 100%;
            padding: 0.8rem;
            background: rgba(255,255,255,0.05);
            border: 1px dashed rgba(255,255,255,0.2);
            border-radius: 10px;
            color: var(--light);
            cursor: pointer;
        }
        
        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: var(--gray);
            font-size: 0.85rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--united-red), var(--city-blue));
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(218, 41, 28, 0.3);
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: var(--light);
            flex: 1;
        }
        
        .btn-secondary:hover {
            background: rgba(255,255,255,0.2);
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .admin-form {
                padding: 1.5rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
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