<?php $old = $_POST ?? []; ?>
<?php if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has purchasing_officer role or higher (purchase management) OR is a viewer
$allowed_roles = ['purchasing_officer', 'admin', 'super_admin', 'viewer'];
if (!in_array($_SESSION['admins']['role'], $allowed_roles)) {
    $_SESSION['error_message'] = "Access denied. Only Purchasing Officers and above, or Viewers can view purchase orders.";
    header("Location: admin_dashboard.php");
    exit();
}

$role = strtolower($_SESSION['admins']['role'] ?? $_SESSION['admins']['Role'] ?? '');
if (!in_array($role, ['super_admin', 'admin', 'purchasing_officer', 'viewer'])) {
    echo '<div style="display:flex;justify-content:center;align-items:center;height:100vh;background:#f8f9fa;"><div style="background:#fff;padding:32px 48px;border-radius:18px;box-shadow:0 4px 24px rgba(60,72,88,0.13);font-size:1.2em;color:#e74c3c;">Access Denied: You do not have permission to view this page.</div></div>';
    exit;
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
    <link rel="stylesheet" href="css/style.css?v=4">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.bootstrap5.min.css" />

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        .table {
            width: 100%;
        }
        .table th, .table td {
            padding: 8px; /* Provides ample space */
            line-height: 1.5; /* Generous line height for readability */
            white-space: normal;
            word-wrap: break-word;
            font-size: 0.8rem;
            vertical-align: top; /* Aligns content to the top */
            text-align: left; /* Ensures all data is left-aligned */
        }
        .table th {
            font-weight: 600;
        }
        
        /* Column width definitions */
        .order-id-col { width: 50px; min-width: 50px; }
        .supplier-id-col { width: 60px; min-width: 60px; }
        .supplier-name-col { width: 100px; min-width: 80px; }
        .product-col { width: 80px; min-width: 70px; }
        .quantity-col { width: 60px; min-width: 60px; }
        .status-col { width: 80px; min-width: 70px; }
        .batch-col { width: 80px; min-width: 70px; }
        .sku-col { width: 70px; min-width: 60px; }
        .category-col { width: 70px; min-width: 60px; }
        .price-col { width: 60px; min-width: 60px; }
        .created-by-col { width: 80px; min-width: 70px; }
        .date-col { width: 80px; min-width: 70px; }
        .action-col { width: 50px; min-width: 50px; text-align: center; }
        
        /* Make text flexible and responsive */
        .table td {
            font-size: clamp(0.55rem, 1.2vw, 0.7rem);
            line-height: 1.1;
        }
        
        .table th {
            font-size: clamp(0.6rem, 1.2vw, 0.75rem);
            font-weight: 600;
        }
        
        /* Status styling */
        .highlight-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.55rem;
        }
        
        .highlight-incomplete {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.55rem;
        }
        
        .complete {
            background-color: #28a745; /* Green */
            color: #fff;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.6rem;
            font-weight: 600;
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
        
        /* Ensure table fits within container */
        #ordersTable {
            width: 100% !important;
            table-layout: fixed;
        }
        
        /* Additional fixes to prevent stretching */
        .dataTables_scrollHead {
            overflow: visible !important;
        }
        
        .dataTables_scrollBody {
            overflow-x: auto;
        }
        
        /* Ensure proper cell alignment */
        .table td, .table th {
            box-sizing: border-box;
        }
        
        /* Fix for potential DataTable rendering issues */
        .dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Batch Separator Styling */
        tr.dtrg-group {
            background-color: #F3F4F6 !important; /* A slightly darker, more modern grey */
        }

        tr.dtrg-group td {
            padding: 0 !important;
        }

        .batch-separator {
            padding: 1rem 1.5rem;
            background: linear-gradient(to right, #eef5f9, #f8fafc); /* Subtle gradient */
            border-left: 6px solid #087ea4; /* A deeper, more vibrant teal */
            margin: 0;
            border-radius: 0;
            box-shadow: inset 0 -1px 0 #e0e6ed; /* Inner shadow for depth */
        }

        .batch-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .batch-icon {
            font-size: 1.8rem;
            color: #087ea4;
            background-color: #ffffff;
            padding: 0.8rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }

        .batch-info {
            flex-grow: 1;
        }

        .batch-title {
            font-weight: 700;
            font-size: 1.4rem;
            color: #1a3b5a; /* Dark blue for contrast */
            margin-bottom: 0.5rem;
        }

        .batch-details {
            display: block; /* Stack product and summary sections */
        }

        .product-list {
            display: flex;
            flex-direction: column;
            align-items: stretch; /* Stretch items to fill width */
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .product-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background: linear-gradient(to right, #fdfdff, #f8f9fa);
            padding: 0.8rem 1.2rem;
            gap: 0.5rem;
            border-width: 1px;
        }

        .product-item-main {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            color: #34495e;
        }

        .product-item-subdetails {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding-left: 2.2rem;
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

        .subdetail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: #5a7b9c;
            background-color: #e9ecef;
            padding: 0.2rem 0.6rem;
            border-radius: 0.8rem;
        }

        .subdetail-item i {
            color: #087ea4;
            font-size: 0.9rem;
        }

        .summary-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            border-top: 1px solid #e0e6ed; /* Separator line */
            padding-top: 0.75rem;
        }

        .batch-item {
            display: flex;
            align-items: center;
            gap: 0.75rem; /* Increased gap for better spacing */
            background-color: #ffffff;
            padding: 0.6rem 1rem; /* More padding */
            border-radius: 0.5rem; /* Modern rounded corners */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
            border: 1px solid #e0e6ed;
            font-weight: 500;
        }

        .batch-item i {
            color: #087ea4; /* Match icon color to border */
            font-size: 1.1rem; /* Slightly larger icons */
        }

        .batch-item strong {
            color: #34495e; /* Slightly darker for emphasis */
            font-weight: 600;
        }

        /* Themed Modal Styling */
        .modal-themed .modal-content {
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .modal-themed .modal-header {
            background-color: #0F9E99;
            color: white;
        }

        .modal-themed .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        .modal-themed .modal-title {
            font-weight: 600;
        }
        
        .modal-themed .modal-body {
            background-color: #f7faff;
        }

        .modal-themed .modal-body .batch-separator {
            margin: 0;
            border-radius: 0;
            box-shadow: none;
            border-left: none;
        }

        /* START: Corrected Centering for Themed Modal */
        .modal-themed .modal-body .batch-header,
        .modal-themed .modal-body .batch-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.5rem;
        }

        .modal-themed .modal-body .batch-icon {
            font-size: 2.5rem;
            color: #0F9E99;
        }

        .modal-themed .modal-body .batch-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }

        .modal-themed .modal-body .product-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
            align-items: center;
            margin-top: 1rem;
        }

        .modal-themed .modal-body .product-item {
            width: 95%;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 1rem;
            text-align: left;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .modal-themed .modal-body .product-item-main {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #0F9E99;
            display: flex;
            align-items: center;
        }

        .modal-themed .modal-body .product-item-main i {
            margin-right: 8px;
        }

        .modal-themed .modal-body .product-item-subdetails {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            padding-top: 0.5rem;
            border-top: 1px solid #f0f0f0;
        }
        
        .modal-themed .modal-body .summary-list {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1.5rem;
            padding: 1rem;
            background-color: #ffffff;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        
        .modal-themed .modal-body .batch-details {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
        
        .subdetail-item, .summary-item {
            font-size: 0.9rem;
        }

        .subdetail-item i, .summary-item i {
            margin-right: 5px;
            color: #0F9E99;
        }
        /* END: Corrected Centering for Themed Modal */
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h1 class="section_header"><i class="fa fa-list"></i> List of Orders</h1>
                            <?php if (!$is_viewer): ?>
                            <a href="add_orders.php" class="btn btn-primary"><i class="fa fa-plus"></i> Create Purchase Order</a>
                            <?php endif; ?>
                        </div>
                        <?php if ($is_viewer): ?>
                        <div class="alert alert-info mt-3" role="alert">
                            <i class="fa fa-eye"></i> <strong>Viewer Mode:</strong> You are viewing this page in read-only mode. You cannot create, edit, or delete orders.
                        </div>
                        <?php endif; ?>
                        <div class="section_content">
                            <div class="users">
                                <div class="table-container">
                                    <table id="ordersTable" class="table table-striped table-bordered table-themed" style="width:100%; table-layout: fixed;">
                                        <thead>
                                            <tr>
                                                <th style="width: 6%;">Order ID</th>
                                                <th style="width: 6%;">Supplier ID</th>
                                                <th style="width: 12%;">Supplier Name</th>
                                                <th style="width: 12%;">Product</th>
                                                <th style="width: 8%;">Order pcs</th>
                                                <th style="width: 8%;">Received</th>
                                                <th style="width: 8%;">Remaining</th>
                                                <th style="width: 8%;">Status</th>
                                                <th style="width: 8%;">Batch</th>
                                                <th style="width: 7%;">SKU</th>
                                                <th style="width: 7%;">Category</th>
                                                <th style="width: 7%;">Price</th>
                                                <th style="width: 5%;">More Info</th>
                                                <th style="width: 8%;">Created By</th>
                                                <th style="width: 8%;">Created At</th>
                                                <th style="width: 8%;">Updated At</th>
                                                <?php if (!$is_viewer): ?>
                                                <th style="width: 6%;">Edit</th>
                                                <th style="width: 6%;">Delete</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $db = mysqli_connect('localhost', 'root', '', 'educarehub');
                                            if (!$db) {
                                                die("Connection failed: " . mysqli_connect_error());
                                            }

                                            $query = "SELECT ps.p_supplier_id, ps.supplier, s.supplier_name, ps.product, p.product_name, ps.quantity_ordered, ps.quantity_recieved, ps.quantity_remaining, ps.status, ps.batch, ps.sku, ps.category, p.price, COALESCE(a.username, 'Unknown Admin') as created_by_name, 
                                                     COALESCE(ps.created_at, 'N/A') as created_at, 
                                                     COALESCE(ps.updated_at, 'N/A') as updated_at
                                                     FROM products_supplier ps 
                                                     LEFT JOIN supplier s ON ps.supplier = s.supplier_id 
                                                     LEFT JOIN products p ON ps.product = p.product_id
                                                     LEFT JOIN admins a ON ps.created_by = a.admin_id
                                                     ORDER BY ps.batch DESC, ps.p_supplier_id ASC";
                                            $results = mysqli_query($db, $query);

                                            if (!$results) {
                                                die("Query failed: " . mysqli_error($db));
                                            }
                                            
                                            $rendered_batches = [];
                                            if (mysqli_num_rows($results) == 0) {
                                                $colspan = $is_viewer ? 16 : 18;
                                                echo "<tr><td colspan='$colspan' style='text-align: center;'>No orders found</td></tr>";
                                            } else {
                                                while ($row = mysqli_fetch_assoc($results)) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['p_supplier_id']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['supplier']) . "</td>";
                                                    echo "<td class='expandable-text product-name-col'>" . htmlspecialchars($row['supplier_name'] ?? 'N/A') . "</td>";
                                                    echo "<td class='expandable-text product-name-col'>" . htmlspecialchars($row['product_name']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['quantity_ordered']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['quantity_recieved'] ?? 0) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['quantity_remaining'] ?? 0) . "</td>";
                                                    
                                                    // Status styling
                                                    $statusClass = '';
                                                    switch(strtolower($row['status'])) {
                                                        case 'pending':
                                                            $statusClass = 'highlight-pending';
                                                            break;
                                                        case 'incomplete':
                                                            $statusClass = 'highlight-incomplete';
                                                            break;
                                                        case 'completed':
                                                            $statusClass = 'complete';
                                                            break;
                                                        default:
                                                            $statusClass = 'highlight-pending';
                                                    }
                                                    echo "<td><span class='$statusClass'>" . htmlspecialchars($row['status']) . "</span></td>";
                                                    
                                                    echo "<td>" . htmlspecialchars($row['batch']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['sku']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                                    $total_price = $row['price'] * $row['quantity_ordered'];
                                                    echo "<td>₱" . htmlspecialchars(number_format($total_price, 2)) . "</td>";

                                                    if (!in_array($row['batch'], $rendered_batches)) {
                                                        echo '<td><button class="btn btn-sm btn-outline-info toggle-info-btn" data-batch="' . htmlspecialchars($row['batch']) . '"><i class="fa fa-eye"></i></button></td>';
                                                        $rendered_batches[] = $row['batch'];
                                                    } else {
                                                        echo '<td></td>';
                                                    }

                                                    echo "<td class='expandable-text fullname-col'>" . htmlspecialchars($row['created_by_name'] ?? 'N/A') . "</td>";
                                                    echo "<td>" . date('M d, Y g:i A', strtotime($row['created_at'])) . "</td>";
                                                    echo "<td>" . date('M d, Y g:i A', strtotime($row['updated_at'])) . "</td>";
                                                    if (!$is_viewer) {
                                                        echo '<td><a href="update_orders.php?id=' . $row['p_supplier_id'] . '" class="btn-edit"><i class="fa fa-box"></i> Receive</a></td>';
                                                        echo '<td><a href="delete_orders.php?id=' . $row['p_supplier_id'] . '" class="btn-delete" onclick="return confirm(\'Are you sure you want to delete this order?\')"><i class="fa fa-trash-can"></i> Delete</a></td>';
                                                    }
                                                    echo "</tr>";
                                                }
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
    </div>
    <script src="js/script.js"></script>
    <script>
    $(document).ready(function () {
        if (!$.fn.dataTable.isDataTable('#ordersTable')) {
            $('#ordersTable').DataTable({
                responsive: false,
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"Bl><"text-end"f>>rtip',
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
                order: [[14, 'desc']],
                rowGroup: {
                    dataSrc: 8,
                    startRender: function (rows, group) {
                        var rowData = rows.data();
                        
                        var totalRemaining = rowData
                            .pluck(6)
                            .reduce(function (a, b) {
                                return a + Number(b);
                            }, 0);
                        
                        var firstRow = rowData[0];
                        var createdBy = rowData.pluck(13).unique()[0]; 
                        var createdAt = rowData.pluck(14).unique()[0]; 
                        var updatedAt = rowData.pluck(15).unique()[0];

                        var createdDate = (createdAt && createdAt !== 'N/A' && !isNaN(new Date(createdAt))) ? new Date(createdAt).toLocaleDateString() : 'N/A';
                        var updatedDate = (updatedAt && updatedAt !== 'N/A' && !isNaN(new Date(updatedAt))) ? new Date(updatedAt).toLocaleDateString() : 'N/A';
                        
                        var productDetailsHtml = '<div class="product-list">';
                        var processedProducts = {};
                        for (var i = 0; i < rows.count(); i++) {
                            var row = rows.data()[i];
                            var productName = row[3];
                            var sku = row[9];
                            var category = row[10];
                            var quantityRemaining = row[6];
                            var productKey = productName + '-' + sku + '-' + category;

                            if (!processedProducts[productKey]) {
                                productDetailsHtml += '<div class="product-item">' +
                                    '<div class="product-item-main">' +
                                        '<i class="fa fa-tag"></i> ' +
                                        '<strong>' + productName + '</strong>' +
                                    '</div>' +
                                    '<div class="product-item-subdetails">' +
                                        '<span class="subdetail-item"><i class="fa fa-barcode"></i> <strong>SKU:</strong> ' + sku + '</span>' +
                                        '<span class="subdetail-item"><i class="fa fa-sitemap"></i> <strong>Category:</strong> ' + category + '</span>' +
                                        '<span class="subdetail-item"><i class="fa fa-cubes"></i> <strong>Remaining:</strong> ' + quantityRemaining + '</span>' +
                                    '</div>' +
                                '</div>';
                                processedProducts[productKey] = true;
                            }
                        }
                        productDetailsHtml += '</div>';

                        var summaryDetailsHtml = '<div class="summary-list">' +
                           '<span class="batch-item summary-item"><i class="fa fa-boxes"></i> <strong>Total Items Remaining:</strong> ' + totalRemaining + '</span>' +
                           '<span class="batch-item summary-item"><i class="fa fa-user"></i> <strong>Created by:</strong> ' + createdBy + '</span>' +
                           '<span class="batch-item summary-item"><i class="fa fa-calendar-plus"></i> <strong>Created:</strong> ' + createdDate + '</span>' +
                           '<span class="batch-item summary-item"><i class="fa fa-calendar-check"></i> <strong>Updated:</strong> ' + updatedDate + '</span>' +
                           '</div>';

                        var separatorContainer = '<div class="batch-separator">' +
                               '<div class="batch-header">' +
                               '<div class="batch-icon"><i class="fa fa-layer-group"></i></div>' +
                               '<div class="batch-info">' +
                               '<div class="batch-title">Batch #' + group + '</div>' +
                               '<div class="batch-details">' +
                               productDetailsHtml +
                               summaryDetailsHtml +
                               '</div>' +
                               '</div>' +
                               '</div>' +
                               '</div>';
                        
                        var colspan = rows.table().columns().header().length;
                        var groupRow = $('<tr class="dtrg-group batch-details-row" id="batch-details-' + group + '" style="display: none;"></tr>')
                            .append('<td colspan="' + colspan + '">' + separatorContainer + '</td>');
                        
                        return groupRow;
                    }
                },
                columnDefs: [
                    <?php if (!$is_viewer): ?>
                    { orderable: false, targets: [12, 16, 17] },
                    { visible: false, targets: [6, 9, 10, 13, 14, 15] }
                    <?php else: ?>
                    { orderable: false, targets: [12] },
                    { visible: false, targets: [6, 9, 10, 13, 14, 15] }
                    <?php endif; ?>
                ],
                language: {
                    lengthMenu: "Show _MENU_ entries per page",
                    search: "Search:",
                    searchPlaceholder: "Search orders..."
                },
                initComplete: function () {
                    this.api().columns.adjust();
                    initializeTruncatedCells();
                },
                drawCallback: function() {
                    initializeTruncatedCells();
                },
                autoWidth: true,
            });
        }
    });

    function initializeTruncatedCells() {
        $('.expandable-text').each(function() {
            if (this.offsetWidth < this.scrollWidth) {
                $(this).addClass('truncated');
            } else {
                $(this).removeClass('truncated');
            }
        });
    }

    $('#ordersTable tbody').on('click', '.toggle-info-btn', function() {
        var batchId = $(this).data('batch');
        var detailRowContent = $('#batch-details-' + batchId).find('.batch-separator').html();

        if (detailRowContent) {
            $('#infoModal .modal-body').html(detailRowContent);
            $('#infoModal #infoModalLabel').text('Details for Batch #' + batchId);
            var infoModal = new bootstrap.Modal(document.getElementById('infoModal'));
            infoModal.show();
        }
    });
    </script>

    <!-- Modal -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-themed">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Batch Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Batch details will be injected here -->
                </div>
            </div>
        </div>
    </div>
</body>
</html>