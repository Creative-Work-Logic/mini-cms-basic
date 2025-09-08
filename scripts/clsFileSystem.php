<?php
    class clsFileSystem {
        public $rootDirectory = __DIR__ ;
        //public $exclude_dirs = array('.git', 'jscript');
        private $exclude_dirs = ['.git', 'jscript']; // example exclude
        public $dirs = array();
        public $files = array();
        public $file_array = array();

        public function __construct() {
            // Initialization code can go here
            //$this->rootDirectory='db://';
            //$this->get_directories();
        }

        public function set_root_directory($rootDirectory) {
            //print "\n Setting root directory: " . $rootDirectory; 
            $this->rootDirectory=$rootDirectory;
        }

        public function get_directories() {
            $path=__DIR__;
            //$parts = explode(DIRECTORY_SEPARATOR, $path);
            $parts = preg_split('/[\/\\\\]+/', $path);

            // Rebuild progressively
            $current = $parts[0]; // "D:"
            for ($i = 1; $i < count($parts); $i++) {
                $current .= DIRECTORY_SEPARATOR . $parts[$i];
                $main_dir=$current. DIRECTORY_SEPARATOR;
                $candidate = $current . DIRECTORY_SEPARATOR . "index.php";
                if (file_exists($candidate)) {
                    //echo "\n Found index.php at: " . $candidate;
                    break;
                }
            }
            $this->rootDirectory= $main_dir;
            //print("\n".$main_dir);
        }

        public function include_files($filePath) {
            //$filePath.='db://' .$this->rootDirectory. $filePath;
            $return=false;
            if($this->fileExists($filePath)){
                $return=include($filePath);
            }
            return $return;
        }

        public function include_files_by_id($dirID,$fileID) {
            //$filePath.='db://' .$this->rootDirectory. $filePath;
            //$filePath= clsMain::$vars->filesByDir[clsMain::$vars->dirs[$dirID]][$fileID];
            //print_r(clsMain::$vars->all_files);
            $return=false;
            if(($dirID!=-1)&&($fileID!=-1)){
                $file_name=clsMain::$vars->all_files[$dirID][$fileID];
                //$file_directory=clsMain::$vars->all_files[$dirID];
                $file_directory = clsMain::$vars->dirs[$dirID];
                //print_r($file_directory);
                $filePath= $file_directory . DIRECTORY_SEPARATOR . $file_name;
                $return=$this->include_files($filePath);
            }else{
                print "\n Invalid directory or file ID: dirID=$dirID, fileID=$fileID";
            }
            
            return $return;     
        }

        public function fileExists($filePath) {
            //return file_exists($this->rootDirectory . $filePath);
            return file_exists( $filePath);
        }

        public function file_is_valid($filePath) {
            //return file_exists($this->rootDirectory . $filePath);
            //print_r(clsMain::$vars->filesByDir);
            return true;
        }

        

        public function readFile($filePath) {
            /*
            if ($this->fileExists($this->rootDirectory .$filePath)) {
                return file_get_contents($this->rootDirectory . $filePath);
            }
                */
            if($this->file_is_valid($filePath)){
                if ($this->fileExists($filePath)) {
                    return file_get_contents( $filePath);
                }
            }
            
            return false;
        }

        public function writeFile($filePath, $content) {
            return file_put_contents($this->rootDirectory . $filePath, $content);
        }

        function getFilesAndDirs($dir) {
            $files = [];
            $dirs  = [];

            $filter = new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
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
                    $dirs[] = $file->getPathname();
                } else {
                    $files[] = $file->getPathname();
                }
            }

            return [$files, $dirs];
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

        public function get_directory_id($dir) {
            $id=-1;
            
            $dirs = clsMain::$vars->dirs;
            //print("\n dir->".$dir." \n");
            //print_r($dirs);
            
            //if(is_array($dirs) && empty($dirs)) {
            if($dir!= '') {
                $id = array_search($dir, $dirs);
                //print("\n id->".$id);
                
            }
            
            return $id;
        }

        public function get_file_id($file_name, $dir_number = 0) {
            $files = clsMain::$vars->all_files;
            //print_r($files);
            $id = array_search($file_name, $files[$dir_number]);
            if ($id === false) {
                return -1; // Not found
            }
            return $id;
        }

        public function getAllDirectories() {
            //list($files, $dirs) = $this->getAllFilesAndDirs($this->rootDirectory);//$this->getFilesAndDirs($this->rootDirectory);
            //print "\n root->".$this->rootDirectory;
            list($dirs, $filesByDir)    = $this->getAllFilesAndDirs($this->rootDirectory);

            clsMain::$vars->dirs=$dirs;
            //print_r(clsMain::$vars->dirs);
            clsMain::$vars->filesByDir=$filesByDir;

            clsMain::$vars->all_files=$this->set_files();
            
        }
        //self::$vars->fs->get_directory_id(self::$vars->fs->web_root);

        public function set_files() {
            //echo "\n<h3>Files:</h3>\n";
            $return=false;
            $file_array=array();
            //$all_files=var_export(clsMain::$vars->filesByDir,true);
            foreach (clsMain::$vars->filesByDir as $dir => $files) {
                if (!empty($files)) {
                    //$return.="<h3>Files in directory: $dir</h3>";
                    $dirID=$this->get_directory_id($dir);
                    $file_array[$dirID]=$files;
                    
                } else {
                    $return.="<h3>No files in directory: $dir</h3>";
                }
            }
            $this->file_array=$file_array;
            return $file_array;
        }

        public function show_all_files() {
            //echo "\n<h3>Files:</h3>\n";
            $return=false;
            $all_files=var_export(clsMain::$vars->filesByDir,true);
            foreach (clsMain::$vars->filesByDir as $dir => $files) {
                if (!empty($files)) {
                    //$return.="<h3>Files in directory: $dir</h3>";
                    $return.="<ul>";
                    foreach ($files as $file) {
                        $return.="<li>$file</li>";
                    }
                    $return.="</ul>";
                } else {
                    $return.="<h3>No files in directory: $dir</h3>";
                }
            }
            return $return;
        }

        public function show_all_files_in_dir() {
            //self::$vars->id = $id;
            //self::$vars->folder = $folder;
            $base = clsMain::$web_root. DIRECTORY_SEPARATOR. clsMain::$vars->folder; // or your path e.g. "C:/xampp/htdocs"

            // Only files (skip directories)
            $files = array_filter(glob($base . '/*'), 'is_file');
            $file_items = '';
            foreach ($files as $file) {
                $file_items.='<br><a href="index.php?id='. clsMain::$vars->id.'&folder='. clsMain::$vars->folder.'&file='.basename($file).'">'.basename($file).'</a>' . PHP_EOL;
            }
            return $file_items;
        }

        public function show_all_dirs() {
            $base = clsMain::$web_root; // or set to your path e.g. "C:/xampp/htdocs"
            $dirs = array_filter(glob($base . '/*'), 'is_dir');

            foreach ($dirs as $dir) {
                $dir_locations[]=basename($dir) . PHP_EOL;
            }

            return $dir_locations;
        }

        public function show_all() {
            $this->show_all_files();
            $this->show_all_dirs();
        }

    }