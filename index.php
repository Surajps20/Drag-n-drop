<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Page</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">      


<style>
        /* This new wrapper will hold both items */
        .page-wrapper {
            /* This ensures it matches the width of your uploader */
            width: 100%; 
            max-width: 600px; /* Adjust this to match your .upload-container max-width */
            
            /* This will stack the uploader and timer vertically */
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>
<body>

    <div class="page-wrapper">

        <div class="upload-container">
            <h2>Select the file you want to upload</h2>
            <p style="text-align: center; margin-top: 20px;">
                </p>


            <div class="drop-zone" id="dropZone">
                <p>Drop files here to start uploading</p>
                <p>or</p>
                <button type="button" id="selectFileBtn">Select File/Folder</button>
            </div>

            <input type="file" id="fileInput" multiple hidden>
            <input type="file" id="folderInput" webkitdirectory multiple hidden>

            <div class="file-list" id="fileList"></div>
        </div>
        
       

    </div> <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="files.js"></script>
    <script src="upload.js"></script>

</body>
</html>