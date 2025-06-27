<?php
session_start(); // Start the session at the very beginning of the file

// Include your database connection file
include('user_connection.php');

// Initialize success/error messages from session and clear them
$message_type = $_SESSION['message_type'] ?? '';
$message = $_SESSION['message'] ?? '';

// Clear session messages so they don't reappear on refresh
unset($_SESSION['message_type']);
unset($_SESSION['message']);

// Retrieve old input and errors from session and clear them
$old_input = $_SESSION['old_admin_input'] ?? [];
unset($_SESSION['old_admin_input']);

$errors = $_SESSION['admin_form_errors'] ?? [];
unset($_SESSION['admin_form_errors']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Admins</title>

    <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="css/style.css?v=4">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.bootstrap5.min.css" />

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.js"></script> 
    
    <script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .highlight-pending { color: orange; font-weight: bold; }
        .highlight-incomplete { color: red; font-weight: bold; }
        .complete { color: green; font-weight: bold; }
        /* Basic styling for error messages */
        .error-message {
            color: #dc3545; /* Bootstrap danger color */
            font-size: 0.875em;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
        }
        /* Style to make input fields red when there's an error */
        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>

</head>

<body class="body">
    <div class="dashboard_main_container">
        <?php include('partials/app_topNav.php'); ?>
        <?php include('partials/app_horizontal_nav.php'); ?>
        <div class="dashboard_content">
            <div class="dashboard_content_main">
                <h1>Comments for Post #<?php echo htmlspecialchars($post_id); ?></h1>
                <div class="row">
                    
                    <div class="column column-7">
                        <h2 class="section_header"><i class="fa fa-users"></i> LIST OF COMMENTS</h2>
                        <div class="section_content">
                            <div class="users">
                                <table id="myTable" class="table table-striped table-bordered display">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>user_id</th>
                                            <th>post_id</th>
                                            <th>comment</th>
                                            <th>created_at</th> 
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Ensure $conn is available from connection.php
                                        if (isset($conn) && $conn) { // <--- Changed from $db to $conn
                                            $query = "SELECT id, user_id, post_id, comment, created_at FROM comments ORDER BY id DESC";
                                            $results = mysqli_query($conn, $query); // Uses $conn

                                            if ($results) {
                                                if (mysqli_num_rows($results) > 0) {
                                                    while ($row = mysqli_fetch_assoc($results)) {
                                                        echo "<tr>";
                                                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['post_id']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['comment']) . "</td>";
                                                        echo "<td>" . date('M d, Y g:i A', strtotime($row['created_at'])) . "</td>";
                                                        echo "<td>";
                                                        // Corrected to use 'id' and a more appropriate filename (edit_comment.php)
                                                        echo "<a href='edit_comment.php?id=" . $row['id'] . "' class='btn btn-info btn-sm me-2'>Edit</a>"; 
                                                        echo "</td>";
                                                        echo "<td>";
                                                        // Corrected to use 'id' and a more appropriate class/filename (delete-comment-btn, delete_comment.php)
                                                        echo "<button type='button' class='btn btn-danger btn-sm delete-comment-btn' data-id='" . htmlspecialchars($row['id']) . "'>Delete</button>";
                                                        echo "</td>";
                                                        
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='7'>No comments found.</td></tr>"; // Updated message
                                                }
                                            } else {
                                                echo "<tr><td colspan='7'>Error fetching data: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>"; // Uses $conn
                                            }
                                            // Close connection after fetching data for display
                                            mysqli_close($conn); // Uses $conn
                                        } else {
                                            echo "<tr><td colspan='7'>Database connection not established.</td></tr>"; 
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    $(document).ready(function () {
        // --- SweetAlert2 for delete confirmation ---
        $('.delete-comment-btn').on('click', function(e) { // <--- Changed class from delete-admin-btn
            e.preventDefault(); 
            const commentId = $(this).data('id'); // <--- Changed variable name to commentId

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this comment? This action cannot be undone!", // Changed text
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_comment.php?id=' + commentId; // <--- Changed to delete_comment.php and commentId
                }
            });
        });

        // Display SweetAlert2 message based on PHP session (after redirect)
        <?php if (!empty($message)): ?>
            Swal.fire({
                icon: '<?php echo htmlspecialchars($message_type); ?>', // 'success', 'error', 'info'
                title: '<?php echo htmlspecialchars($message_type === 'success' ? 'Success!' : ($message_type === 'error' ? 'Error!' : 'Info!')); ?>',
                text: '<?php echo htmlspecialchars($message); ?>',
                showConfirmButton: false, // Auto-close
                timer: 3000 // Auto-close after 3 seconds
            });
        <?php endif; ?>
        
        // Toggle icon for collapsible header
        $('.collapsible-header').on('click', function() {
            $(this).find('.toggle-icon').toggleClass('fa-chevron-down fa-chevron-up');
        });

        // If there are validation errors, ensure the form collapse is open
        <?php if (!empty($errors)): ?>
            $('#insertAdminFormCollapse').collapse('show');
            $('.collapsible-header .toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        <?php endif; ?>

        $('#myTable').DataTable({
            responsive: true,
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"lB><"text-end"f>>rtip',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="fa fa-copy"></i> Copy',
                    className: 'btn btn-sm btn-secondary'
                },
                {
                    extend: 'csv',
                    text: '<i class="fa fa-file-csv"></i> CSV',
                    className: 'btn btn-sm btn-secondary'
                },
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    className: 'btn btn-sm btn-secondary'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-secondary'
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-sm btn-secondary'
                }
            ],
            pageLength: 10
        });
    });
    </script>

    <script src="js/script.js"></script> 
</body>
</html>