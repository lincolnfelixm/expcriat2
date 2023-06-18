<?php
// Include the database connection file
include 'conn.php';
// Include PHPMailer library
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$encryptedEmail = $_POST['email'];
$encryptionKey = $_POST['encryptionKey'];

// Fetch the private key from a file (assuming it's stored in privateKey.txt)
$privateKey = file_get_contents('serverPrivateKey.pem');

// Decrypt the encryption key using the private key
openssl_private_decrypt(base64_decode($encryptionKey), $decryptedEncryptionKey, $privateKey);

// Decrypt the email using the decrypted encryption key
$decryptedEmail = openssl_decrypt(base64_decode($encryptedEmail), 'AES-128-ECB', $decryptedEncryptionKey, OPENSSL_RAW_DATA);

// Check if the email is valid
if (!filter_var($decryptedEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Invalid email address';
    exit();
}

// Check if the email exists in the database
$query = "SELECT * FROM users WHERE email = '$decryptedEmail'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    http_response_code(404);
    echo 'Email not found.';
    exit();
}

// Generate a token
$token = bin2hex(openssl_random_pseudo_bytes(16));

// Save the token in the database
$query = "UPDATE users SET token = '$token' WHERE email = '$decryptedEmail'";
$result = mysqli_query($conn, $query);

if (!$result) {
    http_response_code(500);
    echo 'Failed to save token.';
    exit();
}

// Create a new PHPMailer instance
$mail = new PHPMailer();

// SMTP Configuration
$mail->isSMTP();
$mail->Host = 'smtp.example.com'; // Replace with your SMTP server address
$mail->SMTPAuth = true;
$mail->Username = 'lincoln.felixm@gmail.com'; // Replace with your email address
$mail->Password = 'cekwdxubuczixrrq'; // Replace with your email password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

// Sender and recipient settings
$mail->setFrom('noreply@wecare.com', 'WeCare Health');
$mail->addAddress($decryptedEmail);

// Email content
$mail->isHTML(true);
$mail->Subject = 'Email Confirmation';
$mail->Body = 'Your token to recover your account is <b>' . $token . '</b>';

// Try to send the email
if ($mail->send()) {
    // Send an HTTP 200 response
    http_response_code(200);
    echo 'Email confirmation sent.';
} else {
    // If the email sending fails, send an HTTP 500 response
    http_response_code(500);
    echo 'Failed to send the email confirmation.';
}
?>
