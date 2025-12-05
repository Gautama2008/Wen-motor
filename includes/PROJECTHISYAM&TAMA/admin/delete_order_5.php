<?php
require_once '../includes/config.php';

// Delete order ID 5
$query = "DELETE FROM orders WHERE id = 5";
$stmt = $conn->prepare($query);

if($stmt->execute()) {
    echo "Order ID #5 berhasil dihapus!";
} else {
    echo "Gagal menghapus order!";
}
?>
