<?php
session_start();

// ==================== KONFIGURASI ====================
$host = 'localhost';
$dbname = 'manchester_side';
$username = 'root';
$password = '';

// ==================== FUNGSI HELPER ====================
function redirect($url) {
    header("Location: $url");
    exit();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// ==================== CEK LOGIN ====================
if (isAdminLoggedIn()) {
    redirect('dashboard.php');
}

// ==================== PROSES LOGIN ====================
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // AMAN: Gunakan null coalescing untuk hindari error
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Login sederhana - tanpa hash
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        redirect('dashboard.php');
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Manchester Side</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <strong>⚽ MANCHESTER SIDE</strong>
        </div>
        <h2>Admin Login</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required value="admin">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required value="admin123">
            </div>
            
            <button type="submit" class="btn">Login ke Dashboard</button>
        </form>
        
        <div class="login-info">
            <strong>🔐 Login Default:</strong><br>
            Username: <code>admin</code><br>
            Password: <code>admin123</code>
            <br><br>
            <small>※ Gunakan kredensial di atas untuk login</small>
        </div>
        
        <div style="text-align: center; margin-top: 1rem;">
            <a href="../index.php" style="color: #64748b; text-decoration: none;">
                ← Kembali ke Website
            </a>
        </div>
    </div>

    <script>
        // Focus ke password field setelah username terisi
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            const passwordField = document.getElementById('password');
            
            usernameField.addEventListener('input', function() {
                if (this.value.length > 0) {
                    passwordField.focus();
                }
            });
            
            // Auto submit jika tekan Enter di password field
            passwordField.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    this.form.submit();
                }
            });
        });
    </script>
</body>
</html>
