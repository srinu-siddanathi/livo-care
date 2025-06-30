<?php
// Place this file in your public directory
$target = dirname(__DIR__) . '/storage/app/public';
$link = __DIR__ . '/storage';

// For debugging
echo "Target path: " . $target . "<br>";
echo "Link path: " . $link . "<br>";

if (file_exists($link)) {
    echo "The storage link already exists...<br>";
    exit;
}

// Create directories if they don't exist
if (!file_exists($target)) {
    mkdir($target, 0755, true);
    mkdir($target . '/prescriptions', 0755, true);
    echo "Created target directories<br>";
}

if (symlink($target, $link)) {
    echo "Storage link has been created successfully!<br>";
} else {
    echo "Failed to create storage link. Error: " . error_get_last()['message'] . "<br>";
    
    // Alternative method if symlink fails
    echo "Trying alternative method...<br>";
    if (!file_exists($link)) {
        mkdir($link, 0755);
    }
    
    // Create a .htaccess in the storage directory
    file_put_contents($link . '/.htaccess', "Options +FollowSymLinks\nRewriteEngine On");
    
    echo "Setup complete. Please check if files are accessible.<br>";
}

// For security, delete this file after successful execution
// unlink(__FILE__); 