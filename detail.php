<?php
require_once 'includes/config.php';
require_once 'includes/User.php';
require_once 'includes/Motor.php';

$user = new User($conn);
$motor = new Motor($conn);

$motorId = $_GET['id'] ?? 0;
$motorDetail = $motor->getMotorById($motorId);

if(!$motorDetail) {
    header('Location: index.php');
    exit;
}

// Get stock info
$query = "SELECT quantity FROM stock WHERE motor_id = :motor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':motor_id', $motorId);
$stmt->execute();
$stock = $stmt->fetch(PDO::FETCH_ASSOC);
$available_stock = $stock ? $stock['quantity'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $motorDetail['name'] ?> - Yamaha Motor Indonesia</title>
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
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </nav>

    <!-- Motor Detail -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <img src="<?= $motorDetail['image'] ?>" class="img-fluid rounded" alt="<?= $motorDetail['name'] ?>">
                </div>
                <div class="col-lg-6">
                    <span class="badge bg-primary text-uppercase mb-2"><?= $motorDetail['category'] ?></span>
                    <h1 class="mb-3"><?= $motorDetail['name'] ?></h1>
                    <p class="lead"><?= $motorDetail['description'] ?></p>
                    
                    <div class="mb-4">
                        <h3 class="text-primary">Rp <?= number_format($motorDetail['price'], 0, ',', '.') ?></h3>
                        <small class="text-muted">*Harga OTR Jakarta</small>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-1"><strong>Stok Tersedia:</strong> 
                            <span class="badge <?= $available_stock > 0 ? 'bg-success' : 'bg-danger' ?>">
                                <?= $available_stock ?> unit
                            </span>
                        </p>
                    </div>
                    
                    <?php if(!$user->isAdmin()): ?>
                    <div class="d-grid gap-2">
                        <?php if($user->isLoggedIn() && $available_stock > 0): ?>
                            <a href="order.php?motor_id=<?= $motorDetail['id'] ?>" class="btn btn-success btn-lg">
                                <i class="fas fa-shopping-cart"></i> Pesan Sekarang
                            </a>
                        <?php elseif(!$user->isLoggedIn()): ?>
                            <a href="login.php" class="btn btn-warning btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Login untuk Pesan
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="fas fa-times"></i> Stok Habis
                            </button>
                        <?php endif; ?>
                        
                        <a href="https://wa.me/6282128668284?text=Halo,%20saya%20tertarik%20dengan%20<?= urlencode($motorDetail['name']) ?>%20seharga%20Rp%20<?= number_format($motorDetail['price'], 0, ',', '.') ?>.%20Bisa%20info%20lebih%20lanjut?" target="_blank" class="btn btn-success btn-lg">
                            <i class="fab fa-whatsapp"></i> Chat WhatsApp
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>