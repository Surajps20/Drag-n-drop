// Get all the necessary elements from the DOM
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const selectFileBtn = document.getElementById('selectFileBtn');
const fileList = document.getElementById('fileList');

// URL for your server-side upload script
const uploadURL = 'upload.php';
// Define the size of each chunk (e.g., 5MB)
const CHUNK_SIZE = 5 * 1024 * 1024;

// --- Event Listeners (Same as before) ---
// ... (No changes in this section) ...

// 1. Click "Select File" button
selectFileBtn.addEventListener('click', () => {
    fileInput.click();
});

// 2. Listen for file selection
fileInput.addEventListener('change', () => {
    handleFiles(fileInput.files);
    fileInput.value = null; // Reset input
});

// 3. Drag and Drop: Prevent default browser behavior
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

// 4. Highlight drop zone
['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});
['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('dragover');
}
function unhighlight(e) {
    dropZone.classList.remove('dragover');
}

// 5. Handle the file drop
dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles(files);
}

// --- Main Functions ---

/**
 * Iterates over the selected files and starts the upload process for each.
 */
function handleFiles(files) {
    [...files].forEach(file => {
        // Generate a unique ID for this specific file upload
        // This allows the server to correctly reassemble the file
        const uniqueId = `${file.name}-${file.size}-${file.lastModified}`;
        
        // Start the upload process for this file
        uploadFile(file, uniqueId);
    });
}

/**
 * Creates the UI elements for the file and starts the chunking process.
 */
function uploadFile(file, uniqueId) {
    // --- Create visual elements (same as before) ---
    const fileItem = document.createElement('div');
    fileItem.className = 'file-item';

    const fileName = document.createElement('span');
    fileName.className = 'file-name';
    fileName.textContent = file.name;

    const fileProgress = document.createElement('div');
    fileProgress.className = 'file-progress';
    
    const progressBar = document.createElement('div');
    progressBar.className = 'progress-bar';

    const fileStatus = document.createElement('span');
    fileStatus.className = 'file-status';
    fileStatus.textContent = 'Preparing...';
    fileStatus.style.color = '#666';

    fileProgress.appendChild(progressBar);
    fileItem.appendChild(fileName);
    fileItem.appendChild(fileProgress);
    fileItem.appendChild(fileStatus);
    fileList.appendChild(fileItem);

    // --- NEW Chunking Logic ---
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    let currentChunk = 0;

    // Start uploading the first chunk
    // --- MODIFIED: Pass 'fileItem' as a new argument ---
    uploadChunk(file, uniqueId, currentChunk, totalChunks, progressBar, fileStatus, fileItem);
}

/**
 * This function recursively uploads one chunk at a time.
 * --- MODIFIED: Added 'fileItem' to the function signature ---
 */
function uploadChunk(file, uniqueId, currentChunk, totalChunks, progressBar, fileStatus, fileItem) {
    const start = currentChunk * CHUNK_SIZE;
    const end = Math.min(start + CHUNK_SIZE, file.size);
    const chunk = file.slice(start, end);

    const formData = new FormData();
    formData.append('fileChunk', chunk);
    formData.append('fileName', file.name);
    formData.append('currentChunk', currentChunk);
    formData.append('totalChunks', totalChunks);
    formData.append('uniqueId', uniqueId);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', uploadURL, true);

    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            // Calculate total progress, not just chunk progress
            const bytesUploaded = (currentChunk * CHUNK_SIZE) + e.loaded;
            const percentComplete = Math.round((bytesUploaded / file.size) * 100);
            progressBar.style.width = percentComplete + '%';
        }
    });

    xhr.onload = () => {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);

                if (response.success) {
                    if (response.status === 'chunk_uploaded') {
                        // Upload the next chunk
                        currentChunk++;
                        fileStatus.textContent = `Uploading chunk ${currentChunk + 1}/${totalChunks}`;
                        // --- MODIFIED: Pass 'fileItem' in the recursive call ---
                        uploadChunk(file, uniqueId, currentChunk, totalChunks, progressBar, fileStatus, fileItem);
                    
                    } else if (response.status === 'complete') {
                        // File upload is finished
                        progressBar.style.width = '100%';
                        fileStatus.textContent = 'Uploaded';
                        fileStatus.style.color = '#28a745';

                        // --- NEW ---
                        // Automatically remove the item after 30 seconds
                        setTimeout(() => {
                            // Add a little fade-out animation for a smooth removal
                            fileItem.style.transition = 'opacity 0.5s ease-out';
                            fileItem.style.opacity = '0';
                            
                            // Wait for the animation to finish, then remove
                            setTimeout(() => {
                                fileItem.remove();
                            }, 500); // 0.5 seconds
                            
                        }, 30000); // 30,000 milliseconds = 30 seconds
                        // --- END NEW ---
                    }
                } else {
                    // Server returned a JSON error
                    handleUploadError(response.error, fileStatus, progressBar);
                }
            } catch (e) {
                // JSON parsing failed
                handleUploadError('Invalid server response.', fileStatus, progressBar);
            }
        } else {
            // HTTP error
            handleUploadError(`Upload Failed (Status: ${xhr.status})`, fileStatus, progressBar);
        }
    };

    xhr.onerror = () => {
        handleUploadError('Network Error', fileStatus, progressBar);
    };

    xhr.send(formData);
}

/**
 * A helper function to show an error state on the UI.
 */
function handleUploadError(message, fileStatus, progressBar) {
    fileStatus.textContent = message;
    fileStatus.style.color = '#dc3545';
    progressBar.style.backgroundColor = '#dc3545';
}