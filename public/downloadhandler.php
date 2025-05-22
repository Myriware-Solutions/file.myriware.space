<?php
$baseDir = './FILE_DIR/';
$file = $_GET['file'] ?? '';

$realPath = realpath($baseDir . $file);

// Ensure the file is within allowed base directory
if (!$realPath || strpos($realPath, $baseDir) !== 0 || !file_exists($realPath)) {
    http_response_code(404);
    echo "File not found or access denied: $realPath";
    exit;
}

$filename = basename($realPath);
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($realPath));

ob_clean();
flush();
readfile($realPath);
exit;