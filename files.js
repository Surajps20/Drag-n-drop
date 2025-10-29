$(document).ready(function () {
    // 1. Initialize the DataTable
    var table = $('#filesTable').DataTable({
        "ajax": "list_files.php", // PHP script to get file list
        "columns": [
            { "data": "name" },
            { "data": "size" },
            { "data": "date" },
            {
                "data": "path",
                "render": function (data, type, row) {
                    // Create a share button
                    return `<button class="share-btn" data-link="${data}">Copy Link</button>`;
                },
                "orderable": false
            },
            // --- ADDED DOWNLOAD BUTTON ---
            {
                "data": "path",
                "render": function (data, type, row) {
                    // Create a download link
                    // 'download' attribute uses row.name for a clean filename
                    return `<a href="${data}" class="download-btn" download="${row.name}">Download</a>`;
                },
                "orderable": false
            },
            // --- ADDED DELETE BUTTON ---
            {
                "data": "path",
                "render": function (data, type, row) {
                    // Create a delete button with the file path
                    return `<button class="delete-btn" data-path="${data}">Delete</button>`;
                },
                "orderable": false
            }
        ]
    });

    // 2. Click listener for the 'Copy Link' button (Existing)
    $('#filesTable tbody').on('click', '.share-btn', function () {
        var link = $(this).data('link');
        var button = $(this);

        navigator.clipboard.writeText(link).then(function () {
            button.text('Copied!');
            button.css('background-color', '#28a745'); // Green
            
            setTimeout(function () {
                button.text('Copy Link');
                button.css('background-color', '#007bff'); // Original blue
            }, 2000);
        }, function (err) {
            button.text('Failed!');
            button.css('background-color', '#dc3545'); // Red
        });
    });

    // --- 3. ADDED: Click listener for the 'Delete' button ---
    $('#filesTable tbody').on('click', '.delete-btn', function () {
        var button = $(this);
        var filePath = button.data('path');

        // Confirm with the user before deleting
        if (!confirm('Are you sure you want to delete this file? This cannot be undone.')) {
            return; // Stop if user clicks 'Cancel'
        }

        // Send an AJAX request to the new delete_file.php script
        $.ajax({
            url: 'delete_file.php',
            type: 'POST',
            data: { file_path: filePath },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // File deleted successfully
                    // Reload the table to show the file is gone
                    table.ajax.reload();
                    alert('File deleted successfully.');
                } else {
                    // Show an error message from the server
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle server errors (e.g., 403, 404, 500)
                var errorMsg = 'An error occurred while contacting the server.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });
});