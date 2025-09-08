# mini-cms-basic
Mini CMS - Basic Edition

Create html file in pages directory. the filename is referenced as /index.php?id=filename
/pages/filename.html

Functional Tags are enclosed by {{ command }}

e.g.
{{file:split-columns.html}}

valid commands are..

include:
index:
file:
->

e.g.

{{include:filename.ext}} path to file /includes/ <br>
{{file:filename.ext}} path to file /pages/
{{index:filename.ext}} path to file /
{{class_name->execute_method}}
