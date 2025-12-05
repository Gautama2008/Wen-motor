<?php
require_once '../includes/config.php';
require_once '../includes/User.php';

$user = new User($conn);
if(!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: login.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $image = $_POST['image'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    
    if(empty($name) || empty($description) || empty($price) || empty($category)) {
        $error = "Semua field harus diisi!";
    } else {
        // Insert motor
        $query = "INSERT INTO motors (name, description, price, category, image) VALUES (:name, :description, :price, :category, :image)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':image', $image);
        
        if($stmt->execute()) {
            $motor_id = $conn->lastInsertId();
            
            // Insert stock
            $query = "INSERT INTO stock (motor_id, quantity) VALUES (:motor_id, :quantity)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':motor_id', $motor_id);
            $stmt->bindParam(':quantity', $stock);
            $stmt->execute();
            
            header('Location: admin.php?success=added');
            exit;
        } else {
            $error = "Gagal menambah motor!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Motor - Admin Panel</title>
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
                        <h4><i class="fas fa-plus me-2"></i>Tambah Motor Baru</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Motor</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kategori</label>
                                        <select class="form-control" name="category" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="sport">Sport</option>
                                            <option value="matic">Matic</option>
                                            <option value="bebek">Bebek</option>
                                            <option value="big-bike">Big Bike</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="description" rows="3" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Harga (Rp)</label>
                                        <input type="number" class="form-control" name="price" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Stok Awal</label>
                                        <input type="number" class="form-control" name="stock" value="10" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Path Gambar</label>
                                <input type="text" class="form-control" name="image" placeholder="images/motors/nama-motor.jpg" required>
                                <small class="text-muted">Contoh: images/motors/r15.jpg</small>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="admin.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-yamaha">
                                    <i class="fas fa-save me-2"></i>Simpan Motor
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>