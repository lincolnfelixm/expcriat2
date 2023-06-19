<?php
// Include the database connection file
include 'conn.php';
// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer-master/src/Exception.php'; 
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$data = json_decode(file_get_contents('php://input'), true);
$encryptedEmail = $data['email'];

// Fetch the private key from a file (assuming it's stored in privateKey.txt)
$privateKey = file_get_contents('serverPrivateKey.pem');

// Decrypt the email using the decrypted encryption key
$decryptedEmail = openssl_decrypt(base64_decode($encryptedEmail), 'AES-128-ECB', $privateKey, OPENSSL_RAW_DATA);

// Check if the email exists in the database
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $decryptedEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode(['message' => 'Email not found.']);
    exit();
}

// Generate a token
$token = bin2hex(openssl_random_pseudo_bytes(16));

// Save the token in the database
$query = "UPDATE users SET token = ? WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $token, $decryptedEmail);
$result = $stmt->execute();

if (!$result) {
    http_response_code(500);
    echo json_encode(['message' => 'Failed to save token.']);
    exit();
}

$mail = new PHPMailer();

// Configuração
$mail->Mailer = "smtp";
$mail->IsSMTP(); 
$mail->CharSet = 'UTF-8';   
$mail->SMTPDebug = 0;
$mail->SMTPAuth = true;     
$mail->SMTPSecure = 'ssl'; 
$mail->Host = 'smtp.gmail.com'; 
$mail->Port = 465;

// Detalhes do envio de E-mail
$mail->Username = 'lincoln.felixm@gmail.com'; 
$mail->Password = "cekwdxubuczixrrq";
$mail->SetFrom('noreply@wecare.com', 'WeCare Health');
$mail->addAddress($decryptedEmail);
$mail->Subject = 'Email Confirmation';


// Mensagem
$mensagem = "<h1> Token </h1>";
$mensagem .= 'Your token to recover your account is <b>' . $token . '</b>';


$mail->msgHTML($mensagem);

// Try to send the email
if ($mail->send()) {
    // Send an HTTP 200 response
    http_response_code(200);
    echo json_encode(['message' => 'Email confirmation sent.']);
} else {
    // If the email sending fails, send an HTTP 500 response
    http_response_code(500);
    echo json_encode(['message' => 'Failed to send the email confirmation.']);
}
?>