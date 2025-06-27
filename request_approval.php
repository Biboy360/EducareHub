<?php session_start();

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has rewards_manager role or higher (rewards management)
$allowed_roles = ['rewards_manager', 'admin', 'super_admin'];
if (!in_array($_SESSION['admins']['role'], $allowed_roles)) {
    $_SESSION['error_message'] = "Access denied. Only Rewards Managers and above can approve rewards.";
    header("Location: admin_dashboard.php");
    exit();
}

$role = strtolower($_SESSION['admins']['role'] ?? $_SESSION['admins']['Role'] ?? ''); if (!in_array($role, ['super_admin', 'admin', 'rewards_manager'])) { echo '<div style="display:flex;justify-content:center;align-items:center;height:100vh;background:#f8f9fa;"><div style="background:#fff;padding:32px 48px;border-radius:18px;box-shadow:0 4px 24px rgba(60,72,88,0.13);font-size:1.2em;color:#e74c3c;">Access Denied: You do not have permission to view this page.</div></div>'; exit(); } ?>
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
        #myTable thead th {
            background-color: #0F9E99;
            color: #fff;
            border-bottom: 2px solid #0d8a86;
            font-weight: 600;
        }
    </style>
</head>
<body class="body">
  <div class="dashboard_main_container">
    <?php include('partials/app_topNav.php'); ?>
    <?php include('partials/app_horizontal_nav.php'); ?>
    <div class="dashboard_content">
      <div class="dashboard_content_main">
                <h2 class="section_header"><i class="fa fa-check-circle"></i> Reward Requests</h2>
                <div class="section_content">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary-themed" id="acceptAllBtn"><i class="fa fa-check-double"></i> Accept All Pending</button>
                    </div>
                    <div id="ordersPanel">
                        <!-- Orders will be dynamically loaded here -->
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for Reward Order Actions -->
  <div class="modal fade" id="adminActionModal" tabindex="-1" aria-labelledby="adminActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="adminActionModalLabel">Order Status Updated</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          The User's Info will be sent to the courier.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <script src="js/script.js"></script>
  <script>
  let myTable;

  function fetchAllOrders() {
      fetch('../EducareHubUSer/get_all_reward_orders.php')
          .then(res => res.json())
          .then(data => {
              const panel = document.getElementById('ordersPanel');
              if (data.success && data.orders.length > 0) {
                  let html = '<table id="myTable" class="table table-bordered table-striped table-themed" style="width:100%; table-layout: fixed;"><thead><tr>' +
                      '<th style="width: 20%;">Product</th><th style="width: 15%;">User</th><th style="width: 25%;">Address</th><th style="width: 12%;">Contact</th><th style="width: 12%;">Date</th><th style="width: 8%;">Status</th><th style="width: 8%;">Actions</th></tr></thead><tbody>';
                  data.orders.forEach(order => {
                      let statusBadge = '';
                      if (order.status === 'pending') statusBadge = '<span class="status-badge status-pending">Pending</span>';
                      else if (order.status === 'on delivery') statusBadge = '<span class="status-badge status-delivery">On Delivery</span>';
                      else if (order.status === 'declined') statusBadge = '<span class="status-badge status-declined">Declined</span>';
                      else if (order.status === 'delivered') statusBadge = '<span class="status-badge status-delivered"><i class="bi bi-check-circle-fill me-1"></i>Successful</span>';
                      else if (order.status === 'unsuccessful') statusBadge = '<span class="status-badge status-unsuccessful"><i class="bi bi-x-circle-fill me-1"></i>Unsuccessful</span>';
                      else statusBadge = order.status;
                      html += `<tr data-order-id="${order.order_id}">
                          <td>${order.product_name}</td>
                          <td>${order.username}</td>
                          <td>${order.address}</td>
                          <td>${order.contact}</td>
                          <td>${order.order_date}</td>
                          <td class="order-status">${statusBadge}</td>
                          <td>`;
                      if (order.status === 'pending') {
                          html += `<button class="btn-action-accept me-1 accept-btn">Accept</button>`;
                          html += `<button class="btn-action-decline decline-btn">Decline</button>`;
                      } else if (order.status === 'on delivery') {
                          html += `<button class="btn-action-deliver deliver-success">Deliver Successful</button>`;
                          html += `<button class="btn-action-fail deliver-fail">Unsuccessful</button>`;
                      } else if (order.status === 'delivered') {
                          html += '<span class="status-badge status-delivered">Delivered</span>';
                      } else if (order.status === 'unsuccessful') {
                          html += '<span class="status-badge status-unsuccessful">Unsuccessful</span>';
                      } else if (order.status === 'declined') {
                          html += '<span class="status-badge status-declined">Declined</span>';
                      } else {
                          html += '-';
                      }
                      html += `</td></tr>`;
                  });
                  html += '</tbody></table>';
                  panel.innerHTML = html;
                  
                  // Initialize DataTable
                  if (myTable) {
                      myTable.destroy();
                  }
                  myTable = $('#myTable').DataTable({
                      responsive: true,
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
                      pageLength: 10,
                      order: [[4, 'desc']], // Sort by date column (index 4) in descending order
                      columnDefs: [
                          {
                              targets: [6], // Actions column
                              orderable: false,
                              searchable: false
                          }
                      ]
                  });
              } else {
                  panel.innerHTML = '<p>No reward orders found.</p>';
              }
          });
  }

  document.addEventListener('DOMContentLoaded', function() {
      fetchAllOrders();
      const modal = new bootstrap.Modal(document.getElementById('adminActionModal'));
      const modalBody = document.querySelector('#adminActionModal .modal-body');

      document.getElementById('ordersPanel').addEventListener('click', function(e) {
          const row = e.target.closest('tr[data-order-id]');
          if (!row) return;
          const orderId = row.getAttribute('data-order-id');
          let newStatus = '';
          let modalMsg = '';
          const actionCell = row.querySelector('td:last-child');

          if (e.target.classList.contains('accept-btn')) {
              newStatus = 'on delivery';
              modalMsg = "The User's Info will be sent to the courier.";
              fetch('../EducareHubUSer/update_reward_order_status.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                  body: `order_id=${encodeURIComponent(orderId)}&status=${encodeURIComponent(newStatus)}`
              })
              .then(res => res.json())
              .then(data => {
                  if (data.success) {
                      // Update status badge in the table
                      row.querySelector('.order-status').innerHTML = '<span class="status-badge status-delivery">On Delivery</span>';
                      // Show Deliver Successful and Unsuccessful buttons
                      actionCell.innerHTML = `
                        <button class="btn-action-deliver deliver-success">Deliver Successful</button>
                        <button class="btn-action-fail deliver-fail">Unsuccessful</button>
                      `;
                      modalBody.textContent = modalMsg;
                      modal.show();
                  } else {
                      alert('Failed to update order status.');
                  }
              });
              return;
          }
          if (e.target.classList.contains('decline-btn')) {
              newStatus = 'declined';
              modalMsg = "The user's coins will be refunded.";
              fetch('../EducareHubUSer/update_reward_order_status.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                  body: `order_id=${encodeURIComponent(orderId)}&status=${encodeURIComponent(newStatus)}`
              })
              .then(res => res.json())
              .then(data => {
                  if (data.success) {
                      row.querySelector('.order-status').innerHTML = '<span class="status-badge status-declined">Declined</span>';
                      actionCell.innerHTML = '-';
                      modalBody.textContent = modalMsg;
                      modal.show();
                  } else {
                      alert('Failed to update order status.');
                  }
              });
              return;
          }
          if (e.target.classList.contains('deliver-success')) {
              newStatus = 'delivered';
              fetch('../EducareHubUSer/update_reward_order_status.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                  body: `order_id=${encodeURIComponent(orderId)}&status=${encodeURIComponent(newStatus)}`
              })
              .then(res => res.json())
              .then(data => {
                  if (data.success) {
                      row.querySelector('.order-status').innerHTML = '<span class="status-badge status-delivered">Delivered</span>';
                      actionCell.innerHTML = '<span class="badge bg-success">Delivered</span>';
                  } else {
                      alert('Failed to update order status.');
                  }
              });
              return;
          }
          if (e.target.classList.contains('deliver-fail')) {
              newStatus = 'unsuccessful';
              fetch('../EducareHubUSer/update_reward_order_status.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                  body: `order_id=${encodeURIComponent(orderId)}&status=${encodeURIComponent(newStatus)}`
              })
              .then(res => res.json())
              .then(data => {
                  if (data.success) {
                      row.querySelector('.order-status').innerHTML = '<span class="status-badge status-unsuccessful">Unsuccessful</span>';
                      actionCell.innerHTML = '<span class="badge bg-danger">Unsuccessful</span>';
                  } else {
                      alert('Failed to update order status.');
                  }
              });
              return;
          }
      });

      document.getElementById('acceptAllBtn').addEventListener('click', function() {
          // Find all pending order rows
          const pendingRows = Array.from(document.querySelectorAll('#ordersPanel tr[data-order-id]')).filter(row => {
              const statusCell = row.querySelector('.order-status');
              return statusCell && statusCell.textContent.trim() === 'Pending';
          });
          if (pendingRows.length === 0) {
              alert('No pending orders to accept.');
              return;
          }
          // Accept all pending orders
          let completed = 0;
          pendingRows.forEach(row => {
              const orderId = row.getAttribute('data-order-id');
              fetch('../EducareHubUSer/update_reward_order_status.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                  body: `order_id=${encodeURIComponent(orderId)}&status=on delivery`
              })
              .then(res => res.json())
              .then(data => {
                  completed++;
                  if (completed === pendingRows.length) {
                      modalBody.textContent = "The User's Info will be sent to the courier.";
                      modal.show();
                      fetchAllOrders();
                  }
              });
          });
      });
  });
  </script>
</body>
</html>
<?php
// Close the database connection
if (isset($db)) {
    mysqli_close($db);
}
?>