<?php
require_once '../includes/config.php';
require_once '../includes/User.php';

$user = new User($conn);
if(!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $query = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if($stmt->execute([$id])) {
        header('Location: orders.php?success=deleted');
    } else {
        header('Location: orders.php?error=1');
    }
} else {
    header('Location: orders.php');
}
exit;
?>
