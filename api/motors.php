<?php
require_once '../includes/config.php';
require_once '../includes/Motor.php';

header('Content-Type: application/json');

$motor = new Motor($conn);
$category = $_GET['category'] ?? 'all';

if($category === 'all') {
    $motors = $motor->getAllMotors();
} else {
    $motors = $motor->getMotorsByCategory($category);
}

echo json_encode($motors);
?>