<?php

class FileManager {
    private static $ROOT = "./FILE_DIR";
    private static function Root(string $path) {
        return dirname(__FILE__).'/FILE_DIR';
    }
    public static function GetFiles(): array {
        return FileManager::buildDirectoryTree(FileManager::$ROOT);
    }
    // Thanks GPT
    private static function buildDirectoryTree(string $dir, array &$visited = []): array {
        $result = [];

        // Use realpath to avoid recursion loops through symlinks
        $realBase = realpath($dir);
        if ($realBase === false) {
            return $result;
        }

        // Prevent cycles (especially important with symlinks)
        if (isset($visited[$realBase])) {
            return $result;
        }
        $visited[$realBase] = true;

        $items = scandir($dir);
        $hasContent = false;

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $hasContent = true;
            $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
            $isLink = is_link($fullPath);

            if (is_dir($fullPath)) {
                if ($isLink) {
                    // Symlink to directory: include as empty array
                    $result[$item] = [];
                } else {
                    // Regular directory: recurse
                    $result[$item] = FileManager::buildDirectoryTree($fullPath, $visited);
                }
            } elseif (is_file($fullPath) || $isLink) {
                // File or symlink to file
                $result[$item] = null;
            }
        }

        // If the directory had no content besides . and .., include it as empty
        if (!$hasContent) {
            return [];
        }

        return $result;
    }
    private static function isPathSafe(string $path): bool {
        // Normalize slashes
        $normalized = str_replace('\\', '/', $path);

        // Reject if path contains './' or '../' (including at the start or in the middle)
        return !preg_match('#(^|/)\.\.?(/|$)#', $normalized);
    }
    public static function MoveFile(string $from, string $to): bool|string {
        try {
            if (!FileManager::isPathSafe($from) || !FileManager::isPathSafe(path: $to)) return false;
            $full_old = FileManager::$ROOT . $from;
            $full_new = FileManager::$ROOT . $to;
            return rename($full_old, $full_new);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public static function CopyFile(string $from, string $to): bool|string {
        try {
            if (!FileManager::isPathSafe($from) || !FileManager::isPathSafe(path: $to)) return false;
            $full_old = FileManager::$ROOT . $from;
            $full_new = FileManager::$ROOT . $to;
            return copy($full_old, $full_new);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public static function DeleteFile(string $path): bool|string {
        try {
            if (!FileManager::isPathSafe($path)) return false;
            $full = FileManager::$ROOT . $path;
            return unlink($full);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public static function MakeDir(string $path): bool|string {
        try {
            if (!FileManager::isPathSafe($path)) return false;
            $full = FileManager::$ROOT . $path;
            return mkdir($full);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public static function DeleteDir(string $path) {
        try {
            if (!FileManager::isPathSafe($path)) return false;
            $full = FileManager::$ROOT . $path;
            return rmdir($full);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public static function UploadFile(string $path, array $files) {
        $file = $files['file_upload'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $filename = basename($file['name']);
            $targetPath = FileManager::$ROOT . $path . $filename;
            if (!FileManager::isPathSafe($path . $filename)) {
                return "Unsafe Path: $path $filename";
            } else {
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    return "Successful";
                } else {
                    return "(HTTP 500) Failed to move uploaded file.";
                }
            }
        } else {
            return "(HTTP 400) Upload error: " . $file['error'];
        }
    }
}