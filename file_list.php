<?php
// Start the session to check if it exists
session_start();

// Check if the user_id session variable is NOT set
if (!isset($_SESSION['user_id'])) {
    // If not set, they are not logged in. Redirect to login page.
    header('Location: login.php');
    exit();
}

// Include your countdown timer file
// Best practice to include PHP files before any HTML output
include 'countdown.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        /* --- CUSTOM RESPONSIVE "CARD" TABLE --- */
        /* This is the only CSS we keep, as it creates your 
           desired mobile "card" view for the table. */
        @media (max-width: 768px) {

            /* --- 1. Responsive "Stacked Card" Table --- */
            /* Hide the original table headers */
            table#filesTable thead {
                display: none;
            }

            /* Make everything a full-width block */
            table#filesTable tbody,
            table#filesTable tr,
            table#filesTable td {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            /* This is our new "card" */
            table#filesTable tr {
                border: 1px solid #ddd;
                border-radius: 8px;
                margin-bottom: 1.5rem;
                padding: 0.5rem;
            }

            table#filesTable td {
                border: none;
                border-bottom: 1px solid #f0f0f0;
                padding: 0.75rem;
                padding-left: 45%;
                /* Make room for the label */
                position: relative;
                text-align: right;
                /* Align cell content to the right */
                height: auto;
            }

            table#filesTable td:last-child {
                border-bottom: none;
                /* No border on the last cell */
            }

            /* Create the new "labels" */
            table#filesTable td::before {
                /* This content is set in the 'nth-of-type' rules below */
                position: absolute;
                left: 0.75rem;
                width: 40%;
                text-align: left;
                font-weight: 600;
                color: #333;
            }

            /* --- 2. Custom Labels & Alignment --- */

            /* File Name: Make this the "title" of the card */
            table#filesTable td:nth-of-type(1) {
                text-align: left;
                font-size: 1.1rem;
                font-weight: 600;
                padding-left: 0.75rem;
                word-break: break-all;
                /* Break long file names */
            }

            table#filesTable td:nth-of-type(1)::before {
                display: none;
                /* No label for File Name */
            }

            /* Size */
            table#filesTable td:nth-of-type(2)::before {
                content: "Size:";
            }

            /* Date Uploaded */
            table#filesTable td:nth-of-type(3)::before {
                content: "Date Uploaded:";
            }

            /* Button rows: Center the buttons */
            table#filesTable td:nth-of-type(4),
            table#filesTable td:nth-of-type(5),
            table#filesTable td:nth-of-type(6) {
                text-align: center;
                padding: 0.75rem;
            }

            table#filesTable td:nth-of-type(4)::before,
            table#filesTable td:nth-of-type(5)::before,
            table#filesTable td:nth-of-type(6)::before {
                display: none;
                /* No labels for buttons */
            }

            /* Make buttons bigger and easier to tap */
            /* We target the Bootstrap .btn class *within* this media query */
            table#filesTable .btn {
                width: 80%;
                padding: 0.75rem 1rem;
            }

            /* --- 3. DataTables Controls --- */
            /* The Bootstrap 5 integration for DataTables handles 
               the search/pagination layout, so no extra CSS is needed. */
        }
    </style>
</head>

<body class="bg-light">

    <div class="container my-4 my-md-5" style="max-width: 1100px;">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Welcome!</h1>
                <p class="text-muted mb-0">This is the file list page.</p>
            </div>
            <a href="logout.php" class="btn btn-danger mt-2 mt-md-0">Logout</a>
        </div>

        <?php
        // Call the timer function here. It is now a Bootstrap alert.
        display_countdown_timer();
        ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Uploaded Files</h2>

                <button id="reloadTableBtn" class="btn btn-outline-primary btn-sm">
                    Reload
                </button>
            </div>

            <div class="card-body p-3 p-md-4">
                <div class="table-responsive">
                    <table id="filesTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Size</th>
                                <th>Date Uploaded</th>
                                <th>Share Link</th>
                                <th>Download</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 pt-4 border-top">
            <a href="index.php" class="text-decoration-none fw-medium">Back to Uploader</a>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

    <script src="files.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>