<?php
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if(empty($name) || empty($email) || empty($message)) {
        header('Location: index.php#contact?error=empty');
        exit;
    }
    
    // Format pesan untuk WhatsApp
    $whatsapp_message = "Pesan dari Website Yamaha:\n\n";
    $whatsapp_message .= "Nama: " . $name . "\n";
    $whatsapp_message .= "Email: " . $email . "\n";
    $whatsapp_message .= "Pesan: " . $message;
    
    // Redirect ke WhatsApp
    $whatsapp_url = "https://wa.me/6282128668284?text=" . urlencode($whatsapp_message);
    header('Location: ' . $whatsapp_url);
    exit;
}
?>