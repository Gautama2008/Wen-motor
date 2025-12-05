<?php
require_once '../includes/config.php';
require_once '../includes/User.php';

$user = new User($conn);
if(!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../index.php');
    exit;
}

// Get all orders
$query = "SELECT o.*, m.name as motor_name, u.name as user_name 
          FROM orders o 
          JOIN motors m ON o.motor_id = m.id 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.order_date DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Orders - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar admin-header">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Kelola Orders</span>
            <div>
                <a href="admin.php" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                $messages = [
                    'deleted' => 'Order berhasil dihapus!',
                    'added' => 'Order berhasil ditambahkan!',
                    'updated' => 'Status order berhasil diupdate!'
                ];
                echo $messages[$_GET['success']] ?? 'Operasi berhasil!';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Motor</th>
                                <th>Unit</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $o): ?>
                            <tr>
                                <td>#<?= $o['id'] ?></td>
                                <td>
                                    <strong><?= $o['customer_name'] ?></strong><br>
                                    <small><?= $o['customer_phone'] ?></small>
                                </td>
                                <td><?= $o['motor_name'] ?></td>
                                <td><?= $o['quantity'] ?></td>
                                <td>Rp <?= number_format($o['total_price'], 0, ',', '.') ?></td>
                                <td>
                                    <?php
                                    $badge = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'processing' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    ?>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-<?= $badge[$o['status']] ?> dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <?= ucfirst($o['status']) ?>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="update_status.php?id=<?= $o['id'] ?>&status=pending">Pending</a></li>
                                            <li><a class="dropdown-item" href="update_status.php?id=<?= $o['id'] ?>&status=confirmed">Confirmed</a></li>
                                            <li><a class="dropdown-item" href="update_status.php?id=<?= $o['id'] ?>&status=processing">Processing</a></li>
                                            <li><a class="dropdown-item" href="update_status.php?id=<?= $o['id'] ?>&status=completed">Completed</a></li>
                                            <li><a class="dropdown-item" href="update_status.php?id=<?= $o['id'] ?>&status=cancelled">Cancelled</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td><?= date('d/m/Y', strtotime($o['order_date'])) ?></td>
                                <td>
                                    <a href="delete_order.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus order ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
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
