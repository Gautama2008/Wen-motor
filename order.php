<?php
require_once 'includes/config.php';
require_once 'includes/User.php';

$user = new User($conn);
if(!$user->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if($user->isAdmin()) {
    header('Location: admin/admin.php');
    exit;
}

if(!isset($_GET['motor_id'])) {
    header('Location: index.php');
    exit;
}

$motor_id = $_GET['motor_id'];

// Get motor details
$query = "SELECT * FROM motors WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $motor_id);
$stmt->execute();
$motor = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$motor) {
    header('Location: index.php');
    exit;
}

// Get stock
$query = "SELECT quantity FROM stock WHERE motor_id = :motor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':motor_id', $motor_id);
$stmt->execute();
$stock = $stmt->fetch(PDO::FETCH_ASSOC);
$available_stock = $stock ? $stock['quantity'] : 0;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    $customer_address = $_POST['customer_address'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if(empty($customer_name) || empty($customer_phone) || empty($customer_address)) {
        $error = "Semua field harus diisi!";
    } elseif($quantity > $available_stock) {
        $error = "Stok tidak mencukupi! Stok tersedia: " . $available_stock;
    } else {
        $total_price = $motor['price'] * $quantity;
        
        // Insert order
        $query = "INSERT INTO orders (user_id, motor_id, quantity, total_price, customer_name, customer_phone, customer_address) 
                  VALUES (:user_id, :motor_id, :quantity, :total_price, :customer_name, :customer_phone, :customer_address)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':motor_id', $motor_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':customer_name', $customer_name);
        $stmt->bindParam(':customer_phone', $customer_phone);
        $stmt->bindParam(':customer_address', $customer_address);
        
        if($stmt->execute()) {
            // Update stock
            $query = "UPDATE stock SET quantity = quantity - :quantity WHERE motor_id = :motor_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':motor_id', $motor_id);
            $stmt->execute();
            
            $success = "Pesanan berhasil dibuat! Kami akan menghubungi Anda segera.";
        } else {
            $error = "Gagal membuat pesanan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Motor - <?= $motor['name'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px 15px 0 0;
        }
        .motor-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-order {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-order:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .order-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card order-card">
                    <div class="order-header text-center">
                        <h3><i class="fas fa-motorcycle me-2"></i>Pesan Motor Yamaha</h3>
                        <p class="mb-0">Lengkapi data di bawah untuk melanjutkan pemesanan</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if(isset($success)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                <h3 class="text-success mt-3">Pesanan Berhasil!</h3>
                                <p class="text-muted"><?= $success ?></p>
                                <a href="index.php" class="btn btn-order btn-lg text-white mt-3">
                                    <i class="fas fa-home me-2"></i>Kembali ke Beranda
                                </a>
                            </div>
                        <?php else: ?>
                        
                        <div class="motor-info">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <img src="<?= $motor['image'] ?>" class="img-fluid rounded-3 shadow-sm" alt="<?= $motor['name'] ?>">
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-primary me-2"><?= strtoupper($motor['category']) ?></span>
                                        <h4 class="mb-0"><?= $motor['name'] ?></h4>
                                    </div>
                                    <p class="text-muted mb-3"><?= $motor['description'] ?></p>
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="text-primary mb-0">Rp <?= number_format($motor['price'], 0, ',', '.') ?></h5>
                                            <small class="text-muted">Harga OTR Jakarta</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-box text-success me-2"></i>
                                                <span class="fw-bold"><?= $available_stock ?> unit tersedia</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold"><i class="fas fa-user me-2"></i>Nama Lengkap</label>
                                        <input type="text" class="form-control" name="customer_name" placeholder="Masukkan nama lengkap" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold"><i class="fas fa-phone me-2"></i>Nomor Telepon</label>
                                        <input type="tel" class="form-control" name="customer_phone" placeholder="08xxxxxxxxxx" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fas fa-map-marker-alt me-2"></i>Alamat Lengkap</label>
                                <textarea class="form-control" name="customer_address" rows="3" placeholder="Masukkan alamat lengkap untuk pengiriman" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold"><i class="fas fa-shopping-cart me-2"></i>Jumlah Unit</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="quantity" value="1" min="1" max="<?= $available_stock ?>" onchange="validateQuantity(this, <?= $available_stock ?>)" required>
                                            <span class="input-group-text">/ <?= $available_stock ?> tersedia</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold"><i class="fas fa-calculator me-2"></i>Total Harga</label>
                                        <div class="form-control bg-light" id="totalPrice">Rp <?= number_format($motor['price'], 0, ',', '.') ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="detail.php?id=<?= $motor['id'] ?>" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-order btn-lg text-white">
                                    <i class="fas fa-paper-plane me-2"></i>Pesan Sekarang
                                </button>
                            </div>
                        </form>
                        
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const motorPrice = <?= $motor['price'] ?>;
        
        function validateQuantity(input, maxStock) {
            let value = parseInt(input.value);
            if (value > maxStock) {
                input.value = maxStock;
                alert('Jumlah tidak boleh melebihi stok yang tersedia (' + maxStock + ' unit)');
            }
            if (value < 1) {
                input.value = 1;
            }
            updateTotalPrice();
        }
        
        function updateTotalPrice() {
            const quantity = document.querySelector('input[name="quantity"]').value;
            const total = motorPrice * quantity;
            document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
        
        // Update total price on quantity change
        document.querySelector('input[name="quantity"]').addEventListener('input', updateTotalPrice);
    </script>
</body>
</html>