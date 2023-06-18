<?php
    include 'conn.php';

    $email = $_POST['email'];
    $encryptedPassword = $_POST['password'];
    $encryptionKey = $_POST['encryptionKey'];

    $privateKey = file_get_contents('serverPrivateKey.pem');

    openssl_private_decrypt(base64_decode($encryptionKey), $decryptedEncryptionKey, $privateKey);

    $decryptedPassword = openssl_decrypt(base64_decode($encryptedPassword), 'AES-128-ECB', $decryptedEncryptionKey, OPENSSL_RAW_DATA);

    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$decryptedPassword'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        http_response_code(401);
        echo 'Invalid email or password';
    } else {
        session_start();

        $_SESSION['email'] = $email;
        $_SESSION['password'] = $decryptedPassword;
        $_SESSION['start_time'] = time();
        $_SESSION['expire_time'] = $_SESSION['start_time'] + (60 * 60);

        http_response_code(200);
        echo 'Login successful';
    }
?>