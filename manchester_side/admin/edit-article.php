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

// Sanitize function
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = intval($_GET['id']);

// Get article data
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? sanitize($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $team = isset($_POST['team']) ? sanitize($_POST['team']) : '';
    $category = isset($_POST['category']) ? sanitize($_POST['category']) : '';
    
    if (empty($title)) {
        $error = "Judul berita harus diisi.";
    } elseif (empty($content)) {
        $error = "Konten berita harus diisi.";
    } elseif (empty($team)) {
        $error = "Tim harus dipilih.";
    } elseif (empty($category)) {
        $error = "Kategori harus dipilih.";
    }
    
    $image_url = $article['image_url'];
    
    // Handle image upload
    if (empty($error) && isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = '../uploads/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $error = "Ukuran file terlalu besar. Maksimal 5MB.";
        } elseif (in_array($fileExtension, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                if ($article['image_url'] && file_exists('../' . $article['image_url'])) {
                    unlink('../' . $article['image_url']);
                }
                $image_url = 'uploads/' . $fileName;
            } else {
                $error = "Gagal upload gambar.";
            }
        } else {
            $error = "Hanya file JPG, JPEG, PNG, GIF yang diizinkan.";
        }
    }
    
    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, team = ?, category = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$title, $content, $team, $category, $image_url, $id]);
            $success = "Berita berhasil diupdate!";
            
            $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
            $stmt->execute([$id]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Berita - Manchester Side Admin</title>
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
        
        .error-message, .success-message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid var(--error);
            color: var(--error);
        }
        
        .success-message {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid var(--success);
            color: var(--success);
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
        
        .current-image {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
        }
        
        .current-image img {
            max-width: 200px;
            height: auto;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 5px;
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
            <h1><i class="fas fa-edit"></i> Edit Berita</h1>
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
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="team"><i class="fas fa-shield-alt"></i> Tim:</label>
                    <select id="team" name="team" required>
                        <option value="manchester-united" <?php echo $article['team'] == 'manchester-united' ? 'selected' : ''; ?>>🔴 Manchester United</option>
                        <option value="manchester-city" <?php echo $article['team'] == 'manchester-city' ? 'selected' : ''; ?>>🔵 Manchester City</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category"><i class="fas fa-tag"></i> Kategori:</label>
                    <select id="category" name="category" required>
                        <option value="news" <?php echo $article['category'] == 'news' ? 'selected' : ''; ?>>📰 Berita</option>
                        <option value="transfer" <?php echo $article['category'] == 'transfer' ? 'selected' : ''; ?>>🔄 Transfer</option>
                        <option value="injury" <?php echo $article['category'] == 'injury' ? 'selected' : ''; ?>>🏥 Cedera</option>
                        <option value="match" <?php echo $article['category'] == 'match' ? 'selected' : ''; ?>>⚽ Pertandingan</option>
                        <option value="analysis" <?php echo $article['category'] == 'analysis' ? 'selected' : ''; ?>>📊 Analisis</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="content"><i class="fas fa-align-left"></i> Konten Berita:</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($article['content']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image"><i class="fas fa-image"></i> Upload Gambar Baru (Opsional):</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <small>Biarkan kosong jika tidak ingin mengganti gambar. Format: JPG, JPEG, PNG, GIF | Maksimal: 5MB</small>
                    
                    <?php if ($article['image_url']): ?>
                        <div class="current-image">
                            <p><strong>Gambar saat ini:</strong></p>
                            <img src="../<?php echo htmlspecialchars($article['image_url']); ?>" alt="Current image">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Berita
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
