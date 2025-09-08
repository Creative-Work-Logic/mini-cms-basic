<?php
//require 'FileManager.php';
$fm = new FileManager('.');
$dir = $_GET['dir'] ?? '.';
$files = $fm->list($dir);
?>

<h1>File Manager:
    <?= htmlspecialchars(realpath($dir)) ?>
</h1>

<table>
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Size</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($files as $f):
        if (!$f)
            continue;
        ?>
        <tr>
            <td>
                <?php if ($f['is_dir']): ?>
                    <a href="?dir=<?= urlencode($f['path']) ?>">
                        <?= htmlspecialchars($f['name']) ?>
                    </a>
                <?php else: ?>
                    <?= htmlspecialchars($f['name']) ?>
                <?php endif; ?>
            </td>
            <td>
                <?= $f['is_dir'] ? 'Folder' : 'File' ?>
            </td>
            <td>
                <?= $f['size'] ?? '-' ?>
            </td>
            <td>
                <?php if (!$f['is_dir']): ?>
                    <a href="view.php?file=<?= urlencode($f['path']) ?>" target="_blank">View</a> |
                <?php endif; ?>
                <a href="#" onclick="deleteFile('<?= addslashes($f['path']) ?>')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>