<?php
include '../includes/config.php';
include '../includes/auth.php';

if (!isset($_GET['id'])) {
    redirect('dashboard.php');
}

$id = intval($_GET['id']);

// Get article data to delete image
$stmt = $pdo->prepare("SELECT image_url FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if ($article) {
    // Delete image file if exists
    if ($article['image_url'] && file_exists($article['image_url'])) {
        unlink($article['image_url']);
    }
    
    // Delete article from database
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);
}

redirect('dashboard.php');
?>