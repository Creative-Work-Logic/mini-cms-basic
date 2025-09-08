<?php
    class clsVariables{
        public $variables = array();

        public function __construct() {
            $this->setAllVariable();
            
        }

        public function setAllVariable() {
            /*
            $current_dir = __DIR__;
            //print "\n Current directory: " . $current_dir;
            $templateDir = 'templates';
            $contentDir  = 'pages';
            $classDir  =$current_dir. '\';
            */
            $current_dir = __DIR__;
            $templateDir =  'templates';
            $contentDir  ='pages';  
            $classDir  ='scripts';      
            $this->setVariable('templateDir', $templateDir);
            $this->setVariable('contentDir', $contentDir);
            $this->setVariable('classDir', $classDir);
            $this->setVariable('current_dir', $current_dir);
            $this->setVariable('web_root', $current_dir);

            $title = 'Mini CMS';
            $description = 'A simple content management system.';
            $keywords = 'cms, mini, content, management, system';
            $author = 'Creative Work Logic';
            $version = '1.0.0';

            $this->setVariable('title', $title);
            $this->setVariable('description', $description);
            $this->setVariable('keywords', $keywords);
            $this->setVariable('author', $author);
            $this->setVariable('version', $version);
        }

        public function set_web_root($web_root) {
            $this->setVariable('web_root', $web_root);
        }

        public function setVariable($name, $value) {
            $this->variables[$name] = $value;
        }

        public function getVariable($name) {
            return isset($this->variables[$name]) ? $this->variables[$name] : null;
        }

        public function hasVariable($name) {
            return isset($this->variables[$name]);
        }

        public function removeVariable($name) {
            unset($this->variables[$name]);
        }

        public function clearVariables() {
            $this->variables = [];
        }

        public function __set($name, $value)
        {
            $this->setVariable($name, $value);
        }

        public function __get($name)
        {
            $return=false;
            $return=$this->getVariable($name);
            return $return;
        }
    }