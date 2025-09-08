<?php
class clsOutput_Map {
    private $match_array = [];
    private $contentDir = [];
    private $class = [];

    public function __construct() {
        // Constructor code here$this->add_match("file:","map_file");
        $this->add_details();
    }
    public function add_details()
    {
        $this->add_class(0, "include");
        $this->add_class(1, "index");
        $this->add_class(2, "class");
        $this->add_class(3, "file");
        $this->add_class(4, "default");


        $this->add_match("include:", 0,"split_right");
        $this->add_match("index:", 1,"split_right");
        $this->add_match("->", 2,"split_half");
        $this->add_match("file:", 3,"split_right");
        $this->add_match("", 4, "split_none");


        $this->add_dir(0, "includes");
        $this->add_dir(1);
        $this->add_dir(2, "scripts");
        $this->add_dir(3, "pages");
        $this->add_dir(4, "pages");
    }

    public function add_class($number,$class)
    {
        $this->class[$number] = "map_".$class;
    }


    public function add_match($match_string,$class_number,$split_type)
    {
        $match=array($match_string,$class_number,$split_type);
        $this->match_array[$match_string] = $match;
    }

    public function add_dir($map_class_number, $dir="")
    {
        //$match = array(DIRECTORY_SEPARATOR .$dir, $this->class[$map_class_number]);
        $this->contentDir[$map_class_number] = $dir;//$match;
    }

    public function retrieve_file($filename, $map_class_number = 0)
    {
        $filePath = $this->retrieve_file_path($filename, $map_class_number);
        return file_exists($filePath) ? file_get_contents($filePath) : "<!-- Missing file: $filename -->";
    }
    public function include_file($filename, $map_class_number=0)
    {
        $filePath = $this->retrieve_file_path($filename, $map_class_number);
        return file_exists($filePath) ? include($filePath) : "<!-- Missing file: $filename -->";
    }
    /*
    public function retrieve_folder_file()
    {
        $fileDetails="hello";
        if(clsMain::$vars->file!=""){
            $filePath = clsMain::$web_root . DIRECTORY_SEPARATOR . clsMain::$vars->folder . DIRECTORY_SEPARATOR . clsMain::$vars->file;
            $fileDetails=file_exists($filePath) ? file_get_contents($filePath) : "<!-- Missing file: $filePath -->";
        }
        //$filename = clsMain::$web_root . DIRECTORY_SEPARATOR . clsMain::$vars->folder . DIRECTORY_SEPARATOR . clsMain::$vars->file;
        //print $filename;
        $fileDetails=addslashes($fileDetails);
        return $fileDetails;
    }
        */

