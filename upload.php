<?php
// Define the target upload directory
$upload_dir = 'uploads/';

// --- IMPORTANT ---
// Check if the 'uploads' directory exists.
// If not, try to create it with write permissions (0755).
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        // If creation fails, send an error
        http_response_code(500);
        echo "Error: Failed to create upload directory. Check permissions.";
        exit;
    }
}

// Check if a file was sent in the 'file' field
// This must match formData.append('file', file) from your JS
if (isset($_FILES['file'])) {
    
    $file = $_FILES['file'];
    
    // Get the original file name
    $file_name = basename($file['name']);
    $target_file = $upload_dir . $file_name;

    // Try to move the uploaded file from its temporary location
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        
        // Success! Send a 200 OK status back to the JavaScript
        http_response_code(200);
        echo "File uploaded successfully.";

    } else {
        // Failed to move file (this is almost ALWAYS a permissions problem)
        http_response_code(500); // Internal Server Error
        echo "Error: Failed to save file. Check 'uploads' folder permissions.";
    }
} else {
    // No file was sent
    http_response_code(400); // Bad Request
    echo "Error: No file sent.";
}
?>