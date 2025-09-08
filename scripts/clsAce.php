<?php
    class clsAce{
        //$root = __DIR__;  // set web root (can change to your project root)

        public function __construct() {
            // Constructor logic if needed
        }

        

        

        // recursive function to build tree
        public function buildTree($dir_number= 0, $base = '') {

            $dirs=clsMain::$vars->dirs;
            $files=clsMain::$vars->filesByDir;
            //print_r($dirs);
            $dir=$dirs[$dir_number];
            
            $return=false;
            $items = scandir($dir);
            $return.="<ul>";
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                $path = $dir . DIRECTORY_SEPARATOR . $item;
                $rel  = $base . '/' . $item;
                if (is_dir($path)) {
                    $return.="<li class='folder'>$item";
                    //$return.=$this->buildTree($path, $rel);
                    $return.=$this->buildTree($dir_number+1, $rel);
                    $return.="</li>";
                } else {
                    $return.="<li class='file' data-path='$rel'>$item</li>";
                }
            }
            $return.="</ul>";
            return $return;
        }
    }