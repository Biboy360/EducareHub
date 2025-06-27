<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has stock_encoder role or higher (inventory management) OR is a viewer
$allowed_roles = ['stock_encoder', 'admin', 'super_admin', 'viewer'];
if (!in_array($_SESSION['admins']['role'], $allowed_roles)) {
    $_SESSION['error_message'] = "Access denied. Only Stock Encoders and above, or Viewers can access inventory.";
    header("Location: admin_dashboard.php");
    exit();
}

$role = strtolower($_SESSION['admins']['role'] ?? $_SESSION['admins']['Role'] ?? '');
if (!in_array($role, ['super_admin', 'admin', 'stock_encoder', 'viewer'])) {
    echo '<div style="display:flex;justify-content:center;align-items:center;height:100vh;background:#f8f9fa;"><div style="background:#fff;padding:32px 48px;border-radius:18px;box-shadow:0 4px 24px rgba(60,72,88,0.13);font-size:1.2em;color:#e74c3c;">Access Denied: You do not have permission to view this page.</div></div>';
    exit;
}

$is_viewer = ($_SESSION['admins']['role'] ?? '') === 'viewer';

$db = mysqli_connect('localhost', 'root', '', 'educarehub');
if (!$db) die("Connection failed: " . mysqli_connect_error());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stock_id = (int)($_POST['stock_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);

    $stmt = $db->prepare("UPDATE stocks SET quantity = ?, updated_at = NOW() WHERE stocks_id = ?");
    $stmt->bind_param("ii", $quantity, $stock_id);
    $stmt->execute();
    $stmt->close();
    header("Location: inventory.php");
    exit();
}

$query = "SELECT 
            s.stocks_id, 
            s.product_id, 
            p.product_name, 
            p.sku,
            p.category,
            p.price,
            p.supplier as supplier_name,
            s.quantity, 
            s.created_at, 
            s.updated_at, 
            COALESCE(a.fullname, 'Unknown Admin') AS created_by 
          FROM stocks s
          JOIN products p ON s.product_id = p.product_id
          LEFT JOIN admins a ON s.created_by = a.admin_id
          ORDER BY s.updated_at DESC";
$results = mysqli_query($db, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inventory Management</title>
  <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="css/style.css?v=4">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css"/>

  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    #inventoryTable thead th {
        background-color: #0F9E99;
        color: #fff;
        border-bottom: 2px solid #0d8a86;
        font-weight: 600;
    }
    /* Modal styling */
    #inventoryInfoModal .modal-body { padding: 0; }
    .details-separator { padding: 1.5rem 2rem; background: linear-gradient(to right, #eef5f9, #f8fafc); border-left: 6px solid var(--theme-color); }
    .details-header { display: flex; align-items: center; gap: 1.5rem; }
    .details-icon { flex-shrink: 0; }
    .details-info { flex-grow: 1; }
    .details-title { font-weight: 700; font-size: 1.5rem; color: #1a3b5a; margin-bottom: 0.75rem; }
    .summary-list { display: flex; flex-wrap: wrap; gap: 1.5rem; }
    .summary-item { font-size: 0.9rem; color: #34495e; display: flex; align-items: center; gap: 0.5rem; }

    /* Custom Badge Colors for Inventory Status */
    .badge.status-stock-none { background-color: #6c757d; color: white; }
    .badge.status-stock-low { background-color: #dc3545; color: white; }
    .badge.status-stock-normal { background-color: #0d6efd; color: white; }
    .badge.status-stock-high { background-color: #198754; color: white; }
  </style>
</head>
<body class="body">
  <div class="dashboard_main_container">
    <?php include_once('partials/app_topNav.php'); ?>
    <?php include_once('partials/app_horizontal_nav.php'); ?>
    <div class="dashboard_content">
      <div class="dashboard_content_main">
        <div class="column column-12">
          <h2 class="section_header"><i class="fa fa-boxes-stacked"></i> Inventory Stocks</h2>
          <?php if ($is_viewer): ?>
          <div class="alert alert-info mt-3" role="alert">
              <i class="fa fa-eye"></i> <strong>Viewer Mode:</strong> You are viewing this page in read-only mode.
          </div>
          <?php endif; ?>
          <div class="section_content">
            <div class="users">
              <div class="table-container">
                <table id="inventoryTable" class="table table-striped table-themed" style="width:100%; table-layout: fixed;">
                  <thead>
                    <tr>
                      <th style="width: 20%;">Product Name</th>
                      <th style="width: 12%;">SKU</th>
                      <th style="width: 12%;">Category</th>
                      <th style="width: 15%;">Quantity</th>
                      <th style="width: 15%;">Unit Price</th>
                      <th style="width: 15%;">Total Value</th>
                      <th style="width: 11%;">More Info</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = mysqli_fetch_assoc($results)) : ?>
                      <tr>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= htmlspecialchars($row['sku']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td>
                          <?php
                            $qty = (int)$row['quantity'];
                            $status_class = $qty == 0 ? 'status-stock-none' : ($qty <= 5 ? 'status-stock-low' : ($qty >= 100 ? 'status-stock-high' : 'status-stock-normal'));
                            $status_text = $qty == 0 ? 'No Stock' : ($qty <= 5 ? 'Low' : ($qty >= 100 ? 'High' : 'Normal'));
                          ?>
                          <?= htmlspecialchars($qty) ?>
                          <span class="badge rounded-pill <?= $status_class ?> ms-1"><?= $status_text ?></span>
                        </td>
                        <td>₱<?= htmlspecialchars(number_format($row['price'], 2)) ?></td>
                        <td>₱<?= htmlspecialchars(number_format($row['quantity'] * $row['price'], 2)) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-info toggle-info-btn" 
                                    data-product-name="<?= htmlspecialchars($row['product_name']); ?>"
                                    data-supplier-name="<?= htmlspecialchars($row['supplier_name'] ?? 'N/A'); ?>"
                                    data-created-by="<?= htmlspecialchars($row['created_by']); ?>"
                                    data-created-at="<?= htmlspecialchars($row['created_at']); ?>"
                                    data-updated-at="<?= htmlspecialchars($row['updated_at']); ?>">
                                <i class="fa fa-eye"></i>
                            </button>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- More Info Modal -->
  <div class="modal fade" id="inventoryInfoModal" tabindex="-1" aria-labelledby="inventoryInfoModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="inventoryInfoModalLabel">Product Details</h5>
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
        $('#inventoryTable').DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"lB><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"rt>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                { extend: 'copy', text: '<i class="fa fa-copy"></i> Copy', className: 'btn-secondary-themed' },
                { extend: 'csv', text: '<i class="fa fa-file-csv"></i> CSV', className: 'btn-secondary-themed' },
                { extend: 'excel', text: '<i class="fa fa-file-excel"></i> Excel', className: 'btn-secondary-themed' },
                { extend: 'pdf', text: '<i class="fa fa-file-pdf"></i> PDF', className: 'btn-secondary-themed' },
                { extend: 'print', text: '<i class="fa fa-print"></i> Print', className: 'btn-secondary-themed' }
            ],
            order: [[0, 'asc']], // Sort by product name
            columnDefs: [
                { orderable: false, targets: [6] } 
            ]
        });

        // Modal script
        $('#inventoryTable tbody').on('click', '.toggle-info-btn', function () {
            var button = $(this);
            var productName = button.data('product-name');
            var supplierName = button.data('supplier-name');
            var createdBy = button.data('created-by');
            var createdAt = new Date(button.data('created-at')).toLocaleDateString('en-US');
            var updatedAt = new Date(button.data('updated-at')).toLocaleDateString('en-US');

            var modalTitle = $('#inventoryInfoModal .modal-title');
            modalTitle.text('Details for ' + productName);

            var modalBody = $('#inventoryInfoModal .modal-body');

            const iconStyles = `
                font-size: 2rem; color: #ffffff; background: linear-gradient(45deg, var(--theme-color), #17a2b8);
                padding: 1rem; border-radius: 50%; display: flex; align-items: center; justify-content: center;
                width: 70px; height: 70px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                border: 2px solid rgba(255, 255, 255, 0.3);
            `;

            var modalContent = `
            <div class="details-separator">
                <div class="details-header">
                    <div class="details-icon" style="${iconStyles}">
                        <i class="fa fa-box-open"></i>
                    </div>
                    <div class="details-info">
                        <div class="details-title">${productName}</div>
                        <div class="summary-list">
                            <span class="summary-item"><i class="fa fa-truck"></i> <strong>Supplier:</strong>&nbsp;${supplierName}</span>
                            <span class="summary-item"><i class="fa fa-user"></i> <strong>Created By:</strong>&nbsp;${createdBy}</span>
                            <span class="summary-item"><i class="fa fa-calendar-plus"></i> <strong>Created:</strong>&nbsp;${createdAt}</span>
                            <span class="summary-item"><i class="fa fa-calendar-check"></i> <strong>Updated:</strong>&nbsp;${updatedAt}</span>
                        </div>
                    </div>
                </div>
            </div>
            `;
            
            modalBody.html(modalContent);
            
            var myModal = new bootstrap.Modal(document.getElementById('inventoryInfoModal'));
            myModal.show();
        });
    });
  </script>
</body>
</html>
