<?php
session_start();

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

// Redirect if already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: profile.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password_input = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $favorite_team = $_POST['favorite_team'];
    
    // Validation
    if (empty($name) || empty($email) || empty($password_input) || empty($confirm_password) || empty($favorite_team)) {
        $error = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($password_input) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($password_input !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = "Email sudah terdaftar!";
            } else {
                // Hash password
                $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, favorite_team, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $email, $hashed_password, $favorite_team]);
                
                header('Location: login.php?registered=1');
                exit;
            }
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
    <title>Register - Manchester Side</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --united-red: #DA291C;
            --city-blue: #6CABDD;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .register-container {
            max-width: 500px;
            width: 100%;
            background: rgba(255,255,255,0.05);
            padding: 3rem;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo h1 {
            font-size: 2rem;
            background: linear-gradient(135deg, var(--united-red), var(--city-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        
        .logo p {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        h2 {
            text-align: center;
            color: var(--light);
            margin-bottom: 2rem;
            font-size: 1.8rem;
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
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--light);
            font-weight: 600;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.9rem 1rem 0.9rem 3rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: var(--light);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group select {
            cursor: pointer;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--city-blue);
            background: rgba(255,255,255,0.08);
        }
        
        .team-select {
            position: relative;
        }
        
        .team-option {
            padding: 0.5rem;
        }
        
        .info-text {
            font-size: 0.85rem;
            color: var(--gray);
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--united-red), var(--city-blue));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(218, 41, 28, 0.3);
        }
        
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: rgba(255,255,255,0.1);
        }
        
        .divider span {
            background: rgba(255,255,255,0.05);
            padding: 0 1rem;
            color: var(--gray);
            position: relative;
            z-index: 1;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .login-link a {
            color: var(--city-blue);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .login-link a:hover {
            color: var(--united-red);
        }
        
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
        
        .back-link a {
            color: var(--gray);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .back-link a:hover {
            color: var(--light);
        }
        
        @media (max-width: 768px) {
            .register-container {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h1><i class="fas fa-futbol"></i> MANCHESTER SIDE</h1>
            <p>Bergabunglah dengan Komunitas Fans Manchester</p>
        </div>
        
        <h2>Daftar Akun</h2>
        
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Nama Lengkap:</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Masukkan email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password:</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="favorite_team">Tim Favorit:</label>
                <div class="input-wrapper team-select">
                    <i class="fas fa-shield-alt"></i>
                    <select id="favorite_team" name="favorite_team" required>
                        <option value="">-- Pilih Tim Favorit --</option>
                        <option value="manchester-united" <?php echo (isset($_POST['favorite_team']) && $_POST['favorite_team'] == 'manchester-united') ? 'selected' : ''; ?>>🔴 Manchester United</option>
                        <option value="manchester-city" <?php echo (isset($_POST['favorite_team']) && $_POST['favorite_team'] == 'manchester-city') ? 'selected' : ''; ?>>🔵 Manchester City</option>
                    </select>
                </div>
                <p class="info-text">
                    <i class="fas fa-info-circle"></i>
                    <span>Tim favorit tidak dapat diubah setelah registrasi</span>
                </p>
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </button>
        </form>
        
        <div class="divider">
            <span>atau</span>
        </div>
        
        <div class="login-link">
            <p style="color: var(--gray); margin-bottom: 0.5rem;">Sudah punya akun?</p>
            <a href="login.php">
                <i class="fas fa-sign-in-alt"></i> Login Disini
            </a>
        </div>
        
        <div class="back-link">
            <a href="index.php">
                <i class="fas fa-arrow-left"></i> Kembali ke Home
            </a>
        </div>
    </div>
</body>
</html>