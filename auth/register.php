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
    <title>Register - Yamaha Motor Indonesia</title>
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
                        <h3>Bergabung dengan Yamaha</h3>
                        <p>Buat akun baru untuk memulai</p>
                    </div>
                    
                    <?php 
                    $error = $_GET['error'] ?? '';
                    if($error == 'empty') echo '<div class="alert alert-danger">Semua field harus diisi!</div>';
                    if($error == 'password') echo '<div class="alert alert-danger">Password tidak cocok!</div>';
                    if($error == 'length') echo '<div class="alert alert-danger">Password minimal 6 karakter!</div>';
                    if($error == 'exists') echo '<div class="alert alert-danger">Email sudah terdaftar!</div>';
                    ?>
                    
                    <form method="POST" action="auth/register.php" class="auth-form">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-2"></i>Nama Lengkap
                            </label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama lengkap" required>
                        </div>
                        
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
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Konfirmasi Password
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi password Anda" required>
                        </div>
                        
                        <button type="submit" class="btn btn-yamaha w-100">
                            <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>Sudah punya akun? <a href="login.php" class="auth-link">Masuk Sekarang</a></p>
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