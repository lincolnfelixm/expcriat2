<?php
include 'conn.php';

$privateKey = openssl_pkey_get_private(file_get_contents("serverPrivateKey.pem"));

$data = json_decode(file_get_contents('php://input'), true);
$encryptedUsername = base64_decode($data['username']);
$encryptedEmail = base64_decode($data['email']);
$encryptedPassword = base64_decode($data['password']);
$encryptedSymmetricKey = base64_decode($data['symmetricKey']);
$encryptedIV = base64_decode($data['iv']);

openssl_private_decrypt($encryptedSymmetricKey, $symmetricKey, $privateKey);

// decrypt iv
openssl_private_decrypt($encryptedIV, $ivHex, $privateKey);
$iv = pack('H*', $ivHex); // convert hex to bytes

$username = openssl_decrypt($encryptedUsername, 'aes-128-cbc', $symmetricKey, OPENSSL_RAW_DATA, $iv);
$email = openssl_decrypt($encryptedEmail, 'aes-128-cbc', $symmetricKey, OPENSSL_RAW_DATA, $iv);
$password = openssl_decrypt($encryptedPassword, 'aes-128-cbc', $symmetricKey, OPENSSL_RAW_DATA, $iv);

$query = "INSERT INTO users (username, email, password, token) VALUES (?, ?, ?, NULL)";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
    $response = [
        "success" => true,
        "message" => "User registered successfully"
    ];
    echo json_encode($response);
} else {
    $response = [
        "success" => false,
        "message" => "User not registered"
    ];
    echo json_encode($response);
}

$stmt->close();
$conn->close();
?>
