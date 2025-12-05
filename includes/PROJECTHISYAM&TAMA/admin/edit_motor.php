<?php
require_once '../includes/config.php';
require_once '../includes/User.php';

$user = new User($conn);
if(!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: login.php');
    exit;
}

if(!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$motor_id = $_GET['id'];

// Get motor data
$query = "SELECT * FROM motors WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $motor_id);
$stmt->execute();
$motor = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$motor) {
    header('Location: admin.php');
    exit;
}

// Get stock
$query = "SELECT quantity FROM stock WHERE motor_id = :motor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':motor_id', $motor_id);
$stmt->execute();
$stock_data = $stmt->fetch(PDO::FETCH_ASSOC);
$current_stock = $stock_data ? $stock_data['quantity'] : 0;

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
        // Update motor
        $query = "UPDATE motors SET name = :name, description = :description, price = :price, category = :category, image = :image WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':id', $motor_id);
        
        if($stmt->execute()) {
            // Update stock
            $query = "UPDATE stock SET quantity = :quantity WHERE motor_id = :motor_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':quantity', $stock);
            $stmt->bindParam(':motor_id', $motor_id);
            $stmt->execute();
            
            header('Location: admin.php?success=updated');
            exit;
        } else {
            $error = "Gagal mengupdate motor!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Motor - Admin Panel</title>
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
                        <h4><i class="fas fa-edit me-2"></i>Edit Motor</h4>
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
                                        <input type="text" class="form-control" name="name" value="<?= $motor['name'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kategori</label>
                                        <select class="form-control" name="category" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="sport" <?= $motor['category'] == 'sport' ? 'selected' : '' ?>>Sport</option>
                                            <option value="matic" <?= $motor['category'] == 'matic' ? 'selected' : '' ?>>Matic</option>
                                            <option value="bebek" <?= $motor['category'] == 'bebek' ? 'selected' : '' ?>>Bebek</option>
                                            <option value="big-bike" <?= $motor['category'] == 'big-bike' ? 'selected' : '' ?>>Big Bike</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="description" rows="3" required><?= $motor['description'] ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Harga (Rp)</label>
                                        <input type="number" class="form-control" name="price" value="<?= $motor['price'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Stok</label>
                                        <input type="number" class="form-control" name="stock" value="<?= $current_stock ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Path Gambar</label>
                                <input type="text" class="form-control" name="image" value="<?= $motor['image'] ?>" required>
                                <small class="text-muted">Contoh: images/motors/r15.jpg</small>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="admin.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-yamaha">
                                    <i class="fas fa-save me-2"></i>Update Motor
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
