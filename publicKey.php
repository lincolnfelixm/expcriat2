<?php
    // The name of the file to open
    $filename = 'publicKey.txt';

    // Check if the file exists
    if (!file_exists($filename)) {
        // Send an HTTP 404 response if the file is not found
        http_response_code(404);
        echo 'File not found';
    } else {
        // Read the contents of the file
        $publicKey = file_get_contents($filename);

        // Send the contents of the file as the response
        echo $publicKey;
    }
?>
