<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    $whatsappNumber = '6282128668284';
    
    $text = "Halo, saya *{$name}*\n\n";
    $text .= "Email: {$email}\n\n";
    $text .= "Pesan:\n{$message}";
    
    $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($text);
    
    header("Location: {$whatsappUrl}");
    exit;
}
?>
