<?php
    // Include the database connection file
    include 'conn.php';

    // Fetch the token from the URL
    $token = $_POST['token'];

    // Decrypt the token using the private key
    $privateKey = file_get_contents('serverPrivateKey.pem');
    openssl_private_decrypt(base64_decode($token), $decryptedToken, $privateKey);

    // Find the user with the token
    $query = "SELECT * FROM users WHERE token = '$decryptedToken'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        http_response_code(404);
        echo 'Token not found.';
        exit();
    }

    // Generate a new password
    $newPassword = bin2hex(openssl_random_pseudo_bytes(16));

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the user's password and clear the token
    $query = "UPDATE users SET password = '$hashedPassword', token = NULL WHERE token = '$decryptedToken'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        http_response_code(500);
        echo 'Failed to update password.';
        exit();
    }

    echo 'Your password has been updated.';

?>
