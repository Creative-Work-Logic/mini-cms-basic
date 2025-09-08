<?php
// --- Simple loader/saver (demo) ---
// ?file=path/to/file.ext to load a file. Save posts back to same file.
$error = '';
$msg   = '';
$filepath = isset($_GET['file']) ? $_GET['file'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filepath = $_POST['filepath'] ?? '';
    $content  = $_POST['code'] ?? '';
    if ($filepath) {
        // Basic safety: only allow files under current dir
        $realBase = realpath(__DIR__);
        $realFile = realpath($filepath) ?: $filepath;
        if (strpos(realpath(dirname($realFile)) ?: __DIR__, $realBase) === 0) {
            if (@file_put_contents($realFile, $content) !== false) {
                $msg = "Saved: " . htmlspecialchars($realFile);
            } else {
                $error = "Failed to save file.";
            }
        } else {
            $error = "Invalid path.";
        }
    } else {
        $error = "No file path provided.";
    }
}

$code = '';
if ($filepath && is_readable($filepath)) {
    $code = file_get_contents($filepath);
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Textarea Code Editor with Line Numbers</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  :root { --bg:#0b0d10; --fg:#e6e6e6; --muted:#9aa3af; --accent:#3b82f6; --border:#1f2937;}
  body { margin:0; background:var(--bg); color:var(--fg); font:14px/1.4 ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace; }
  .wrap { max-width:1100px; margin:24px auto; padding:0 16px; }
  h1 { font-size:18px; margin:0 0 12px; }
  form { display:grid; gap:12px; }
  .status { padding:8px 12px; border-radius:8px; }
  .ok { background:#0f2e16; color:#9ef2c3; border:1px solid #1c4d2c;}
  .err{ background:#2e1010; color:#ffb3b3; border:1px solid #4d1c1c;}

  /* Editor shell */
  .editor {
    display:grid;
    grid-template-columns: max-content 1fr;
    border:1px solid var(--border);
    border-radius:10px;
    overflow:hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.35);
  }
  .gutter {
    user-select:none;
    padding:8px 8px 8px 12px;
    background:#0f1318;
    color:var(--muted);
    text-align:right;
    min-width:3.5ch;
    border-right:1px solid var(--border);
    overflow:hidden;        /* hidden + synced scroll */
  }
  .gutter pre {
    margin:0; white-space:pre; font:inherit; line-height:1.4;
  }
  textarea {
    display:block;
    resize:vertical;
    width:100%;
    min-height:60vh;
    padding:8px 12px;
    background:#0b0d10;
    color:var(--fg);
    border:0;
    outline:0;
    font:inherit;
    line-height:1.4;
    tab-size:2;
    white-space:pre;        /* critical for no wrap */
    overflow:auto;
  }
  .bar { display:flex; gap:8px; align-items:center; }
  .bar input[type="text"]{
    flex:1; background:#0f1318; color:var(--fg); border:1px solid var(--border);
    padding:8px 10px; border-radius:8px; outline:none;
  }
  .btn {
    background:var(--accent); color:white; border:0; padding:10px 14px; border-radius:8px; cursor:pointer;
  }
  .btn.secondary { background:#111827; border:1px solid var(--border); }
  .row { display:flex; gap:8px; align-items:center; }
  .hint { color:var(--muted); font-size:12px; }
</style>
</head>
<body>
<div class="wrap">
  <h1>Textarea Code Editor (with Line Numbers)</h1>

  <?php if ($msg): ?><div class="status ok"><?= $msg ?></div><?php endif; ?>
  <?php if ($error): ?><div class="status err"><?= $error ?></div><?php endif; ?>

  <form method="post" action="">
    <div class="bar">
      <input type="text" name="filepath" placeholder="Path to file (relative or absolute)" value="<?= htmlspecialchars($filepath) ?>">
      <button class="btn secondary" type="button" id="reloadBtn">Reload</button>
      <button class="btn" type="submit">Save</button>
    </div>

    <div class="editor" id="editor">
      <div class="gutter" id="gutter"><pre>1</pre></div>
      <textarea id="code" name="code" wrap="off" spellcheck="false"><?= htmlspecialchars($code) ?></textarea>
    </div>

    <div class="row">
      <label class="hint">Tab inserts spaces • Ctrl/Cmd+S to save • Line/col shows in title</label>
    </div>
  </form>
</div>

<script>
(function(){
  const ta = document.getElementById('code');
  const gut = document.getElementById('gutter').firstElementChild; // <pre>
  const reloadBtn = document.getElementById('reloadBtn');
  const form = document.forms[0];

  // --- Line number rendering ---
  function updateLineNumbers(){
    const lines = ta.value.split('\n').length || 1;
    // build once to avoid flicker
    let out = '';
    for (let i=1;i<=lines;i++) out += i + '\n';
    // trim trailing newline for nicer bottom spacing
    gut.textContent = out.replace(/\n$/, '');
  }

  // --- Sync scroll: gutter follows textarea ---
  function syncScroll(){
    const gutter = document.getElementById('gutter');
    gutter.scrollTop = ta.scrollTop;
  }

  // --- Tab key inserts spaces (2 or 4, your pick) ---
  const TAB = '  '; // 2 spaces
  ta.addEventListener('keydown', function(e){
    if (e.key === 'Tab') {
      e.preventDefault();
      const start = this.selectionStart, end = this.selectionEnd;
      const val = this.value;
      this.value = val.slice(0,start) + TAB + val.slice(end);
      this.selectionStart = this.selectionEnd = start + TAB.length;
      updateLineNumbers();
    } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
      e.preventDefault();
      form.submit();
    }
  });

  // --- Track caret line/col in document title (fun) ---
  function updateTitleCaret(){
    const pos = ta.selectionStart;
    const upToPos = ta.value.slice(0,pos);
    const line = (upToPos.match(/\n/g)||[]).length + 1;
    const col  = pos - upToPos.lastIndexOf('\n');
    document.title = `Ln ${line}, Col ${col} — Textarea Code Editor`;
  }

  // Update numbers efficiently
  let rafPending = false;
  function scheduleUpdate(){
    if (rafPending) return;
    rafPending = true;
    requestAnimationFrame(()=>{ rafPending=false; updateLineNumbers(); });
  }

  ta.addEventListener('input', scheduleUpdate);
  ta.addEventListener('scroll', syncScroll);
  ta.addEventListener('click', updateTitleCaret);
  ta.addEventListener('keyup', updateTitleCaret);
  window.addEventListener('resize', syncScroll);

  // Initial render
  updateLineNumbers();
  syncScroll();
  updateTitleCaret();

  // Reload button (GET with same filepath)
  reloadBtn.addEventListener('click', ()=>{
    const fp = document.querySelector('input[name="filepath"]').value.trim();
    const url = new URL(window.location.href);
    if (fp) url.searchParams.set('file', fp); else url.searchParams.delete('file');
    window.location.href = url.toString();
  });
})();
</script>
</body>
</html>
