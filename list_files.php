<?php
// Start the session to check if it exists
session_start();

date_default_timezone_set('Asia/Kolkata');

// Check if the user_id session variable is NOT set
if (!isset($_SESSION['user_id'])) {
    
    // If not, send an unauthorized error (don't redirect a script)
    http_response_code(401); // 401 Unauthorized
    header('Content-Type: application/json');
    echo json_encode(['data' => [], 'error' => 'Not logged in.']);
    exit();
}
// --- Helper function to format bytes nicely ---
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// --- Main Script ---
$uploadDir = 'uploads/';
$data = [];

// Build the base URL for the download link
// This makes it work on 'localhost' or a real server
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptDir = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$baseUrl = $protocol . $host . $scriptDir;

// Scan the directory for files
$files = scandir($uploadDir);

foreach ($files as $file) {
    // Ignore '.' and '..' (directory navigation) and system files
    if ($file === '.' || $file === '..' || $file === '.htaccess') {
        continue;
    }

    $filePath = $uploadDir . $file;

    // Make sure it's a file, not a sub-directory
    if (is_file($filePath)) {
        $data[] = [
            'name' => htmlspecialchars($file),
            'size' => formatBytes(filesize($filePath)),
            'date' => date("Y-m-d H:i:s", filemtime($filePath)),
            'path' => $baseUrl . '/' . $uploadDir . $file
        ];
    }
}

// Send the data back as JSON, wrapped in a 'data' key for DataTables
header('Content-Type: application/json');
echo json_encode(['data' => $data]);

?>