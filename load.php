<?php
$file = $_GET['file'] ?? '';
$path = __DIR__ . $file;
if (is_file($path)) {
    echo file_get_contents($path);
}
