<?php
// filetree.php
$root = __DIR__;  // set web root (can change to your project root)

// recursive function to build tree
function buildTree($dir, $base = '') {
    $items = scandir($dir);
    echo "<ul>";
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        $rel  = $base . '/' . $item;
        if (is_dir($path)) {
            echo "<li class='folder'>$item";
            buildTree($path, $rel);
            echo "</li>";
        } else {
            echo "<li class='file' data-path='$rel'>$item</li>";
        }
    }
    echo "</ul>";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>PHP File Tree + Editor</title>
<style>
  body { margin:0; font-family:Arial, sans-serif; display:flex; height:100vh; }
  #sidebar { width:250px; background:#f4f4f4; overflow:auto; padding:10px; border-right:1px solid #ccc; }
  #editor { flex:1; height:100%; border:none; }
  ul { list-style:none; margin:0; padding-left:15px; }
  li { cursor:pointer; margin:2px 0; }
  .folder { font-weight:bold; }
  .file:hover { background:#ddd; }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.3/ace.js"></script>
</head>
<body>
<div id="sidebar">
  <h3>Files</h3>
  <?php buildTree($root); ?>
</div>
<div style="flex:1; display:flex; flex-direction:column;">
  <div id="editor"><?php echo "<?php echo 'Hello World'; ?>"; ?></div>
  <form method="post" action="save.php">
    <textarea name="code" id="code" hidden></textarea>
    <input type="hidden" name="file" id="filePath">
    <button type="submit">💾 Save</button>
  </form>
</div>

<script>
const editor = ace.edit("editor");
editor.setTheme("ace/theme/monokai");
editor.session.setMode("ace/mode/php");

document.querySelectorAll(".file").forEach(el=>{
  el.addEventListener("click", ()=>{
    const path = el.dataset.path;
    fetch("load.php?file=" + encodeURIComponent(path))
      .then(r=>r.text())
      .then(txt=>{
        editor.setValue(txt, -1);
        document.getElementById("filePath").value = path;
      });
  });
});

document.querySelector("form").addEventListener("submit", e=>{
  document.getElementById("code").value = editor.getValue();
});
</script>
</body>
</html>
