<?php

class FileManager {
    private static $ROOT = "./FILE_DIR";
    public static function GetFiles(): array {
        $file_paths = FileManager::getFilesRecursive(FileManager::$ROOT, FileManager::$ROOT);
        return FileManager::buildDirectoryTree($file_paths);
    }
    // Thanks GPT
    private static function getFilesRecursive(string $dir, string $baseDir = null): array {
        $files = [];
        $baseDir ??= realpath($dir);

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS|FilesystemIterator::FOLLOW_SYMLINKS)
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', substr($fileInfo->getPathname(), strlen($baseDir) + 1));
                $files[] = $relativePath;
            }
        }

        return $files;
    }
    private static function buildDirectoryTree(array $paths): array {
        $tree = [];

        foreach ($paths as $path) {
            $parts = explode('/', $path);
            $current = &$tree;

            foreach ($parts as $index => $part) {
                if ($index === count($parts) - 1) {
                    // Last part is a file
                    $current[$part] = null;
                } else {
                    // Directory
                    if (!isset($current[$part])) {
                        $current[$part] = [];
                    }
                    $current = &$current[$part];
                }
            }
        }

        return $tree;
    }
    private static function isPathSafe(string $path): bool {
        // Normalize slashes
        $normalized = str_replace('\\', '/', $path);

        // Reject if path contains './' or '../' (including at the start or in the middle)
        return !preg_match('#(^|/)\.\.?(/|$)#', $normalized);
    }

    public static function MoveFile(string $old, string $new) {
        if (!FileManager::isPathSafe($old) || !FileManager::isPathSafe(path: $new)) return;
        $full_old = FileManager::$ROOT . $old;
        $full_new = FileManager::$ROOT . $old;
        rename($full_old, $full_new);
    }
    public static function CopyFile(string $from, string $to) {
        if (!FileManager::isPathSafe($from) || !FileManager::isPathSafe(path: $to)) return;
        $full_old = FileManager::$ROOT . $from;
        $full_new = FileManager::$ROOT . $to;
        copy($full_old, $full_new);
    }
    public static function DeleteFile(string $path) {
        if (!FileManager::isPathSafe($path)) return;
        $full = FileManager::$ROOT . $path;
        unlink($full);
    }
}