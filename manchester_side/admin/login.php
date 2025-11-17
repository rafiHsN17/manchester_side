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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #DA291C, #6CABDD);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 90%;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            border: 2px solid #e2e8f0;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #DA291C;
            outline: none;
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #DA291C, #6CABDD);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
            font-weight: bold;
            transition: opacity 0.3s;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .error {
            background: #fee;
            color: #c33;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border: 1px solid #fcc;
            text-align: center;
        }
        .login-info {
            background: #f0f8ff;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            font-size: 0.9rem;
            border-left: 4px solid #6CABDD;
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }
        .logo {
            text-align: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
    </style>
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