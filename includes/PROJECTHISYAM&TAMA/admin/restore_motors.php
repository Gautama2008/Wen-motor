<?php
require_once '../includes/config.php';
require_once '../includes/User.php';

$user = new User($conn);
if(!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: login.php');
    exit;
}

// Data motor default
$default_motors = [
    ['name' => 'Yamaha R15', 'description' => 'Motor sport dengan performa tinggi dan desain aerodinamis', 'price' => 35000000, 'category' => 'sport', 'image' => 'images/motors/r15.jpg', 'stock' => 15],
    ['name' => 'Yamaha NMAX', 'description' => 'Skutik premium dengan teknologi Blue Core', 'price' => 32000000, 'category' => 'matic', 'image' => 'images/motors/nmax.jpg', 'stock' => 20],
    ['name' => 'Yamaha Vixion', 'description' => 'Motor bebek sport dengan mesin bertenaga', 'price' => 25000000, 'category' => 'bebek', 'image' => 'images/motors/vixion.jpg', 'stock' => 12],
    ['name' => 'Yamaha MT-25', 'description' => 'Naked bike dengan karakter agresif', 'price' => 55000000, 'category' => 'sport', 'image' => 'images/motors/mt25.jpg', 'stock' => 8],
    ['name' => 'Yamaha Aerox', 'description' => 'Skutik sporty untuk anak muda', 'price' => 28000000, 'category' => 'matic', 'image' => 'images/motors/aerox.jpg', 'stock' => 18],
    ['name' => 'Yamaha Jupiter MX', 'description' => 'Motor bebek sport legendaris', 'price' => 20000000, 'category' => 'bebek', 'image' => 'images/motors/jupiter-mx.jpg', 'stock' => 10],
    ['name' => 'Yamaha R25', 'description' => 'Sport bike dengan teknologi terdepan', 'price' => 45000000, 'category' => 'sport', 'image' => 'images/motors/r25.jpg', 'stock' => 6],
    ['name' => 'Yamaha Lexi', 'description' => 'Skutik stylish untuk mobilitas harian', 'price' => 22000000, 'category' => 'matic', 'image' => 'images/motors/lexi.jpg', 'stock' => 25],
    ['name' => 'Yamaha MT-09', 'description' => 'Big bike dengan performa luar biasa', 'price' => 250000000, 'category' => 'big-bike', 'image' => 'images/motors/mt09.jpg', 'stock' => 3]
];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $restored = 0;
    
    foreach($default_motors as $motor) {
        // Check if motor already exists
        $query = "SELECT id FROM motors WHERE name = :name";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $motor['name']);
        $stmt->execute();
        
        if($stmt->rowCount() == 0) {
            // Insert motor
            $query = "INSERT INTO motors (name, description, price, category, image) VALUES (:name, :description, :price, :category, :image)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name', $motor['name']);
            $stmt->bindParam(':description', $motor['description']);
            $stmt->bindParam(':price', $motor['price']);
            $stmt->bindParam(':category', $motor['category']);
            $stmt->bindParam(':image', $motor['image']);
            
            if($stmt->execute()) {
                $motor_id = $conn->lastInsertId();
                
                // Insert stock
                $query = "INSERT INTO stock (motor_id, quantity) VALUES (:motor_id, :quantity)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':motor_id', $motor_id);
                $stmt->bindParam(':quantity', $motor['stock']);
                $stmt->execute();
                
                $restored++;
            }
        }
    }
    
    $success = "$restored motor berhasil di-restore!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restore Motor - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar admin-header">
        <div class="container">
            <a href="admin.php" class="text-white text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Admin Panel
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card admin-card">
                    <div class="card-header">
                        <h4><i class="fas fa-undo me-2"></i>Restore Motor yang Terhapus</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i><?= $success ?>
                                <div class="mt-3">
                                    <a href="admin.php" class="btn btn-yamaha">Lihat Daftar Motor</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <p>Fitur ini akan mengembalikan motor yang terhapus ke database dengan data default.</p>
                            <p class="text-muted">Motor yang sudah ada tidak akan di-restore ulang.</p>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Motor yang akan di-restore:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach($default_motors as $m): ?>
                                        <li><?= $m['name'] ?> - <?= $m['category'] ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <form method="POST">
                                <div class="d-flex justify-content-between">
                                    <a href="admin.php" class="btn btn-secondary">Batal</a>
                                    <button type="submit" class="btn btn-yamaha" onclick="return confirm('Yakin ingin restore motor yang terhapus?')">
                                        <i class="fas fa-undo me-2"></i>Restore Motor
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
