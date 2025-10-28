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
                    // Create a share button with the file path in a data-attribute
                    return `<button class="share-btn" data-link="${data}">Copy Link</button>`;
                },
                "orderable": false // Don't allow sorting on this column
            }
        ]
    });

    // 2. Add click listener for the 'Copy Link' button
    $('#filesTable tbody').on('click', '.share-btn', function () {
        var link = $(this).data('link');
        var button = $(this);

        // Copy the link to the clipboard
        navigator.clipboard.writeText(link).then(function () {
            // Success!
            button.text('Copied!');
            button.css('background-color', '#28a745'); // Green
            
            // Reset button text after 2 seconds
            setTimeout(function () {
                button.text('Copy Link');
                button.css('background-color', '#007bff'); // Original blue
            }, 2000);

        }, function (err) {
            // Failed
            button.text('Failed!');
            button.css('background-color', '#dc3545'); // Red
        });
    });
});