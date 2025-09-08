<?php

    class clsRunTemplates{
        public $vars=array();
        public $rootDirectory='D:\Library\Repositorys\Mini-CMS\mini-cms-basic\mini-cms-basic\templates'; // example root directory
        public $exclude_dirs=['.git', 'jscript']; // example exclude

        public function __construct() {
            echo"hello";
            print $this->getAllDirectories();
        }

        public function getAllFilesAndDirs($root) {
            $filesByDir = [];
            $dirs = [];

            // normalize root (remove trailing slash/backslash)
            $root = rtrim($root, "/\\");
            $dirs[] = $root;
            $filesByDir[$root] = [];

            $filter = new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
                function ($file, $key, $iterator) {
                    if ($file->isDir()) {
                        return !in_array($file->getFilename(), $this->exclude_dirs);
                    }
                    return true;
                }
            );

            $rii = new RecursiveIteratorIterator($filter, RecursiveIteratorIterator::SELF_FIRST);

            foreach ($rii as $file) {
                if ($file->isDir()) {
                    $path = rtrim($file->getPathname(), "/\\");
                    $dirs[] = $path;
                    $filesByDir[$path] = [];
                } else {
                    $path = rtrim($file->getPath(), "/\\");
                    $filesByDir[$path][] = $file->getFilename();
                }
            }

            return [$dirs, $filesByDir];
        }


        public function getAllDirectories() {
            //list($files, $dirs) = $this->getAllFilesAndDirs($this->rootDirectory);//$this->getFilesAndDirs($this->rootDirectory);
            //print "\n root->".$this->rootDirectory;
            list($dirs, $filesByDir)    = $this->getAllFilesAndDirs($this->rootDirectory);

            $this->vars['dirs']=$dirs;
            $this->vars['filesByDir']=$filesByDir;

            return $this->show_all_files();
            
        }

        public function show_all_files() {
            //echo "\n<h3>Files:</h3>\n";
            $return=false;
            $all_files=var_export($this->vars['filesByDir'],true);
            foreach ($this->vars['filesByDir'] as $dir => $files) {
                if (!empty($files)) {
                    //$return.="<h3>Files in directory: $dir</h3>";
                    $return.="<ul>";
                    foreach ($files as $file) {
                        $return.="<li>$file</li>";
                        $str = file_get_contents($this->rootDirectory."\\".$file);
                        file_put_contents($this->rootDirectory."\\".$file.".html", base64_decode($str));
                        //echo base64_decode($str);
                    }
                    $return.="</ul>";
                } else {
                    $return.="<h3>No files in directory: $dir</h3>";
                }
            }
            return $return;
        }
    }

    $new=new clsRunTemplates();
    