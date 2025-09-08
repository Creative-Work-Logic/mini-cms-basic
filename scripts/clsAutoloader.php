<?php
    class clsAutoloader {
      public $all_class=array(); 
      public $rootDirectory="";
		  
      function __construct(){
        

        
        
      }

      public function auto_load_classes() {
        spl_autoload_register(function ($class_name) {
          
          $this->load($this->rootDirectory,$class_name);
        });
      }

      public function load_streams() {
        stream_wrapper_unregister("file");
        stream_wrapper_register("file", "DBStreamWrapper");

        stream_wrapper_register("db", "DBStreamWrapper");
      }

      public function set_root_directory($rootDirectory) {
            //print "\n Setting root directory: " . $rootDirectory."\n"; 
            $this->rootDirectory=$rootDirectory;
        }
     

      public function load_file($file_dir="",$file_name) {
        
        $return=false;
        $filename=$file_dir.$file_name . ".php";
        //$filename=$file_name . ".php";
        //print "\n load file->".$filename;
        if (clsMain::$vars->fs->fileExists($filename)) {
            $return=clsMain::$vars->fs->include_files($filename);
          
        }else{
          print "\n File not found: " . $filename;
        }
        return $return;        
      }
      public function load($file_dir="",$class_name) {
        $return=false;
        $return=$this->load_file($file_dir,$class_name);
        return $return;  
      }
    }