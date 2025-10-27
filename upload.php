<?php
// Directory where files will be uploaded
$uploadDir = 'uploads/';

// Check if the 'uploads' directory exists, if not, create it
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Check if a file was uploaded
if (isset($_FILES['file'])) {
    
    $file = $_FILES['file'];
    
    // Get file details
    $fileName = basename($file['name']);
    $targetFilePath = $uploadDir . $fileName;
    $fileError = $file['error'];

    // Check for upload errors
    if ($fileError === UPLOAD_ERR_OK) {
        
        // Move the temporary file to the final destination
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            // File upload successful
            http_response_code(200); // Send 'OK' status
            echo json_encode(['message' => 'File uploaded successfully.']);
        } else {
            // Failed to move file
            http_response_code(500); // Server error
            echo json_encode(['message' => 'Failed to move uploaded file.']);
        }
    } else {
        // Handle specific upload errors
        http_response_code(400); // Bad request
        echo json_encode(['message' => 'File upload error: ' . $fileError]);
    }
} else {
    // No file was sent
    http_response_code(400);
    echo json_encode(['message' => 'No file received.']);
}
?>