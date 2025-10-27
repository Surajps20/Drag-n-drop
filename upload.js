// Get all the necessary elements from the DOM
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const selectFileBtn = document.getElementById('selectFileBtn');
const fileList = document.getElementById('fileList');

// URL for your server-side upload script
const uploadURL = 'upload.php'; 

// --- Event Listeners ---

// 1. Click "Select File" button
selectFileBtn.addEventListener('click', () => {
    fileInput.click();
});

// 2. Listen for file selection
fileInput.addEventListener('change', () => {
    handleFiles(fileInput.files);
    
    // *** THIS IS THE FIX ***
    // Reset the input value. This allows the 'change' event to
    // fire again, even if the same file is selected.
    fileInput.value = null;
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

function handleFiles(files) {
    // Clear the list if you want to upload one at a time
    // fileList.innerHTML = ""; // Uncomment this if you only want to show the current upload
    
    [...files].forEach(uploadFile);
}

function uploadFile(file) {
    // --- Create visual elements ---
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
    fileStatus.textContent = 'Uploading...';
    fileStatus.style.color = '#666';

    fileProgress.appendChild(progressBar);
    fileItem.appendChild(fileName);
    fileItem.appendChild(fileProgress);
    fileItem.appendChild(fileStatus);
    fileList.appendChild(fileItem);

    // --- AJAX Upload Logic ---
    const formData = new FormData();
    formData.append('file', file); 

    const xhr = new XMLHttpRequest();
    xhr.open('POST', uploadURL, true);

    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            progressBar.style.width = percentComplete + '%';
        }
    });

    xhr.onload = () => {
        progressBar.style.width = '100%';
        if (xhr.status === 200) {
            fileStatus.textContent = 'Uploaded';
            fileStatus.style.color = '#28a745';
        } else {
            // Display a more specific error
            fileStatus.textContent = 'Upload Failed';
            fileStatus.style.color = '#dc3545';
            progressBar.style.backgroundColor = '#dc3545';
        }
    };

    xhr.onerror = () => {
        fileStatus.textContent = 'Network Error';
        fileStatus.style.color = '#dc3545';
        progressBar.style.backgroundColor = '#dc3545';
    };

    xhr.send(formData);
}