<?php
// Place this file in your project root
$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';

if (file_exists($link)) {
    echo "The storage link already exists...\n";
    exit;
}

if (symlink($target, $link)) {
    echo "Storage link has been created successfully!\n";
} else {
    echo "Failed to create storage link.\n";
} 