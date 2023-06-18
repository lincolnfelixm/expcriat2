<?php
    // Include the database connection file
    include 'conn.php';

    $encryptedUsername = $_POST['username'];
    $encryptedEmail = $_POST['email'];
    $encryptedPassword = $_POST['password'];
    $encryptionKey = $_POST['encryptionKey'];

    // Fetch the private key from a file (assuming it's stored in privateKey.txt)
    $privateKey = file_get_contents('serverPrivateKey.pem');

    // Decrypt the encryption key using the private key
    openssl_private_decrypt(base64_decode($encryptionKey), $decryptedEncryptionKey, $privateKey);

    // Decrypt the username, email, and password using the decrypted encryption key
    $decryptedUsername = openssl_decrypt(base64_decode($encryptedUsername), 'AES-128-ECB', $decryptedEncryptionKey, OPENSSL_RAW_DATA);
    $decryptedEmail = openssl_decrypt(base64_decode($encryptedEmail), 'AES-128-ECB', $decryptedEncryptionKey, OPENSSL_RAW_DATA);
    $decryptedPassword = openssl_decrypt(base64_decode($encryptedPassword), 'AES-128-ECB', $decryptedEncryptionKey, OPENSSL_RAW_DATA);

    // Check if the email is valid
    if (!filter_var($decryptedEmail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo 'Invalid email address';
        exit();
    }

    // Check if the terms and conditions are accepted
    if (!isset($_POST['terms']) || $_POST['terms'] !== 'accepted') {
        http_response_code(400);
        echo 'Please accept the terms and conditions';
        exit();
    }

    // Check if the email is already registered in the database
    $query = "SELECT * FROM users WHERE email = '$decryptedEmail'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        http_response_code(409);
        echo 'Email already registered';
        exit();
    }

    // Insert the user into the database
    $query = "INSERT INTO users (username, email, password) VALUES ('$decryptedUsername', '$decryptedEmail', '$decryptedPassword')";
    if (mysqli_query($conn, $query)) {
        // Start the session
        session_start();

        // Set the session variables
        $_SESSION['email'] = $decryptedEmail;
        $_SESSION['password'] = $decryptedPassword;
        $_SESSION['start_time'] = time();
        $_SESSION['expire_time'] = $_SESSION['start_time'] + (60 * 60); // 1 hour expiration

        // Send an HTTP 200 response
        http_response_code(200);
        echo 'Registration successful';
    } else {
        http_response_code(500);
        echo 'Error occurred during registration';
    }
?>
