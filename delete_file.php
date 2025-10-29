<?php
session_start();

// Set content type to JSON for all responses
header('Content-Type: application/json');

// 1. Security: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'You are not logged in.']);
    exit();
}

// 2. Check if file_path is provided
if (!isset($_POST['file_path'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'No file path specified.']);
    exit();
}

// 3. Define the *local server path* to the uploads folder
// realpath() makes it a secure, absolute path
$baseUploadDir = realpath(__DIR__ . '/uploads');

if (!$baseUploadDir) {
     http_response_code(500); // Server error
     echo json_encode(['status' => 'error', 'message' => 'Server configuration error: Upload directory not found.']);
     exit();
}

// 4. --- THIS IS THE KEY CHANGE ---
// Get *only* the filename from the URL.
// This is much safer than parsing the whole URL.
// It stops "directory traversal" attacks (e.g., ../../index.php)
$filename = basename($_POST['file_path']);

if (empty($filename)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Invalid file name.']);
    exit();
}

// 5. Build the full, trusted *local server path* to the file
// DIRECTORY_SEPARATOR uses '\' on Windows and '/' on Linux
$fileToDelete = $baseUploadDir . DIRECTORY_SEPARATOR . $filename;

// 6. Final Security Check:
// Check if the file path is valid and is *still* inside the uploads folder.
// realpath() will return 'false' if the file doesn't exist.
$realFileToDelete = realpath($fileToDelete);

// This check catches the "Invalid file path" error
if ($realFileToDelete === false || strpos($realFileToDelete, $baseUploadDir) !== 0) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Invalid file path or permission denied.']);
    exit();
}

// 7. Attempt to delete the file
if (unlink($realFileToDelete)) {
    // Success
    echo json_encode(['status' => 'success', 'message' => 'File deleted.']);
} else {
    // Failed to delete (e.g., file permissions)
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Could not delete the file. Check server permissions.']);
}
exit();
?>