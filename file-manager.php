<?php
$dir = isset($_GET['dir']) ? $_GET['dir'] : '.';
$dir = realpath($dir); // Resolve path safely

// Prevent going above root
$root = realpath('.');
if (strpos($dir, $root) !== 0) {
    $dir = $root;
}

$files = scandir($dir);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PHP File Manager</title>
    <style>
        body {
            font-family: monospace;
            background: #111;
            color: #eee;
        }

        a {
            color: #3b82f6;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
        }

        tr:nth-child(even) {
            background: #222;
        }
    </style>
</head>

<body>
    <h1>File Manager: <?= htmlspecialchars($dir) ?></h1>
    <table>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Size</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($files as $file):
            if ($file === '.')
                continue;
            $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
            $type = is_dir($fullPath) ? 'Folder' : 'File';
            $size = is_file($fullPath) ? filesize($fullPath) : '-';
            ?>
            <tr>
                <td>
                    <?php if (is_dir($fullPath)): ?>
                        <a href="?dir=<?= urlencode($fullPath) ?>"><?= htmlspecialchars($file) ?></a>
                    <?php else: ?>
                        <?= htmlspecialchars($file) ?>
                    <?php endif; ?>
                </td>
                <td><?= $type ?></td>
                <td><?= $size ?></td>
                <td>
                    <?php if (is_file($fullPath)): ?>
                        <a href="view.php?file=<?= urlencode($fullPath) ?>" target="_blank">View</a> |
                    <?php endif; ?>
                    <a href="#" onclick="deleteFile('<?= addslashes($fullPath) ?>')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function deleteFile(path) {
            if (confirm('Delete ' + path + '?')) {
                fetch('delete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'file=' + encodeURIComponent(path)
                }).then(res => res.text()).then(alert).then(() => location.reload());
            }
        }
    </script>
</body>

</html>