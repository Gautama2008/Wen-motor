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

// Delete stock first
$query = "DELETE FROM stock WHERE motor_id = :motor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':motor_id', $motor_id);
$stmt->execute();

// Delete motor
$query = "DELETE FROM motors WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $motor_id);

if($stmt->execute()) {
    header('Location: admin.php?success=deleted');
} else {
    header('Location: admin.php?error=1');
}
exit;
?>
