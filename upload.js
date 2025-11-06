// Get all the necessary elements from the DOM
const dropZone = document.getElementById("dropZone");
const fileInput = document.getElementById("fileInput");
const selectFileBtn = document.getElementById("selectFileBtn");

// Get new folder-upload elements
const selectFolderBtn = document.getElementById("selectFolderBtn");
const folderInput = document.getElementById("folderInput");

const fileList = document.getElementById("fileList");

// URL for your server-side upload script
const uploadURL = "upload.php";
// Define the size of each chunk (e.g., 5MB)
const CHUNK_SIZE = 5 * 1024 * 1024;

// --- Event Listeners (Unchanged) ---
selectFileBtn.addEventListener("click", () => {
  fileInput.click();
});
fileInput.addEventListener("change", () => {
  handleFiles(fileInput.files);
  fileInput.value = null;
});
selectFolderBtn.addEventListener("click", () => {
  folderInput.click();
});
folderInput.addEventListener("change", () => {
  handleFiles(folderInput.files);
  folderInput.value = null;
});
["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
  dropZone.addEventListener(eventName, preventDefaults, false);
});
function preventDefaults(e) {
  e.preventDefault();
  e.stopPropagation();
}
["dragenter", "dragover"].forEach((eventName) => {
  dropZone.addEventListener(eventName, highlight, false);
});
["dragleave", "drop"].forEach((eventName) => {
  dropZone.addEventListener(eventName, unhighlight, false);
});
function highlight(e) {
  dropZone.classList.add("dragover");
}
function unhighlight(e) {
  dropZone.classList.remove("dragover");
}
dropZone.addEventListener("drop", handleDrop, false);
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
  [...files].forEach((file) => {
    // === THIS IS THE NEW FIX ===
    // If the file size is 0 AND it has no file type,
    // it's a folder object. Skip it.
    if (file.size === 0 && file.type === "") {
      console.log("Skipping folder object:", file.name);
      return; // 'return' here is like 'continue' in a forEach
    }
    // === END NEW FIX ===

    const uniqueId = `${file.webkitRelativePath || file.name}-${file.size}-${
      file.lastModified
    }`;
    uploadFile(file, uniqueId);
  });
}

/**
 * Creates the UI elements for the file and starts the chunking process.
 */
function uploadFile(file, uniqueId) {
  // --- Create visual elements ---
  const fileItem = document.createElement("div");
  fileItem.className = "file-item";
  const fileName = document.createElement("span");
  fileName.className = "file-name";
  fileName.textContent = file.webkitRelativePath || file.name;
  const fileProgress = document.createElement("div");
  fileProgress.className = "file-progress";
  const progressBar = document.createElement("div");
  progressBar.className = "progress-bar";
  const fileStatus = document.createElement("span");
  fileStatus.className = "file-status";
  fileStatus.textContent = "Preparing...";
  fileStatus.style.color = "#666";
  fileProgress.appendChild(progressBar);
  fileItem.appendChild(fileName);
  fileItem.appendChild(fileProgress);
  fileItem.appendChild(fileStatus);
  fileList.appendChild(fileItem);

  // --- Chunking Logic ---

  // === THIS FIX STOPS THE LOOP FOR 0-BYTE FILES ===
  const totalChunks = Math.max(1, Math.ceil(file.size / CHUNK_SIZE));
  // === END FIX ===

  let currentChunk = 0;
  uploadChunk(
    file,
    uniqueId,
    currentChunk,
    totalChunks,
    progressBar,
    fileStatus,
    fileItem
  );
}

/**
 * This function recursively uploads one chunk at a time.
 * (Includes your 30-second auto-remove)
 */
function uploadChunk(
  file,
  uniqueId,
  currentChunk,
  totalChunks,
  progressBar,
  fileStatus,
  fileItem
) {
  const start = currentChunk * CHUNK_SIZE;
  const end = Math.min(start + CHUNK_SIZE, file.size);
  const chunk = file.slice(start, end);

  const formData = new FormData();
  formData.append("fileChunk", chunk);
  formData.append("relativePath", file.webkitRelativePath || file.name);
  formData.append("currentChunk", currentChunk);
  formData.append("totalChunks", totalChunks);
  formData.append("uniqueId", uniqueId);
  formData.append("fileName", file.name);

  const xhr = new XMLHttpRequest();
  xhr.open("POST", uploadURL, true);

  xhr.upload.addEventListener("progress", (e) => {
    if (e.lengthComputable) {
      const bytesUploaded = currentChunk * CHUNK_SIZE + e.loaded;
      const percentComplete = Math.round((bytesUploaded / file.size) * 100);
      progressBar.style.width = percentComplete + "%";
    }
  });

  xhr.onload = () => {
    if (xhr.status === 200) {
      try {
        const response = JSON.parse(xhr.responseText);
        if (response.success) {
          if (response.status === "chunk_uploaded") {
            currentChunk++;
            fileStatus.textContent = `Uploading chunk ${
              currentChunk + 1
            }/${totalChunks}`;
            uploadChunk(
              file,
              uniqueId,
              currentChunk,
              totalChunks,
              progressBar,
              fileStatus,
              fileItem
            );
          } else if (response.status === "complete") {
            progressBar.style.width = "100%";
            fileStatus.textContent = "Uploaded";
            fileStatus.style.color = "#28a745";

            // --- Your auto-remove code ---
            setTimeout(() => {
              fileItem.style.transition = "opacity 0.5s ease-out";
              fileItem.style.opacity = "0";
              setTimeout(() => {
                fileItem.remove();
              }, 500);
            }, 30000);
          }
        } else {
          handleUploadError(response.error, fileStatus, progressBar);
        }
      } catch (e) {
        handleUploadError("Invalid server response.", fileStatus, progressBar);
      }
    } else {
      handleUploadError(
        `Upload Failed (Status: ${xhr.status})`,
        fileStatus,
        progressBar
      );
    }
  };
  xhr.onerror = () => {
    handleUploadError("Network Error", fileStatus, progressBar);
  };
  xhr.send(formData);
}

/**
 * A helper function to show an error state on the UI.
 */
function handleUploadError(message, fileStatus, progressBar) {
  fileStatus.textContent = message;
  fileStatus.style.color = "#dc3545";
  progressBar.style.backgroundColor = "#dc3545";
}