    public function retrieve_folder_file()
    {
        $fileDetails = "Hello World";
        //if (!empty(clsMain::$vars->file)) {
            /*
            $filePath = clsMain::$web_root 
                . DIRECTORY_SEPARATOR . clsMain::$vars->folder 
                . DIRECTORY_SEPARATOR . clsMain::$vars->file;
            */
            $filePath = clsMain::$web_root . DIRECTORY_SEPARATOR . clsMain::$vars->folder . DIRECTORY_SEPARATOR . clsMain::$vars->file;
            $fileDetails = file_exists($filePath) ? file_get_contents($filePath) : "<!-- Missing file: $filePath -->";
            $fileDetails=base64_encode($fileDetails);
            /*
            if (file_exists($filePath) && is_file($filePath)) {
                $fileDetails = file_get_contents($filePath);
            } else {
                $fileDetails = "<!-- Missing file: $filePath -->";
            }
            */
        //}

        // Encode safely for JS/HTML output
        return json_encode($fileDetails, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }


    public function retrieve_file_path($filename, $map_class_number)
    {
        //$offset = strlen($tag);
        //$filename = $filename = basename(substr($tag, $offset));
        //$filePath = rtrim($this->contentDir[$tag]) . DIRECTORY_SEPARATOR . $filename;
        //print $filename. $map_class_number;
        //print_r($this->contentDir);
        $filename =$this->contentDir[$map_class_number]. DIRECTORY_SEPARATOR.$filename;
        //print $filename;
        return $filename;
    }

    public function retrieve_match_array($tag)
    {   
        
        $match_variables=$this->match_array[$tag];
        //print "\n VVV->" . $tag . "| \n";
        //print_r($match_variables);
        //print "\n XXX->" . $tag . "| \n";
        if(is_array($match_variables))
        {
            //print "\n ->" . $tag . "| \n";
            $match = $this->match_array[$tag];
            $class_number = $match[1];
            $match_type = $match[2];
            $method = $this->class[$class_number];
        }else{
            //print "\n FFF->" . $tag . "| \n";
            $match = $this->match_array[""];
            $class_number = 4;
            $match_type = $match[2];
            $method = $this->class[$class_number];
        }
        
        $return =array($match,$class_number,$match_type,$method);
        return $return;
    }

    public function match_tag_split($tag = "")
    {
        $pos = strpos($tag,  ':');
        //echo"\n xxx".$pos." ";
        if(is_numeric($pos)){
            $offset = $pos +1;
            
            $filename = substr($tag, $offset);
            $tag = substr($tag, 0, $offset);
            //print ("hello->".$filename);
            $match_details = $this->retrieve_match_array($tag);
            //print ("\n hell->" . $filename);
            //print_r($match_details);
            //print ("\n hell \n");
            $match = $match_details[0];
            $class_number = $match_details[1];
            $match_type = $match_details[2];
            $method = $match_details[3];
        }else{
            $pos = strpos($tag, '->');
            if(is_numeric($pos)){
                $match = $this->match_array["->"];
                $class_number = 2;
                $match_type = $match[2];
                $method = $this->class[$class_number];
            }else{
                $match = $this->match_array[""];
                $class_number = 4;
                $match_type = $match[2];
                $method = $this->class[$class_number];
            }
            
        }
        
        
        
        switch($match_type) {
            case "split_half":
                $return = explode('->', $tag, 2);
                $class_details = array("execute_class"=>$return[0],"execute_method"=>$return[1], "map_class_number" =>$class_number);
                $return_arguments=$class_details;
                break;
            case "split_right":
                $offset = strlen($tag);
                $return["filename"] = $filename;//basename(substr($tag, $offset));
                $return["map_class_number"]=$class_number;
                $return_arguments=$return;
                break;
            case "split_none":
                $return_arguments=array("tag"=>$tag, "map_class_number" => $class_number);
                break;
            default:
                return null; // No match found
                
        }
        $return_array=array("class"=>$match,"map_class_number"=>$class_number,"match_type"=>$match_type,"call_method"=>$method,"arguments"=>$return_arguments);
        //print_r($return_array);
        return $return_array;
    }

    public function match_tag($tag = "")
    {
        $match_details=$this->match_tag_split($tag);
        /*
        $return_array= array("class"=>$match,"class_number"=>$class_number,"match_type"=>$match_type,
        "call_method"=>$method,"arguments"=>$return_arguments);
        */
        //print_r($match_details);
        //$function_arguments=array("method"=> $match_details["call_method"], "arguments"=>$match_details["arguments"]);
        
        return call_user_func_array([$this, $match_details["call_method"]], $match_details["arguments"]);
        
    }

    public function map_default($tag = "", $map_class_number = 0)
    {
        //$filename = $tag.'.html';
        $return_details="";
        
        if(isset(clsMain::$vars->page_titles[$tag])){
            $pos = strpos(clsMain::$vars->page_titles[$tag], '.html');
            if(is_numeric($pos)){
                $filename = clsMain::$vars->page_titles[$tag];
                $return_details=$this->retrieve_file($filename, $map_class_number);
            }else{
                $return_details=clsMain::$vars->page_titles[$tag];
            }
            
        }
        return $return_details;//$this->retrieve_file($filename, $map_class_number);
    }

    public function map_file($filename = "", $map_class_number = 0)
    {
        return $this->retrieve_file($filename, $map_class_number);
    }
    public function map_index($filename = "", $map_class_number =0)
    {
        return $this->retrieve_file($filename, $map_class_number);
    }
    public function map_include($filename = "", $map_class_number=0)
    {
        return $this->include_file($filename, $map_class_number);
    }

    

    public function map_class($execute_class="",$execute_method="",$map_class_number = 0)
    {
        //echo "\n map_class->" . $execute_class . "->" . $execute_method . "| \n";
        $return_details="";
        if (!isset(clsMain::$vars->$execute_class)) {
            $target_class = new $execute_class();
            clsMain::$vars->$execute_class = $target_class;
        }else{
            $target_class = clsMain::$vars->$execute_class;
        }
        $return_details = call_user_func([$target_class, $execute_method]);
        if (is_array($return_details)) {
            $return_details = var_export($return_details, true);
        }
        return $return_details;
        /*
        //if (class_exists($execute_class) && method_exists($execute_class, $execute_method)) {
        if (class_exists($execute_class) && method_exists($target_class, $execute_method)) {


            
            //$return_details= call_user_func([$obj, $execute_method]);
            $return_details = call_user_func([$target_class, $execute_method]);
            if(is_array($return_details)){
                $return_details=var_export($return_details,true);
            }
            return $return_details;
        } else {
            /*
            if (!isset(clsMain::$vars->$execute_class)) {
                $obj = new $execute_class();
                clsMain::$vars->$execute_class = $obj;
            }
            
            //$return_details= call_user_func([$obj, $execute_method]);
            //$return_details =call_user_func_array([$obj, $execute_method], array());
            $return_details = call_user_func([$target_class, $execute_method]);
            if (is_array($return_details)) {
                $return_details = var_export($return_details, true);
            }
            return $return_details;
            //return "<!--Map Class Missing class/method: $execute_class -->$execute_method";
        }
        */
        /*
        if (strpos($tag, '->') !== false) {
            list($class, $method) = explode('->', $tag, 2);
            if (class_exists($class) && method_exists($class, $method)) {
                $obj = new $class();
                return call_user_func([$obj, $method]);
            } else {
                return "<!-- Missing class/method: $tag -->";
            }
        }
            */
    }


    /*
    public function map($match="",$filename="", $contentDir="", $tag="") {
        // Case 1: File include
        if (strpos($tag, 'file:') === 0) {
            $filename = basename(substr($tag, 5));
            $filePath = rtrim($contentDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
            return file_exists($filePath) ? file_get_contents($filePath) : "<!-- Missing file: $filename -->";
        } elseif (strpos($tag, 'index:') === 0) {
            $contentDir = clsMain::$web_root;
            $filename = basename(substr($tag, 6));
            $filePath = rtrim($contentDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
            return file_exists($filePath) ? file_get_contents($filePath) : "<!-- Missing file: $filename -->";
        }

        // Case 2: Class->Method
        if (strpos($tag, '->') !== false) {
            list($class, $method) = explode('->', $tag, 2);
            if (class_exists($class) && method_exists($class, $method)) {
                $obj = new $class();
                return call_user_func([$obj, $method]);
            } else {
                return "<!-- Missing class/method: $tag -->";
            }
        }
    }

    */
    
}
