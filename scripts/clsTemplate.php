<?php
    class   clsTemplate {
        
        public function __construct() {
        }

        function loadTemplate($file) {
            //print $file;
            //$file="file://".$file;
            if (!clsMain::$vars->fs->fileExists($file)) {
                return "<!-- Template not found: $file -->";
            }
            $return=false;
            $return=clsMain::$vars->fs->readFile($file);
            return $return;
        }
    }