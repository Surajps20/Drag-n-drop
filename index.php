<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Page</title>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="style.css">

    <?php
    // Include your new countdown timer file
    include 'countdown.php';
    ?>
</head>

<body class="bg-light">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="d-flex flex-column align-items-center">

                    <div class="upload-container bg-white border rounded-3 p-4 p-md-5 shadow-sm w-100">

                        <h2 class="h4 text-dark text-center mb-4">Select the file you want to upload</h2>

                        <div class="drop-zone text-center p-5 rounded-3 border border-2 border-primary border-dashed bg-body-secondary" id="dropZone">
                            <p class="text-muted mb-2">Drop files here to start uploading</p>
                            <p class="text-muted small my-2">or</p>

                            <button type="button" id="selectFileBtn" class="btn btn-primary">Select File</button>
                        </div>

                        <input type="file" id="fileInput" accept="*/*" multiple hidden>

                        <div class="file-list mt-4" id="fileList">
                        </div>

                    </div>
                    <?php
                    // The timer call stays here, inside the flex column
                    display_countdown_timer();
                    ?>

                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="files.js"></script>
    <script src="upload.js"></script>

</body>

</html>