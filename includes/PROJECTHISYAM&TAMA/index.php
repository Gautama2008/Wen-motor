<?php
require_once 'includes/config.php';
require_once 'includes/User.php';
require_once 'includes/Motor.php';

$user = new User($conn);
$motor = new Motor($conn);
$motors = $motor->getAllMotors();

// Get user orders if logged in
$userOrders = [];
if($user->isLoggedIn() && !$user->isAdmin()) {
    $query = "SELECT o.*, m.name as motor_name, m.image FROM orders o 
              JOIN motors m ON o.motor_id = m.id 
              WHERE o.user_id = :user_id 
              ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $userOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yamaha Motor Indonesia - Dealer Resmi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo/yamaha-logo.png" alt="Yamaha">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#motors">Motor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Kontak</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if($user->isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?= $_SESSION['user_name'] ?? 'User' ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if($user->isAdmin()): ?>
                                <li><a class="dropdown-item" href="admin/admin.php">Admin Panel</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="auth/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Revs Your Heart</h1>
                    <p class="lead mb-4">Temukan motor Yamaha terbaru dengan teknologi terdepan dan desain yang memukau. Wujudkan perjalanan impian Anda bersama Yamaha.</p>
                    <a href="#motors" class="btn btn-primary btn-lg">Lihat Motor</a>
                </div>
                <div class="col-lg-6">
                    <img src="images/hero/hero-bike.jpg" class="img-fluid" alt="Yamaha Motor">
                </div>
            </div>
        </div>
    </section>

    <!-- Category Navigation -->
    <section class="category-nav">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center flex-wrap gap-2">
                        <button class="btn btn-outline-primary category-btn active" data-category="all">Semua</button>
                        <button class="btn btn-outline-primary category-btn" data-category="sport">Sport</button>
                        <button class="btn btn-outline-primary category-btn" data-category="matic">Matic</button>
                        <button class="btn btn-outline-primary category-btn" data-category="bebek">Bebek</button>
                        <button class="btn btn-outline-primary category-btn" data-category="big-bike">Big Bike</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- All Motors -->
    <section id="motors" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Daftar Motor Yamaha</h2>
            <div class="row" id="motorsContainer">
                <?php foreach($motors as $motorItem): ?>
                <div class="col-md-4 mb-4 motor-item" data-category="<?= $motorItem['category'] ?>">
                    <div class="card motor-card">
                        <img src="<?= $motorItem['image'] ?>" class="card-img-top" alt="<?= $motorItem['name'] ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= $motorItem['name'] ?></h5>
                            <p class="card-text"><?= substr($motorItem['description'], 0, 80) ?>...</p>
                            <p class="text-primary fw-bold">Rp <?= number_format($motorItem['price'], 0, ',', '.') ?></p>
                            <a href="detail.php?id=<?= $motorItem['id'] ?>" class="btn btn-primary btn-sm">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Hubungi Kami</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Informasi Kontak</h5>
                            <p><i class="fab fa-whatsapp text-success"></i> +62 821-2866-8284</p>
                            <p><i class="fas fa-phone text-primary"></i> (021) 1234-5678</p>
                            <p><i class="fas fa-envelope text-primary"></i> info@yamaha-motor.co.id</p>
                            <p><i class="fas fa-map-marker-alt text-primary"></i> Bandung, Indonesia</p>
                            <a href="https://wa.me/6282128668284" target="_blank" class="btn btn-success btn-sm mt-2">
                                <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <?php if($user->isLoggedIn() && !$user->isAdmin()): ?>
                                <h5><i class="fas fa-box me-2"></i>Pesanan Saya</h5>
                                <?php if(empty($userOrders)): ?>
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Belum ada pesanan
                                    </div>
                                <?php else: ?>
                                    <div style="max-height: 400px; overflow-y: auto;" class="mt-3">
                                        <?php foreach($userOrders as $order): 
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'processing' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusIcon = [
                                                'pending' => 'clock',
                                                'confirmed' => 'check',
                                                'processing' => 'cog',
                                                'completed' => 'check-circle',
                                                'cancelled' => 'times-circle'
                                            ];
                                        ?>
                                        <div class="card mb-2">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= $order['image'] ?>" alt="<?= $order['motor_name'] ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded me-2">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0" style="font-size: 0.9rem;"><?= $order['motor_name'] ?></h6>
                                                        <small class="text-muted"><?= date('d/m/Y', strtotime($order['order_date'])) ?></small>
                                                        <div class="mt-1">
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-shopping-cart me-1"></i>Jumlah: <?= $order['quantity'] ?? 1 ?> unit
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <span class="badge bg-<?= $statusClass[$order['status']] ?>">
                                                        <i class="fas fa-<?= $statusIcon[$order['status']] ?> me-1"></i><?= ucfirst($order['status']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <a href="https://wa.me/6282128668284?text=Halo, saya ingin menanyakan status pesanan" target="_blank" class="btn btn-success w-100 mt-3">
                                    <i class="fab fa-whatsapp me-2"></i>Hubungi Admin
                                </a>
                            <?php else: ?>
                                <h5>Kirim Pesan</h5>
                                <form method="POST" action="send_message.php">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="name" placeholder="Nama" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                                    </div>
                                    <div class="mb-3">
                                        <textarea class="form-control" name="message" rows="3" placeholder="Pesan" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-yamaha">
                                        <i class="fab fa-whatsapp me-2"></i>Kirim via WhatsApp
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Yamaha Motor Indonesia</h5>
                    <p>Dealer resmi motor Yamaha dengan layanan terbaik dan produk berkualitas tinggi.</p>
                </div>
                <div class="col-md-4">
                    <h5>Kontak</h5>
                    <p><i class="fab fa-whatsapp"></i> +62 821-2866-8284</p>
                    <p><i class="fas fa-phone"></i> (021) 1234-5678</p>
                    <p><i class="fas fa-envelope"></i> info@yamaha-motor.co.id</p>
                </div>
                <div class="col-md-4">
                    <h5>Ikuti Kami</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube fa-2x"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2024 Yamaha Motor Indonesia. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
<script src="js/main.js"></script>
<script src="js/main.js"></script>
<script src="js/main.js"></script>
</body>
</html>