<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files</title>
    <link rel="stylesheet" href="style.css">
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>

    <div class="files-container">
        <h2>Uploaded Files</h2>
        <table id="filesTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Size</th>
                    <th>Date Uploaded</th>
                    <th>Share Link</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
        
        <p style="text-align: center; margin-top: 20px;">
            <a href="index.html">Back to Uploader</a>
        </p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script src="files.js"></script> 

</body>
</html>