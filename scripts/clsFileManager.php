<?php
class FileManager
{
    private string $root;

    public function __construct(string $rootDir = '.')
    {
        $this->root = realpath($rootDir);
    }

    private function sanitizePath(string $path): string
    {
        $fullPath = realpath($path);
        if (!$fullPath || strpos($fullPath, $this->root) !== 0) {
            return $this->root;
        }
        return $fullPath;
    }

    public function list(string $dir = ''): array
    {
        $dir = $this->sanitizePath($dir);
        $files = scandir($dir);
        return array_map(function ($file) use ($dir) {
            if ($file === '.')
                return null;
            $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
            return [
                'name' => $file,
                'path' => $fullPath,
                'is_dir' => is_dir($fullPath),
                'size' => is_file($fullPath) ? filesize($fullPath) : null
            ];
        }, $files);
    }

    public function view(string $file): string
    {
        $file = $this->sanitizePath($file);
        if (!is_file($file))
            throw new Exception("File not found");
        return file_get_contents($file);
    }

    public function delete(string $file): bool
    {
        $file = $this->sanitizePath($file);
        if (!is_file($file))
            throw new Exception("Invalid file");
        return unlink($file);
    }
}
