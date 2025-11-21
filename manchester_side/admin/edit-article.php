<?php
session_start();
include '../includes/config.php';
include '../includes/auth.php';
include '../includes/functions.php';

// Helper redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    redirect('login.php');
}

if (!isset($_GET['id'])) {
    redirect("dashboard.php");
}

$id = intval($_GET['id']);

// Get article data to delete image
$stmt = $pdo->prepare("SELECT image_url FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if ($article) {
    // Delete image file if exists
    if (!empty($article['image_url'])) {
        $imagePath = '../' . $article['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Delete article from database
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);
}

redirect("dashboard.php");
?>
