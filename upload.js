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
        const uniqueId = `${file.name}-${file.size}-${file.lastModified}`;
        
        // Start the upload process for this file
        uploadFile(file, uniqueId);
    });
}

/**
 * NEW HELPER FUNCTION
 * Formats bytes into a human-readable string (KB, MB, GB).
 */
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

/**
 * Creates the UI elements (NOW BOOTSTRAP-COMPATIBLE)
 * and starts the chunking process.
 */
function uploadFile(file, uniqueId) {
    
    // --- Create Bootstrap-styled visual elements ---
    const fileItem = document.createElement('div');
    fileItem.className = 'd-flex align-items-center justify-content-between p-2 bg-body-secondary rounded-2 mb-2';

    const fileName = document.createElement('div');
    fileName.className = 'file-name small text-truncate pe-2';
    fileName.textContent = file.name;

    // Progress wrapper
    const progressWrapper = document.createElement('div');
    progressWrapper.className = 'flex-grow-1';
    progressWrapper.style.maxWidth = '120px';

    const progressContainer = document.createElement('div');
    progressContainer.className = 'progress';
    progressContainer.setAttribute('role', 'progressbar');
    progressContainer.style.height = '8px';

    const progressBar = document.createElement('div');
    progressBar.className = 'progress-bar'; // 'bg-success' / 'bg-danger' will be added later
    progressBar.style.width = '0%';

    const fileStatus = document.createElement('div');
    fileStatus.className = 'file-status small text-muted ps-2';
    // --- MODIFIED --- Give it more space for "X MB / Y MB"
    fileStatus.style.minWidth = '130px'; 
    fileStatus.style.textAlign = 'right';
    fileStatus.textContent = 'Preparing...';

    // Assemble the elements
    progressContainer.appendChild(progressBar);
    progressWrapper.appendChild(progressContainer);

    fileItem.appendChild(fileName);
    fileItem.appendChild(progressWrapper);
    fileItem.appendChild(fileStatus);
    fileList.appendChild(fileItem);

    // --- Chunking Logic ---
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    let currentChunk = 0;

    // Start uploading the first chunk
    // --- MODIFIED --- Pass 'fileItem' as a new argument
    uploadChunk(file, uniqueId, currentChunk, totalChunks, progressBar, fileStatus, fileItem);
}

/**
 * This function recursively uploads one chunk at a time.
 * --- MODIFIED --- Added 'fileItem' to parameters
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

            // --- MODIFIED (Task 2: Show numerical value) ---
            const formattedUploaded = formatBytes(bytesUploaded);
            const formattedTotal = formatBytes(file.size);
            fileStatus.textContent = `${formattedUploaded} / ${formattedTotal}`;
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
                        // --- MODIFIED --- Pass 'fileItem' in the recursive call
                        uploadChunk(file, uniqueId, currentChunk, totalChunks, progressBar, fileStatus, fileItem);
                    
                    } else if (response.status === 'complete') {
                        // --- SWEETALERT SUCCESS ---
                        // 1. Update UI
                        progressBar.style.width = '100%';
                        progressBar.classList.add('bg-success');
                        fileStatus.textContent = 'Done'; // Final status
                        fileStatus.classList.remove('text-muted');
                        fileStatus.classList.add('text-success');

                        // 2. Show Success Toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Upload complete!',
                            text: file.name, // Show which file finished
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        // --- NEW (Task 1: Disappear after 30sec) ---
                        setTimeout(() => {
                            fileItem.remove(); 
                        }, 30000); // 30,000 milliseconds = 30 seconds
                        
                    }
                } else {
                    // Server returned a JSON error (e.g., "file type not allowed")
                    handleUploadError(response.error, fileStatus, progressBar, file.name);
                }
            } catch (e) {
                // JSON parsing failed
                handleUploadError('Invalid server response.', fileStatus, progressBar, file.name);
            }
        } else {
            // HTTP error (e.g., 404, 500)
            handleUploadError(`Upload Failed (Status: ${xhr.status})`, fileStatus, progressBar, file.name);
        }
    };

    xhr.onerror = () => {
        // Network Error
        handleUploadError('Network Error', fileStatus, progressBar, file.name);
    };

    xhr.send(formData);
}

/**
 * A helper function to show an error state on the UI
 * and trigger a SweetAlert modal.
 */
function handleUploadError(message, fileStatus, progressBar, fileName) {
    // 1. Update the specific file's UI
    fileStatus.textContent = 'Failed';
    fileStatus.classList.remove('text-muted');
    fileStatus.classList.add('text-danger');
    progressBar.classList.add('bg-danger');
    progressBar.style.width = '100%'; // Show a full red bar

    // 2. --- SWEETALERT ERROR MODAL ---
    // Show a modal (not a toast) for errors as they are more important
    Swal.fire({
        icon: 'error',
        title: 'Upload Failed',
        text: `Could not upload "${fileName}": ${message}`,
        confirmButtonColor: '#d33' // Use Bootstrap's danger color
    });
}