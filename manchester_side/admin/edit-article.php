<?php
include '../includes/config.php';
include '../includes/auth.php';

// Sanitize function
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if (!isset($_GET['id'])) {
    redirect('dashboard.php');
}

$id = intval($_GET['id']);

// Get article data
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $title = isset($_POST['title']) ? sanitize($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $team = isset($_POST['team']) ? sanitize($_POST['team']) : '';
    $category = isset($_POST['category']) ? sanitize($_POST['category']) : '';
    
    // Basic validation
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
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        
        // Check file size (max 5MB)
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $error = "Ukuran file terlalu besar. Maksimal 5MB.";
        } elseif (in_array($fileExtension, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                // Delete old image if exists
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
            // Refresh article data
            $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
            $stmt->execute([$id]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="admin-form">
    <h2>Edit Berita</h2>
    
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success-message"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Judul Berita:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="team">Tim:</label>
            <select id="team" name="team" required>
                <option value="manchester-united" <?php echo $article['team'] == 'manchester-united' ? 'selected' : ''; ?>> Manchester United</option>
                <option value="manchester-city" <?php echo $article['team'] == 'manchester-city' ? 'selected' : ''; ?>> Manchester City</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="category">Kategori:</label>
            <select id="category" name="category" required>
                <option value="news" <?php echo $article['category'] == 'news' ? 'selected' : ''; ?>>Berita</option>
                <option value="transfer" <?php echo $article['category'] == 'transfer' ? 'selected' : ''; ?>>Transfer</option>
                <option value="injury" <?php echo $article['category'] == 'injury' ? 'selected' : ''; ?>>Cedera</option>
                <option value="match" <?php echo $article['category'] == 'match' ? 'selected' : ''; ?>>Pertandingan</option>
                <option value="analysis" <?php echo $article['category'] == 'analysis' ? 'selected' : ''; ?>>Analisis</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="content">Konten Berita:</label>
            <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($article['content']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="image">Upload Gambar Baru:</label>
            <input type="file" id="image" name="image" accept="image/*">
            <small>Biarkan kosong jika tidak ingin mengganti gambar</small>
            <?php if ($article['image_url']): ?>
                <div class="current-image">
                    <p>Gambar saat ini:</p>
                    <img src="../<?php echo htmlspecialchars($article['image_url']); ?>" alt="Current image" style="max-width: 200px; height: auto; border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Berita</button>
            <a href="dashboard.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>