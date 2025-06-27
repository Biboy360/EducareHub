<?php $old = $_POST ?? []; ?>
<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has purchasing_officer role or higher (purchase management) OR is a viewer
$allowed_roles = ['purchasing_officer', 'admin', 'super_admin', 'viewer'];
if (!in_array($_SESSION['admins']['role'], $allowed_roles)) {
    $_SESSION['error_message'] = "Access denied. Only Purchasing Officers and above, or Viewers can view suppliers.";
    header("Location: admin_dashboard.php");
    exit();
}

$is_viewer = ($_SESSION['admins']['role'] ?? '') === 'viewer';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>admin_dashboard</title>

    <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css?v=4"> <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css"/>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        .table {
            width: 100%;
        }
        .table th, .table td {
            padding: 8px;
            line-height: 1.5;
            white-space: normal;
            word-wrap: break-word;
            font-size: 0.8rem; /* Consistent font size */
            vertical-align: top;
            text-align: left;
        }
        .table th {
            font-weight: 600;
        }
        
        .action-col { text-align: center; }
        
        /* Action button styling */
        .btn-edit, .btn-delete {
            padding: 0.2rem 0.4rem;
            font-size: 0.65rem;
            border-radius: 0.25rem;
            text-decoration: none;
            display: inline-block;
            margin: 0 1px;
            white-space: nowrap;
        }
        
        .btn-edit {
            background-color: #28a745;
            color: white;
            border: 1px solid #28a745;
        }
        
        .btn-edit:hover {
            background-color: #218838;
            border-color: #1e7e34;
            color: white;
            text-decoration: none;
        }
        
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: 1px solid #dc3545;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: white;
            text-decoration: none;
        }
        
        /* Custom DataTable styling for better alignment */
        .dataTables_wrapper .dt-buttons {
            float: left;
            margin-right: 15px;
        }
        
        .dataTables_wrapper .dt-button {
            margin-right: 5px;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            transition: all 0.15s ease-in-out;
        }
        
        .dataTables_wrapper .dt-button:hover {
            background-color: #5a6268;
            border-color: #545b62;
            color: white;
        }
        
        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-right: 15px;
        }
        
        .dataTables_wrapper .dataTables_length select {
            margin: 0 5px;
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            background-color: white;
        }
        
        .dataTables_wrapper .dataTables_filter {
            float: right;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px;
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            background-color: white;
        }
        
        .dataTables_wrapper .dataTables_info {
            clear: both;
            padding-top: 10px;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            float: right;
            margin-top: 10px;
        }
        
        .dataTables_wrapper .paginate_button {
            padding: 0.375rem 0.75rem;
            margin-left: 2px;
            border: 1px solid #dee2e6;
            background-color: white;
            color: #007bff;
            border-radius: 0.375rem;
        }
        
        .dataTables_wrapper .paginate_button.current {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }
        
        .dataTables_wrapper .paginate_button:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
            color: #0056b3;
        }
        
        /* Ensure proper spacing and alignment */
        .dataTables_wrapper::after {
            content: "";
            display: table;
            clear: both;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dataTables_wrapper .dt-buttons,
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none;
                margin-bottom: 10px;
                text-align: center;
            }
        }
        
        /* Table container to prevent horizontal scrolling */
        .table-container {
            overflow-x: auto;
            max-width: 100%;
        }

        /* Modal styling copied from view_product */
        #supplierInfoModal .modal-body {
            padding: 0;
        }

        .details-separator {
            padding: 1.5rem 2rem;
            background: linear-gradient(to right, #eef5f9, #f8fafc);
            border-left: 6px solid var(--theme-color);
        }

        .details-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .details-icon {
            font-size: 2rem;
            color: #ffffff;
            background: linear-gradient(45deg, var(--theme-color), #17a2b8);
            padding: 1rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
        }

        .details-info {
            flex-grow: 1;
        }

        .details-title {
            font-weight: 700;
            font-size: 1.5rem;
            color: #1a3b5a;
            margin-bottom: 0.75rem;
        }

        .summary-list {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .summary-item {
            font-size: 0.9rem;
            color: #34495e;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Expandable text styling */
        .expandable-text {
            position: relative;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            max-width: 150px; /* Adjust as needed */
        }
        .expandable-text.expanded {
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
            cursor: default;
        }
    </style>
</head>
<body class="body">
  <div class="dashboard_main_container">
    <?php include('partials/app_topNav.php'); ?>
    <?php include('partials/app_horizontal_nav.php'); ?>

    <div class="dashboard_content">
      <div class="dashboard_content_main">
        <div class="row">
          <div class="column-12">
            <div class="d-flex justify-content-between align-items-center">
              <h1 class="section_header"><i class="fa fa-list"></i> List of Suppliers</h1>
              <?php if (!$is_viewer): ?>
              <a href="add_suppliers.php" class="btn btn-primary-themed"><i class="fa fa-plus"></i> Add Supplier</a>
              <?php endif; ?>
            </div>
            <?php if ($is_viewer): ?>
            <div class="alert alert-info mt-3" role="alert">
                <i class="fa fa-eye"></i> <strong>Viewer Mode:</strong> You are viewing this page in read-only mode. You cannot add, edit, or delete suppliers.
            </div>
            <?php endif; ?>
            <div class="section_content">
              <div class="users">
                <div class="table-container">
                  <table id="suppliersTable" class="display responsive table-themed" style="width:100%; table-layout: fixed;">
                  <thead>
                    <tr>
                        <th style="width: 8%;">ID</th>
                        <th style="width: 20%;">Supplier Name</th>
                        <th style="width: 15%;">Contact Person</th>
                        <th style="width: 20%;">Email</th>
                        <th style="width: 12%;">Phone</th>
                        <th style="width: 15%;">Address</th>
                        <th style="width: 10%;">More Info</th>
                        <?php if (!$is_viewer): ?>
                        <th style="width: 7%;">Edit</th>
                        <th style="width: 7%;">Delete</th>
                        <?php endif; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $db = mysqli_connect('localhost', 'root', '', 'educarehub');
                    if (!$db) {
                      die("Connection failed: " . mysqli_connect_error());
                    }

                    $query = "SELECT s.*, a.username as created_by_name FROM supplier s LEFT JOIN admins a ON s.created_by = a.admin_id ORDER BY s.created_at DESC";
                    $results = mysqli_query($db, $query);

                    while ($row = mysqli_fetch_array($results)) { ?>
                        <tr>
                            <td><?php echo $row['supplier_id'] ?></td>
                            <td class="expandable-text"><?php echo $row['supplier_name'] ?></td>
                            <td class="expandable-text"><?php echo $row['contact_no'] ?></td>
                            <td class="expandable-text"><?php echo $row['email'] ?></td>
                            <td><?php echo $row['contact_no'] ?></td>
                            <td class="expandable-text"><?php echo $row['supplier_location'] ?></td>
                            <td class="action-col">
                                <button class="btn btn-sm btn-outline-info toggle-info-btn" 
                                        data-supplier-name="<?php echo htmlspecialchars($row['supplier_name']); ?>"
                                        data-created-by="<?php echo htmlspecialchars($row['created_by_name'] ?? 'N/A'); ?>"
                                        data-created-at="<?php echo htmlspecialchars($row['created_at']); ?>"
                                        data-updated-at="<?php echo htmlspecialchars($row['updated_at']); ?>">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                            <?php if (!$is_viewer): ?>
                            <td class="action-col"><a href="update_suppliers.php?id=<?php echo $row['supplier_id']; ?>" class="btn-edit"><i class="fa fa-pencil"></i> Edit</a></td>
                            <td class="action-col"><a href="delete_suppliers.php?id=<?php echo $row['supplier_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this supplier?');"><i class="fa fa-trash"></i> Delete</a></td>
                            <?php endif; ?>
                        </tr>
                    <?php } ?>
                  </tbody>
                </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- More Info Modal -->
    <div class="modal fade" id="supplierInfoModal" tabindex="-1" aria-labelledby="supplierInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierInfoModalLabel">Supplier Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be injected by JavaScript -->
                </div>
            </div>
        </div>
    </div>

  <script src="js/script.js"></script>
    <script>
    $(document).ready(function() {
        $('#suppliersTable').DataTable({
            responsive: true,
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"lB><"text-end"f>>rtip',
            buttons: [
                { extend: 'copy', text: '<i class="fa fa-copy"></i> Copy', className: 'btn-secondary-themed' },
                { extend: 'csv', text: '<i class="fa fa-file-csv"></i> CSV', className: 'btn-secondary-themed' },
                { extend: 'excel', text: '<i class="fa fa-file-excel"></i> Excel', className: 'btn-secondary-themed' },
                { extend: 'pdf', text: '<i class="fa fa-file-pdf"></i> PDF', className: 'btn-secondary-themed' },
                { extend: 'print', text: '<i class="fa fa-print"></i> Print', className: 'btn-secondary-themed' }
            ],
            order: [[0, 'desc']], // Default sort by the first column (ID) descending
            columnDefs: [
                <?php if (!$is_viewer): ?>
                { "orderable": false, "targets": [6, 7, 8] }
                <?php else: ?>
                { "orderable": false, "targets": [6] }
                <?php endif; ?>
            ]
        });

        // Modal script copied from view_product
        $('#suppliersTable tbody').on('click', '.toggle-info-btn', function () {
            var button = $(this);
            var supplierName = button.data('supplier-name');
            var createdBy = button.data('created-by');
            var createdAtRaw = button.data('created-at');
            var updatedAtRaw = button.data('updated-at');

            var createdDate = new Date(createdAtRaw).toLocaleDateString('en-US');
            var updatedDate = new Date(updatedAtRaw).toLocaleDateString('en-US');

            var modalTitle = $('#supplierInfoModal .modal-title');
            modalTitle.text('Details for ' + supplierName);

            var modalBody = $('#supplierInfoModal .modal-body');

            const iconStyles = `
                font-size: 2rem;
                color: #ffffff;
                background: linear-gradient(45deg, var(--theme-color), #17a2b8);
                padding: 1rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 70px;
                height: 70px;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                border: 2px solid rgba(255, 255, 255, 0.3);
                flex-shrink: 0;
            `;

            var modalContent = `
            <div class="details-separator">
                <div class="details-header">
                    <div class="details-icon" style="${iconStyles}">
                        <i class="fa fa-truck"></i>
                    </div>
                    <div class="details-info">
                        <div class="details-title">${supplierName}</div>
                        <div class="summary-list">
                            <span class="summary-item"><i class="fa fa-user"></i> <strong>Created by:</strong>&nbsp;${createdBy}</span>
                            <span class="summary-item"><i class="fa fa-calendar-plus"></i> <strong>Created:</strong>&nbsp;${createdDate}</span>
                            <span class="summary-item"><i class="fa fa-calendar-check"></i> <strong>Updated:</strong>&nbsp;${updatedDate}</span>
                        </div>
                    </div>
                </div>
            </div>
            `;
            
            modalBody.html(modalContent);
            
            var myModal = new bootstrap.Modal(document.getElementById('supplierInfoModal'));
            myModal.show();
        });

        // Initialize expandable text
        function reinitializeExpandableText() {
            $('.expandable-text').each(function() {
                var $this = $(this);
                $this.removeClass('expanded'); // Reset state
                if (this.offsetWidth < this.scrollWidth) {
                    $this.addClass('truncated');
                } else {
                    $this.removeClass('truncated');
                }
            });
        }

        // Handle click to expand
        $('#suppliersTable').on('click', '.expandable-text.truncated', function() {
            $(this).removeClass('truncated').addClass('expanded');
        });

        // Initial call
        reinitializeExpandableText();
        
        // Re-run on DataTables draw event
        $('#suppliersTable').on('draw.dt', function() {
            reinitializeExpandableText();
        });
    });
  </script>
</body>
</html>
 