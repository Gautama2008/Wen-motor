<?php
require_once '../includes/config.php';

$query = "UPDATE motors SET category = 'sport' WHERE name = 'Yamaha Vixion'";
$stmt = $conn->prepare($query);

if($stmt->execute()) {
    header('Location: admin.php?success=updated');
} else {
    header('Location: admin.php?error=1');
}
?>
