<?php
require_once '../includes/config.php';
require_once '../includes/User.php';
require_once '../includes/Motor.php';

$user = new User($conn);
if(!$user->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if(!$user->isAdmin()) {
    header('Location: index.php');
    exit;
}

$motor = new Motor($conn);
$motors = $motor->getAllMotors();

// Get stock data for each motor
$query = "SELECT motor_id, quantity FROM stock";
$stmt = $conn->prepare($query);
$stmt->execute();
$stock_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Get total users
$query = "SELECT COUNT(*) FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$total_users = $stmt->fetchColumn();

// Get pending orders
$query = "SELECT COUNT(*) FROM orders WHERE status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->execute();
$pending_orders = $stmt->fetchColumn();

// Get sales this month
$query = "SELECT COUNT(*) FROM orders WHERE status = 'completed' AND MONTH(order_date) = MONTH(CURRENT_DATE()) AND YEAR(order_date) = YEAR(CURRENT_DATE())";
$stmt = $conn->prepare($query);
$stmt->execute();
$monthly_sales = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Yamaha Motor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css?v=2" rel="stylesheet">
    <style>
        .card-bodytext-center i {
            background: rgba(255, 255, 255, 0.2);
        }
        
    </style>
</head>
<body>
    <nav class="navbar admin-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <i class="fas fa-tachometer-alt me-3 fs-4"></i>
                <span class="navbar-brand mb-0 h1">Admin Panel</span>
            </div>
            <div class="d-flex align-items-center">
                <a href="../index.php" class="btn btn-light btn-sm me-2 text-dark">
                    <i class="fas fa-home me-2"></i>Beranda
                </a>
                <a href="../auth/logout.php" class="btn btn-light btn-sm text-dark">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if(isset($_GET['success'])): ?>
            <?php 
            $messages = [
                'added' => 'Motor berhasil ditambahkan!',
                'updated' => 'Motor berhasil diupdate!',
                'deleted' => 'Motor berhasil dihapus!'
            ];
            $message = $messages[$_GET['success']] ?? 'Operasi berhasil!';
            ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-chart-line me-3"></i>Dashboard Admin
                </h2>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card admin-card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-motorcycle fa-2x mb-3"></i>
                        <h5>Total Motor</h5>
                        <h2 class="fw-bold"><?= count($motors) ?></h2>
                        <small>Unit Tersedia</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-3"></i>
                        <h5>Total User</h5>
                        <h2 class="fw-bold"><?= $total_users ?></h2>
                        <small>Pengguna Terdaftar</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                        <h5>Pesanan</h5>
                        <h2 class="fw-bold"><?= $pending_orders ?></h2>
                        <small>Pesanan Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-bar fa-2x mb-3"></i>
                        <h5>Penjualan</h5>
                        <h2 class="fw-bold"><?= $monthly_sales ?></h2>
                        <small>Bulan Ini</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Daftar Motor
                </h5>
                <div>
                    <a href="orders.php" class="btn btn-success btn-sm me-2">
                        <i class="fas fa-shopping-cart me-2"></i>Kelola Orders
                    </a>
                    <a href="restore_motors.php" class="btn btn-info btn-sm text-white me-2">
                        <i class="fas fa-undo me-2"></i>Restore Motor
                    </a>
                    <a href="add_motor.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Tambah Motor
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Motor</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($motors as $m): ?>
                            <tr>
                                <td><span class="badge bg-secondary">#<?= $m['id'] ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../<?= $m['image'] ?>" alt="<?= $m['name'] ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0"><?= $m['name'] ?></h6>
                                            <small class="text-muted"><?= substr($m['description'], 0, 50) ?>...</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= strtoupper($m['category']) ?></span>
                                </td>
                                <td class="fw-bold text-success">Rp <?= number_format($m['price'], 0, ',', '.') ?></td>
                                <td>
                                    <?php 
                                    $stock_qty = $stock_data[$m['id']] ?? 0;
                                    $badge_class = $stock_qty > 5 ? 'bg-success' : ($stock_qty > 0 ? 'bg-warning' : 'bg-danger');
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= $stock_qty ?> unit</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">

                                        <a href="edit_motor.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-warning" title="Edit Motor">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <a href="delete_motor.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus Motor" onclick="return confirm('Yakin ingin menghapus motor <?= $m['name'] ?>?')">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>