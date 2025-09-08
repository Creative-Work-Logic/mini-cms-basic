<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Editable PHP + JS + HTML Editor</title>

<!-- Ace Editor CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.1/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.1/ext-language_tools.js"></script>

<style>
html, body { height: 100%; margin: 0; background: #0b0d10; color: #e6e6e6; font-family: monospace; }
#editor { 
    position: absolute; top: 0; bottom: 0; left: 0; right: 0;
    border: 1px solid #333;
    font-size: 14px;
}
</style>
</head>
<body>

<div id="editor">
// Editable PHP + JS + HTML example
<?php
if () {
    return "The parser recovers from this type of syntax error";
}
?>

<script type="text/javascript">
var server_token = <?=rand(5, 10000)?>;
if (typeof server_token === 'number') {
    alert('token: ' + server_token);
}
</script>

<div>
Hello
<?php if (isset($user)) { ?>
    <b><?= $user ?></b>
<?php } else { ?>
    <i>guest</i>
<?php } ?>
!
</div>

<?php
$cards = array("ah","ac","ad","as",
    "2h","2c","2d","2s",
    "3h","3c","3d","3s");
srand(time());
for($i=0;$i<12;$i++){
    $count=count($cards);
    $random=(rand()%$count);
    if($cards[$random]==""){$i--;}else{$deck[]=$cards[$random];$cards[$random]="";}
}
?>
</div>

<script>
// Initialize Ace Editor
var editor = ace.edit("editor");
editor.setTheme("ace/theme/monokai");
editor.session.setMode("ace/mode/html"); // mixed PHP+JS+HTML works best in HTML mode
editor.setOptions({
    enableBasicAutocompletion: true,
    enableLiveAutocompletion: true,
    enableSnippets: true,
    showLineNumbers: true,
    tabSize: 4
});
</script>

</body>
</html>
