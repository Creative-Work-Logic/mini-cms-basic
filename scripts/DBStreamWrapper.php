<?php
class DBStreamWrapper {
    private $handle = null;   // real file handle
    private $data   = null;   // DB content
    private $pos    = 0;

    private $basePath = "D:\\Library\\Repositorys\\Mini-CMS\\mini-cms-basic\\mini-cms-basic\\";

    

    public function stream_open($path, $mode, $options, &$opened_path) {
        $realPath = $path;

        $this->handle = fopen('\\\\?\\' . $path, $mode);


        /*
    $scheme = parse_url($path, PHP_URL_SCHEME);
    $rawPath = $path;

    // Only strip scheme if present
    if ($scheme === 'file') {
        $rawPath = parse_url($path, PHP_URL_PATH);
    }

    // Make absolute path
    if (!preg_match('#^[a-zA-Z]:\\\\#', $rawPath) && strpos($rawPath, '/') !== 0) {
        $rawPath = $this->basePath . ltrim($rawPath, '/\\');
    }
    
    // ---- Safe filesystem check ----
    if (@is_file($rawPath)) {   // Use @ to suppress wrapper recursion warnings
        $this->handle = fopen($rawPath, $mode);
        return $this->handle !== false;
    }
        */
    exit("error");
    // ---- DB fallback ----
    $key = ltrim(parse_url($path, PHP_URL_PATH) ?? $path, '/\\');
    $this->data = $this->loadFromDatabase($key);
    $this->pos = 0;
    return $this->data !== false;
}


// Helper
function is_absolute_path($path) {
    return preg_match('#^[a-zA-Z]:\\\\#', $path) || strpos($path, '/') === 0;
}




    public function stream_read($count) {
        if ($this->handle) {
            return fread($this->handle, $count);
        }
        $chunk = substr($this->data, $this->pos, $count);
        $this->pos += strlen($chunk);
        return $chunk;
    }

    public function stream_eof() {
        if ($this->handle) {
            return feof($this->handle);
        }
        return $this->pos >= strlen($this->data);
    }

    public function stream_stat() {
        if ($this->handle) {
            return fstat($this->handle);
        }
        return $this->buildStat(strlen($this->data));
    }

    public function url_stat($path, $flags) {
        if (is_file($path)) {
            return stat($path);
        }
        $relative = $this->toRelative($path);
        $data = $this->loadFromDatabase($relative);
        if ($data === false) {
            return false;
        }
        return $this->buildStat(strlen($data));
    }

    // --- Helpers ---
    private function toRelative($fullPath) {
        return ltrim(str_replace($this->basePath, '', $fullPath), '\\/');
    }

    private function buildStat($size) {
        return [
            7 => $size,
            'size' => $size,
            'mode' => 0100444,
            'mtime' => time(),
        ];
    }

    private function loadFromDatabase($key) {
        // Example with PDO + MySQL
        
        static $pdo = null;
        if (!$pdo) {
            $pdo = new PDO("mysql:host=localhost;dbname=mycms", "user", "pass");
        }
        $stmt = $pdo->prepare("SELECT content FROM files WHERE path = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['content'] : false;
    }
}

/*
// Replace built-in "file://" handler
stream_wrapper_unregister("file");
stream_wrapper_register("file", "HybridFileStreamWrapper");

// Example usage:
echo file_get_contents("D:\\Library\\Repositorys\\Mini-CMS\\mini-cms-basic\\mini-cms-basic\\templates\\main.html");
*/
