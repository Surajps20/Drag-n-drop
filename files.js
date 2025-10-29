$(document).ready(function () {
    // 1. Initialize the DataTable
    var table = $('#filesTable').DataTable({
        "ajax": "list_files.php", 
        "columns": [
            { "data": "name" },     // index 0
            { "data": "size" },     // index 1
            { "data": "date" },     // index 2
            {
                "data": "path",
                "render": function (data, type, row) {
                    return `<button class="btn btn-primary btn-sm share-btn" data-link="${data}">Copy Link</button>`;
                },
                "orderable": false
            },
            {
                "data": "path",
                "render": function (data, type, row) {
                    return `<a href="${data}" class="btn btn-success btn-sm download-btn" download="${row.name}">Download</a>`;
                },
                "orderable": false
            },
            {
                "data": "path",
                "render": function (data, type, row) {
                    return `<button class="btn btn-danger btn-sm delete-btn" data-path="${data}">Delete</button>`;
                },
                "orderable": false
            }
        ],
        "order": [[ 2, "desc" ]] // Sort by date in descending order
    });

    // --- 4. Click listener for the new 'Reload' button ---
    $('#reloadTableBtn').on('click', function () {
        // Use the 'table' variable you already defined
        // to reload the table data from 'list_files.php'
        table.ajax.reload();
    });

    // --- 2. "Copy Link" listener (UPDATED with SweetAlert2 toast) ---
    $('#filesTable tbody').on('click', '.share-btn', function () {
        var link = $(this).data('link');
        
        navigator.clipboard.writeText(link).then(function () {
            // Success: Show a small toast notification
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Link copied!',
                showConfirmButton: false,
                timer: 2000, // Disappears after 2 seconds
                timerProgressBar: true
            });
        }, function (err) {
            // Failed: Show an error toast
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Failed to copy',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });
    });

    // --- 3. "Delete" listener (UPDATED with SweetAlert2) ---
    $('#filesTable tbody').on('click', '.delete-btn', function () {
        var button = $(this);
        var filePath = button.data('path');

        // Step 1: Replace window.confirm() with a SweetAlert modal
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            // Step 2: Check if the user confirmed
            if (result.isConfirmed) {
                // User clicked "Yes", proceed with AJAX delete
                $.ajax({
                    url: 'delete_file.php',
                    type: 'POST',
                    data: { file_path: filePath },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Step 3: Replace alert() with a success modal
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            // Reload the table to show the file is gone
                            table.ajax.reload();
                        } else {
                            // Step 4: Replace alert() with an error modal
                            Swal.fire(
                                'Error!',
                                response.message, // Show server's error message
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        // Step 4: Handle server/network errors
                        Swal.fire(
                            'Error!',
                            'An error occurred while contacting the server.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});