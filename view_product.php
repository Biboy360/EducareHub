<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has stock_encoder role or higher (inventory management)
$allowed_roles = ['stock_encoder', 'admin', 'super_admin'];
if (!in_array($_SESSION['admins']['role'], $allowed_roles)) {
    $_SESSION['error_message'] = "Access denied. Only Stock Encoders and above can view products.";
    header("Location: admin_dashboard.php");
    exit();
}

include('insert_product.php'); ?>
<?php $old = $_POST ?? []; ?>

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
            font-size: 0.8rem;
            vertical-align: top;
            text-align: left;
        }
        .table th {
            font-weight: 600;
        }
        
        /* Column width definitions */
        .id-col { width: 50px; min-width: 50px; }
        .img-col { width: 60px; min-width: 60px; }
        .product-name-col { width: 120px; min-width: 100px; }
        .description-col { width: 150px; min-width: 120px; }
        .supplier-col { width: 100px; min-width: 80px; }
        .coins-col { width: 60px; min-width: 60px; }
        .sku-col { width: 80px; min-width: 70px; }
        .category-col { width: 80px; min-width: 70px; }
        .price-col { width: 60px; min-width: 60px; }
        .created-by-col { width: 80px; min-width: 70px; }
        .date-col { width: 90px; min-width: 80px; }
        .action-col { width: 50px; min-width: 50px; text-align: center; }
        
        /* Product image styling */
        .img-col img {
            max-width: 50px;
            max-height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        /* Action button styling */
        .btn-edit, .btn-delete {
            padding: 0.15rem 0.3rem;
            font-size: 0.6rem;
            border-radius: 0.2rem;
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

        .expandable-text {
            position: relative;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .expandable-text.truncated::after {
            content: '...';
            position: absolute;
            right: 0;
            top: 0;
            background: linear-gradient(to right, rgba(255, 255, 255, 0), white 50%);
            padding-left: 20px;
        }

        /* New modal styling from view_orders */
        #productInfoModal .modal-body {
            padding: 0;
        }

        .product-details-separator {
            padding: 1.5rem 2rem;
            background: linear-gradient(to right, #eef5f9, #f8fafc);
            border-left: 6px solid var(--theme-color);
        }

        .product-details-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .product-details-info {
            flex-grow: 1;
        }

        .product-details-title {
            font-weight: 700;
            font-size: 1.5rem;
            color: #1a3b5a;
            margin-bottom: 0.75rem;
        }

        .product-summary-list {
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
    </style>
</head>
<body class="body">
    <div class="dashboard_main_container">
        <?php include_once('partials/app_topNav.php'); ?>
        <?php include_once('partials/app_horizontal_nav.php'); ?>

        <div class="dashboard_content">
            <div class="dashboard_content_main">
                <div class="row">
                    <div class="column-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h1 class="section_header" style="margin-bottom: 0;"><i class="fa fa-list"></i> List of Products</h1>
                            <a href="add_product.php" class="btn btn-primary-themed"><i class="fa fa-plus"></i> Add Product</a>
                        </div>
                        <div class="section_content">
                            <div class="users">
                                <div class="table-container">
                                    <table id="productsTable" class="display responsive table-themed" style="width:100%; table-layout: fixed;">
                                    <thead>
                                        <tr>
                                                <th style="width: 6%;">ID</th>
                                                <th style="width: 8%;">Image</th>
                                            <th style="width: 15%;">Product Name</th>
                                            <th style="width: 20%;">Description</th>
                                            <th style="width: 12%;">Supplier</th>
                                            <th style="width: 8%;">SKU</th>
                                            <th style="width: 8%;">Category</th>
                                            <th style="width: 8%;">Required Coins</th>
                                            <th style="width: 8%;">Price</th>
                                            <th style="width: 7%;">More Info</th>
                                            <th style="width: 7%;">Edit</th>
                                            <th style="width: 7%;">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $db = mysqli_connect('localhost', 'root', '', 'educarehub');
                                        if (!$db) {
                                            die("Connection failed: " . mysqli_connect_error());
                                        }

                                        // Modified: Added 'price' to the SELECT query
                                        $query = "SELECT p.*, a.username as created_by_name FROM products p LEFT JOIN admins a ON p.created_by = a.admin_id ORDER BY p.created_at DESC";
                                        $results = mysqli_query($db, $query);

                                        while ($row = mysqli_fetch_array($results)) { ?>
                                            <tr>
                                                <td class="id-col"><?php echo htmlspecialchars($row['product_id']); ?></td>
                                                <td class="img-col"><img src="<?php echo htmlspecialchars($row['img']); ?>" alt="Product Image"></td>
                                                <td class="product-name-col expandable-text"><?php echo htmlspecialchars($row['product_name']); ?></td>
                                                <td class="description-col expandable-text"><?php echo htmlspecialchars($row['description']); ?></td>
                                                <td class="supplier-col expandable-text"><?php echo htmlspecialchars($row['supplier'] ?? 'N/A'); ?></td>
                                                <td class="sku-col"><?php echo htmlspecialchars($row['sku']); ?></td>
                                                <td class="category-col"><?php echo htmlspecialchars($row['category']); ?></td>
                                                <td class="coins-col">🪙<?php echo htmlspecialchars($row['points'] ?? 0); ?></td>
                                                <td class="price-col">₱<?php echo htmlspecialchars(number_format($row['price'], 2) ?? '0.00'); ?></td>
                                                <td class="action-col">
                                                    <button class="btn btn-sm btn-outline-info toggle-info-btn" 
                                                            data-product-name="<?php echo htmlspecialchars($row['product_name']); ?>"
                                                            data-created-by="<?php echo htmlspecialchars($row['created_by_name'] ?? 'N/A'); ?>"
                                                            data-created-at="<?php echo htmlspecialchars($row['created_at']); ?>"
                                                            data-updated-at="<?php echo htmlspecialchars($row['updated_at']); ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </td>
                                                <td class="action-col"><a href="update_product.php?id=<?php echo $row['product_id']; ?>" class="btn-edit"><i class="fa fa-pencil"></i> Edit</a></td>
                                                <td class="action-col"><a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fa fa-trash"></i> Delete</a></td>
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
    <div class="modal fade" id="productInfoModal" tabindex="-1" aria-labelledby="productInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-themed">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productInfoModalLabel">Product Details</h5>
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
        // First, initialize the DataTable
        var table = $('#productsTable').DataTable({
            "order": [[ 0, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": [1, 9, 10, 11] }, // Disable sorting on Image and action columns
                { "className": "text-center", "targets": [1, 9, 10, 11] }
            ],
            // Re-initialize expandable text on each draw
            "drawCallback": function( settings ) {
                        reinitializeExpandableText();
                    }
        });

        // Now, set up the event listener for the modal
        $('#productsTable tbody').on('click', '.toggle-info-btn', function () {
            var button = $(this);
            var productName = button.data('product-name');
            var createdBy = button.data('created-by');
            var createdAtRaw = button.data('created-at');
            var updatedAtRaw = button.data('updated-at');

            // Format dates to M/D/YYYY
            var createdDate = new Date(createdAtRaw).toLocaleDateString('en-US');
            var updatedDate = new Date(updatedAtRaw).toLocaleDateString('en-US');

            var modalTitle = $('#productInfoModal .modal-title');
            modalTitle.text('Details for ' + productName);

            var modalBody = $('#productInfoModal .modal-body');

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
            <div class="product-details-separator">
                <div class="product-details-header">
                    <div class="product-details-icon" style="${iconStyles}">
                        <i class="fa fa-box-open"></i>
                    </div>
                    <div class="product-details-info">
                        <div class="product-details-title">${productName}</div>
                        <div class="product-summary-list">
                            <span class="summary-item"><i class="fa fa-user"></i> <strong>Created by:</strong>&nbsp;${createdBy}</span>
                            <span class="summary-item"><i class="fa fa-calendar-plus"></i> <strong>Created:</strong>&nbsp;${createdDate}</span>
                            <span class="summary-item"><i class="fa fa-calendar-check"></i> <strong>Updated:</strong>&nbsp;${updatedDate}</span>
                        </div>
                    </div>
                </div>
            </div>
            `;
            
            modalBody.html(modalContent);
            
            var myModal = new bootstrap.Modal(document.getElementById('productInfoModal'));
            myModal.show();
        });
    });

    // Function to re-initialize expandable text
    function reinitializeExpandableText() {
        $('.expandable-text').each(function() {
            if (this.offsetWidth < this.scrollWidth) {
                $(this).addClass('truncated');
            } else {
                $(this).removeClass('truncated');
                }
            });
        }

    $(document).on('click', '.expandable-text.truncated', function() {
        $(this).toggleClass('expanded');
    });
    </script>
</body>
</html>