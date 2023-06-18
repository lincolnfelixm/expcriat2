<?php

    $filename = 'serverPublicKey.pem';
    
    function getPublicKey($filename) {
        // Check if the file exists
        if (!file_exists($filename)) {
            // Send an HTTP 404 response if the file is not found
            http_response_code(404);
            echo 'File not found';
            return null;
        } else {
            // Read the contents of the file
            $publicKey = file_get_contents($filename);
    
            // Return the public key
            return $publicKey;
        }
    }
?>