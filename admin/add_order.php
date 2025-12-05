<?php
require_once '../includes/config.php';
require_once '../includes/User.php';

$user = new User($conn);
if(!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../index.php');
    exit;
}

// Get all motors
$query = "SELECT * FROM motors WHERE deleted_at IS NULL";
$stmt = $conn->prepare($query);
$stmt->execute();
$motors = $stmt->fetchAll();

// Get all users
$query = "SELECT id, name, email FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $motor_id = $_POST['motor_id'];
    $quantity = $_POST['quantity'];
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $customer_address = $_POST['customer_address'];
    $status = $_POST['status'];
    
    // Get motor price
    $query = "SELECT price FROM motors WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$motor_id]);
    $motor = $stmt->fetch();
    $total_price = $motor['price'] * $quantity;
    
    // Insert order
    $query = "INSERT INTO orders (user_id, motor_id, quantity, total_price, customer_name, customer_phone, customer_address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if($stmt->execute([$user_id, $motor_id, $quantity, $total_price, $customer_name, $customer_phone, $customer_address, $status])) {
        header('Location: orders.php?success=added');
        exit;
    } else {
        $error = "Gagal menambahkan order!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Order - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar admin-header">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Tambah Order Baru</span>
            <a href="orders.php" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Pilih User</option>
                                <?php foreach($users as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= $u['name'] ?> (<?= $u['email'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Motor</label>
                            <select name="motor_id" class="form-select" required>
                                <option value="">Pilih Motor</option>
                                <?php foreach($motors as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['name'] ?> - Rp <?= number_format($m['price'], 0, ',', '.') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Customer</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="customer_phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="customer_address" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-yamaha">
                        <i class="fas fa-save me-2"></i>Simpan Order
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
