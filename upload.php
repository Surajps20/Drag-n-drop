<?php
// Set headers
header('Content-Type: application/json');

// --- Configuration ---
$uploadDir = 'uploads/';
$maxFileSize = 5 * 1024 * 1024; // 5MB chunk size (must match JS)

// --- Helper Functions ---
function sendJsonError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

function sendJsonSuccess($data = []) {
    http_response_code(200);
    echo json_encode(array_merge(['success' => true], $data));
    exit;
}

// --- Check for required parameters ---
if (!isset($_POST['fileName'], $_POST['currentChunk'], $_POST['totalChunks'], $_POST['uniqueId'])) {
    sendJsonError('Missing required metadata.');
}

// --- Get Metadata ---
$fileName = basename($_POST['fileName']); // Sanitize file name
$currentChunk = (int)$_POST['currentChunk'];
$totalChunks = (int)$_POST['totalChunks'];
$uniqueId = basename($_POST['uniqueId']); // Sanitize unique ID

// --- Define File Paths ---
$tempFileName = $uniqueId . '.tmp';
$tempFilePath = $uploadDir . $tempFileName;
$finalFilePath = $uploadDir . $fileName;

// --- Check for Uploaded Chunk ---
if (!isset($_FILES['fileChunk']) || $_FILES['fileChunk']['error'] !== UPLOAD_ERR_OK) {
    sendJsonError('File chunk upload error. Code: ' . $_FILES['fileChunk']['error']);
}

$chunkTmpName = $_FILES['fileChunk']['tmp_name'];

// --- Open the temporary file in "append binary" mode ---
// 'a' = append, 'b' = binary safe
$fileHandle = fopen($tempFilePath, 'ab');
if (!$fileHandle) {
    sendJsonError('Failed to open temporary file for writing.', 500);
}

// Read the uploaded chunk and write it to the temp file
$chunkData = file_get_contents($chunkTmpName);
if ($chunkData === false) {
    fclose($fileHandle);
    sendJsonError('Failed to read chunk data.', 500);
}

fwrite($fileHandle, $chunkData);
fclose($fileHandle);

// --- Clean up the temporary chunk file ---
// This is the file PHP stored in its /tmp/ directory
unlink($chunkTmpName);

// --- Check if this was the last chunk ---
if ($currentChunk === $totalChunks - 1) {
    // This was the last chunk, rename the file to its final name
    if (rename($tempFilePath, $finalFilePath)) {
        sendJsonSuccess(['status' => 'complete', 'message' => 'File upload complete.']);
    } else {
        // If rename fails, try to delete the temp file
        unlink($tempFilePath);
        sendJsonError('Could not rename temporary file to final file.', 500);
    }
} else {
    // Not the last chunk, just acknowledge success
    sendJsonSuccess(['status' => 'chunk_uploaded', 'message' => 'Chunk ' . $currentChunk . ' uploaded.']);
}
?>