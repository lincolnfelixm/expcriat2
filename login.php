<?php
    include 'conn.php';

    $privateKey = openssl_pkey_get_private(file_get_contents("serverPrivateKey.pem"));
    
    $data = json_decode(file_get_contents('php://input'), true);

    $encryptedUsername = $data['username'];
    $encryptedPassword = $data['password'];
    $encryptedSymmetricKey = base64_decode($data['symmetricKey']);
    $encryptedIV = base64_decode($data['iv']);
    
    openssl_private_decrypt($encryptedSymmetricKey, $decryptedSymmetricKey, $privateKey);
    openssl_private_decrypt($encryptedIV, $decryptedIV, $privateKey);
    
    // Convert hex string back to binary
    $symmetricKey = hex2bin($decryptedSymmetricKey);
    $iv = hex2bin($decryptedIV);
    
    $username = openssl_decrypt($encryptedUsername, 'aes-128-cbc', $symmetricKey, OPENSSL_RAW_DATA, $iv);
    $password = openssl_decrypt($encryptedPassword, 'aes-128-cbc', $symmetricKey, OPENSSL_RAW_DATA, $iv);

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0) {
        $response = [
            "success" => true,
            "message" => "User logged in successfully"
        ];
        echo json_encode($response);
    } else {
        $response = [
            "success" => false,
            "message" => "User not found"
        ];
        echo json_encode($response);
    }
?>
