<?php
require_once 'includes/config.php';
require_once 'includes/User.php';

$user = new User($conn);

if($user->isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Yamaha Motor Indonesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="auth-bg">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="auth-card">
                    <div class="auth-header">
                        <img src="images/logo/yamaha-logo.png" alt="Yamaha" height="50">
                        <h3>Selamat Datang</h3>
                        <p>Masuk ke akun Yamaha Anda</p>
                    </div>
                    
                    <?php 
                    if(isset($_GET['error'])) echo '<div class="alert alert-danger">Email atau password salah!</div>';
                    if(isset($_GET['success'])) echo '<div class="alert alert-success">Registrasi berhasil! Silakan login.</div>';
                    ?>
                    
                    <form method="POST" action="auth/login.php" class="auth-form">
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password Anda" required>
                        </div>
                        
                        <button type="submit" class="btn btn-yamaha w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk Sekarang
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Belum punya akun? <a href="register.php" class="auth-link">Daftar Sekarang</a></p>
                        <a href="index.php" class="back-link">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>