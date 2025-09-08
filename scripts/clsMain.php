<?php

    class clsMain {
        public static $vars;
        public static $web_root = '';

        public function __construct() {
            
            //$this->execute_main();
            // Initialization code can go here
            
            
            
        }

        public function execute_main() {
            // Initialization code can go here
            include_once './scripts/clsFileSystem.php';
            $fs=new clsFileSystem();
            
            $fs->include_files('./scripts/clsVariables.php');
            //include_once './scripts/clsVariables.php';
            self::$vars=new clsVariables();
            self::$vars->main=$this;

            self::$vars->fs = $fs;
            self::$vars->clsFileSystem = $fs;

            

            

            self::$vars->fs->set_root_directory(self::$web_root);
            self::$vars->fs->getAllDirectories();

            self::$vars->fs->templateDir=self::$web_root . 'templates' ;
            self::$vars->fs->pagesDir=self::$web_root . 'pages' ;
            self::$vars->fs->classDir=self::$web_root . 'scripts' ;
            self::$vars->fs->cgiDir=self::$web_root . 'cgi-bin' ;
            self::$vars->fs->web_root=self::$web_root;

            self::$vars->fs->templateDirId=self::$vars->fs->get_directory_id(self::$vars->fs->templateDir);
            self::$vars->fs->pagesDirId=self::$vars->fs->get_directory_id(self::$vars->fs->pagesDir);
            self::$vars->fs->classDirId=self::$vars->fs->get_directory_id(self::$vars->fs->classDir);
            self::$vars->fs->cgiDirId=self::$vars->fs->get_directory_id(self::$vars->fs->cgiDir);
            self::$vars->fs->web_rootId=self::$vars->fs->get_directory_id(self::$vars->fs->web_root);
            //self::$vars->fs->show_all();
            //self::$vars->fs->get_directories();
            
            //$dirID=self::$vars->fs->get_directory_id($dir);
            $fileID=self::$vars->fs->get_file_id('clsAutoloader.php', self::$vars->fs->classDirId);
            //print self::$vars->fs->classDirId . "\n";
            self::$vars->fs->include_files_by_id(self::$vars->fs->classDirId,$fileID);
            //self::$vars->fs->include_files(self::$vars->fs->classDir.'clsAutoloader.php');
            //include_once './scripts/clsAutoloader.php';
            $auto=new clsAutoloader();
            
            //$templateDir=self::$web_root;
            //$templateDir.= self::$vars->classDir."\\";
            $auto->set_root_directory(self::$vars->fs->classDir.DIRECTORY_SEPARATOR);
            $auto->auto_load_classes();
            //$stream=new DBStreamWrapper();
            //$auto->load_streams();
            
            //$auto->set_root_directory(self::$web_root);
            self::$vars->auto=$auto;
            //self::$vars->stream=$stream;

            $om = new clsOutput_Map();
            clsMain::$vars->om = $om;
        }

        public function set_web_root($dir) {
            //print "\n Setting web root directory: " . $dir;
            self::$web_root = $dir;
        }

        /*
        public function output() {
            $id = isset($_GET['id']) ? $_GET['id'] : 'online_community';
            //$templateFile = self::$vars->templateDir . 'main.html';
            
            // Load and resolve
            $templateFile=self::$web_root;
            $templateFile.= self::$vars->templateDir."\\". 'main.html';
            self::$vars->template=new clsTemplate();
            $templateContent = self::$vars->template->loadTemplate($templateFile);
            //print( "\n con->\n");
            // Replace {{file:<id>.html}} with file content
            self::$vars->output=new clsOutput();
            $resolved = self::$vars->output->resolveTags($templateContent, self::$web_root."\\".self::$vars->contentDir."\\");
            $resolved = self::$vars->output->resolveTags($resolved, self::$web_root."\\".self::$vars->contentDir."\\");

            // Optional: replace a {{main}} tag directly with chosen page
            $template_file = self::$web_root."\\".self::$vars->contentDir."\\" . basename($id) . '.html';
            //$content = self::$vars->output->replaceTags($pageFile, $resolved);
            $content = self::$vars->output->resolveTags($template_file, self::$web_root."\\".self::$vars->contentDir."\\");
            print_r( "\n con->".$content."\n");
            //self::$vars->output->output($content);
        }
            */
        public function output() {
            $id = isset($_GET['id']) ? $_GET['id'] : 'online_community';
            $folder = isset($_GET['folder']) ? $_GET['folder'] : '';
            $file = isset($_GET['file']) ? $_GET['file'] : '';

            self::$vars->id=$id;
            self::$vars->folder=$folder;
            self::$vars->file = $file;

            // Load template
            //$templateFile = self::$web_root . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'main.html';
            $templateFile = self::$web_root . 'templates' . DIRECTORY_SEPARATOR . 'main.html';
            //$templateFile =  'templates' . DIRECTORY_SEPARATOR . 'main.html';
            //if (!file_exists($templateFile)) {
             //   die("Template not found: $templateFile");
            //}

            self::$vars->page_titles=array("html-head"=>"ace-header.html","main-title"=>"Toho Website",
            "side-bar"=> basename($id)."_side-bar.html","main"=> basename($id) . '.html',"dc-title"=>"","Toho Website"=>"","meta_keywords"=>"Toho Website");

            //$templateContent = file_get_contents($templateFile);
            $templateContent =self::$vars->fs->readFile($templateFile);
            // Initialize output handler
            self::$vars->output = new clsOutput();

            // Resolve template tags
            $resolvedTemplate = self::$vars->output->resolveTags($templateContent, self::$web_root . DIRECTORY_SEPARATOR . self::$vars->contentDir);

            // Load page content
            $pageFile = self::$web_root . DIRECTORY_SEPARATOR . self::$vars->contentDir . DIRECTORY_SEPARATOR . basename($id) . '.html';
            if (file_exists($pageFile)) {
                //$pageContent = file_get_contents($pageFile);
                $pageContent = self::$vars->fs->readFile($pageFile);
                $resolvedPage = self::$vars->output->resolveTags($pageContent, self::$web_root . DIRECTORY_SEPARATOR . self::$vars->contentDir);
            } else {
                $resolvedPage = "<!-- Missing page: $id -->";
            }

            // Replace {{main}} tag in template with page content
            $finalContent = str_replace('{{main}}', $resolvedPage, $resolvedTemplate);

            // Output final HTML
            self::$vars->output->output($finalContent);
        }

    }


