<?php
class clsOutput {

    public function __construct()
    {
        $om = new clsOutput_Map();
        clsMain::$vars->om = $om;
    }
    public function resolveTags($template, $contentDir, $maxDepth = 10)
    {
        $pattern = '/\{\{([^\}]+)\}\}/';

        $depth = 0;
        do {
            $depth++;
            $changed = false;

            $newTemplate = preg_replace_callback($pattern, function ($matches) use ($contentDir, &$changed) {
                $tag = trim($matches[1]);
                $changed = true;

                

                return clsMain::$vars->om->match_tag($tag);
                
            }, $template);

            if ($newTemplate === $template) {
                // nothing changed -> break early
                break;
            }

            $template = $newTemplate;
        } while ($changed && $depth < $maxDepth);

        return $template;
    }
/*
    public function resolveTags($template, $contentDir, $maxDepth = 10)
    {
        $pattern = '/\{\{([^\}]+)\}\}/';

        $depth = 0;
        do {
            $depth++;
            $changed = false;

            $newTemplate = preg_replace_callback($pattern, function ($matches) use ($contentDir, &$changed) {
                $tag = trim($matches[1]);
                $changed = true;

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

                // Leave unknown tags as-is (so loop will eventually stop)
                return $matches[0];
            }, $template);

            if ($newTemplate === $template) {
                // nothing changed -> break early
                break;
            }

            $template = $newTemplate;
        } while ($changed && $depth < $maxDepth);

        return $template;
    }
    */

    public function output($content) {
        header('Content-Type: text/html; charset=utf-8');
        echo $content;
    }
}
