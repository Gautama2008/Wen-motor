<?php
require_once '../includes/config.php';
require_once '../includes/User.php';

$user = new User($conn);
if(!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if(isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    
    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if($stmt->execute([$status, $id])) {
        header('Location: orders.php?success=updated');
    } else {
        header('Location: orders.php?error=1');
    }
} else {
    header('Location: orders.php');
}
exit;
?>
