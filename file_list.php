<?php
// Start the session to check if it exists
session_start();

// Check if the user_id session variable is NOT set
if (!isset($_SESSION['user_id'])) {
    
    // If not set, they are not logged in. Redirect to login page.
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files</title>
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
    /* --- Global Styles --- */
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background-color: #f4f7f6;
        margin: 0;
        padding: 2rem;
        color: #333;
    }

    /* --- Page Layout --- */
    .page-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    /* --- Header (Welcome Message & Logout) --- */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .header .welcome-message h1 {
        margin: 0;
        font-size: 1.75rem;
        color: #111;
    }

    .header .welcome-message p {
        margin: 0.25rem 0 0;
        color: #555;
    }

    .logout-button {
        background-color: #d93025;
        color: white;
        text-decoration: none;
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.2s;
    }

    .logout-button:hover {
        background-color: #b0261e;
    }

    /* --- Main File List Card --- */
    .file-container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        padding: 2rem 2.5rem;
    }

    .file-container h2 {
        margin-top: 0;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
    }

    /* --- Table Controls (Search/Show) --- */
    .table-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        color: #444;
    }

    .table-controls select,
    .table-controls input[type="search"] {
        padding: 0.5rem 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        margin: 0 0.5rem;
    }

    .table-controls input[type="search"] {
        min-width: 250px;
    }

    /* --- The File Table --- */
    .files-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
    }

    .files-table th,
    .files-table td {
        padding: 1rem 0.75rem;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .files-table th {
        background-color: #f9fafa;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #666;
    }

    /* Give the first and last cells a bit of extra padding */
    .files-table th:first-child,
    .files-table td:first-child {
        padding-left: 1.25rem;
    }
    .files-table th:last-child,
    .files-table td:last-child {
        padding-right: 1.25rem;
        text-align: right;
    }
    
    .files-table td {
        font-size: 0.95rem;
    }

    /* --- Copy Button --- */
    .copy-button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .copy-button:hover {
        background-color: #0056b3;
    }

    /* --- Table Footer (Info & Pagination) --- */
    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
        color: #555;
    }

    .pagination a {
        text-decoration: none;
        color: #007bff;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        margin-left: 0.25rem;
    }

    .pagination a.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination a.disabled {
        color: #aaa;
        border-color: #eee;
        pointer-events: none;
    }

    /* --- "Back to Uploader" Link --- */
    .footer-link {
        text-align: center;
        margin-top: 2rem;
        border-top: 1px solid #eee;
        padding-top: 1.5rem;
    }

    .footer-link a {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }

    .footer-link a:hover {
        text-decoration: underline;
    }

/* --- Countdown Timer --- */
        .timer-container {
            background-color: #fff8e1; /* A light, non-intrusive yellow */
            border: 1px solid #ffe5b4;
            color: #856404; /* Dark text for contrast */
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 500;
        }

        #countdown-timer {
            font-weight: 700;
            color: #d93025; /* Red for urgency */
            margin-left: 0.5rem;
        }

        /* --- Action Buttons in Table --- */
/* Base style for all buttons */
.share-btn, .download-btn, .delete-btn {
    color: white;
    border: none;
    padding: 0.6rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
    text-decoration: none; /* For the <a> tag */
    display: inline-block; /* For the <a> tag */
    font-size: 0.85rem;
    text-align: center;
}

/* Style for the 'Copy Link' button */
.share-btn {
    background-color: #007bff; /* Blue */
}
.share-btn:hover {
    background-color: #0056b3;
}

/* Style for the new 'Download' button */
.download-btn {
    background-color: #28a745; /* Green */
}
.download-btn:hover {
    background-color: #218838;
}

/* Style for the new 'Delete' button */
.delete-btn {
    background-color: #dc3545; /* Red */
}
.delete-btn:hover {
    background-color: #c82333;
}

/* --- Responsive Styles for Mobile Phones --- */
@media (max-width: 768px) {

    /* --- 1. General Layout --- */
    body {
        padding: 0.5rem; /* Reduce body padding */
    }
    .page-container {
        padding: 0;
    }
    .file-container {
        padding: 1rem 0.5rem; /* Reduce card padding */
    }

    /* --- 2. Header --- */
    .header {
        flex-direction: column; /* Stack logo and logout button */
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    /* --- 3. DataTables Controls --- */
    /* Make 'Show Entries' and 'Search' full-width */
    .dataTables_length,
    .dataTables_filter {
        float: none; /* Disable default DataTables float */
        width: 100%;
        text-align: left;
        margin-bottom: 0.5rem;
    }
    .dataTables_filter input[type="search"] {
        width: 70%;
        display: inline-block;
    }

    /* --- 4. Responsive "Stacked Card" Table --- */
    table#filesTable thead {
        /* Hide the original table headers */
        display: none;
    }

    table#filesTable tbody,
    table#filesTable tr,
    table#filesTable td {
        /* Make everything a full-width block */
        display: block;
        width: 100%;
        box-sizing: border-box;
    }

    table#filesTable tr {
        /* This is our new "card" */
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        padding: 0.5rem;
    }

    table#filesTable td {
        border: none;
        border-bottom: 1px solid #f0f0f0;
        padding: 0.75rem;
        padding-left: 45%; /* Make room for the label */
        position: relative;
        text-align: right; /* Align cell content to the right */
        height: auto;
    }

    table#filesTable td:last-child {
        border-bottom: none; /* No border on the last cell */
    }

    /* Create the new "labels" */
    table#filesTable td::before {
        content: attr(data-label); /* Gets label text from JS */
        position: absolute;
        left: 0.75rem;
        width: 40%;
        text-align: left;
        font-weight: 600;
        color: #333;
    }

    /* --- 5. Custom Labels & Alignment --- */

    /* File Name: Make this the "title" of the card */
    table#filesTable td:nth-of-type(1) {
        text-align: left;
        font-size: 1.1rem;
        font-weight: 600;
        padding-left: 0.75rem;
        word-break: break-all; /* Break long file names */
    }
    table#filesTable td:nth-of-type(1)::before {
        display: none; /* No label for File Name */
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
        display: none; /* No labels for buttons */
    }
    
    /* Make buttons bigger and easier to tap */
    .share-btn, .download-btn, .delete-btn {
        width: 80%;
        padding: 0.75rem 1rem;
    }

    /* --- 6. Table Footer --- */
    /* Center the 'Showing X' and 'Pagination' */
    .dataTables_info,
    .dataTables_paginate {
        float: none;
        width: 100%;
        text-align: center;
    }
    .dataTables_info {
        margin-bottom: 1rem;
    }
}
/* This replaces your old .copy-button style */
/* You can remove .copy-button and .copy-button:hover */
</style>
</head>

<body>
    <div class="page-container">

        <div class="header">
            <div class="welcome-message">
                <h1>Welcome! You are logged in.</h1>
                <p>This is the file list page.</p>
            </div>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
        

    <div class="files-container">
        <h2>Uploaded Files</h2>
        <table id="filesTable" class="display" style="width:100%">
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

        <p style="text-align: center; margin-top: 20px;">
            <a href="index.php">Back to Uploader</a>
        </p>
    </div>
    


    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="files.js"></script>

</body>

</html>