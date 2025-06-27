<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has rewards_manager role or higher OR is a viewer
$allowed_roles = ['rewards_manager', 'admin', 'super_admin', 'viewer'];
if (!in_array($_SESSION['admins']['role'], $allowed_roles)) {
    $_SESSION['error_message'] = "Access denied. Only Rewards Managers and above, or Viewers can view reward orders.";
    header("Location: admin_dashboard.php");
    exit();
}

$is_viewer = ($_SESSION['admins']['role'] ?? '') === 'viewer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reward Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">All Reward Orders</h2>
    <?php if ($is_viewer): ?>
    <div class="alert alert-info" role="alert">
        <i class="fa fa-eye"></i> <strong>Viewer Mode:</strong> You are viewing this page in read-only mode. You cannot approve or decline orders.
    </div>
    <?php endif; ?>
    <div id="ordersPanel">
        <p>Loading orders...</p>
    </div>
</div>

<!-- Modal -->
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

<script>
const isViewer = <?php echo $is_viewer ? 'true' : 'false'; ?>;

function fetchAllOrders() {
    fetch('get_all_reward_orders.php')
        .then(res => res.json())
        .then(data => {
            const panel = document.getElementById('ordersPanel');
            if (data.success && data.orders.length > 0) {
                let html = '<table class="table table-bordered table-striped"><thead><tr>' +
                    '<th>Product</th><th>User</th><th>Address</th><th>Contact</th><th>Date</th><th>Status</th>';
                if (!isViewer) {
                    html += '<th>Actions</th>';
                }
                html += '</tr></thead><tbody>';
                data.orders.forEach(order => {
                    let statusBadge = '';
                    if (order.status === 'pending') statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
                    else if (order.status === 'on delivery') statusBadge = '<span class="badge bg-info text-dark">On Delivery</span>';
                    else if (order.status === 'declined') statusBadge = '<span class="badge bg-danger">Declined</span>';
                    else statusBadge = order.status;
                    html += `<tr data-order-id="${order.order_id}">
                        <td>${order.product_name}</td>
                        <td>${order.username}</td>
                        <td>${order.address}</td>
                        <td>${order.contact}</td>
                        <td>${order.order_date}</td>
                        <td class="order-status">${statusBadge}</td>`;
                    if (!isViewer) {
                        html += `<td>`;
                        if (order.status === 'pending') {
                            html += `<a href="update_reward_order_status.php?id=${encodeURIComponent(order.order_id)}&status=on_delivery" class="btn-approve" onclick="return confirm('Approve this order for delivery?');"><i class="fa-solid fa-truck-fast"></i> Approve</a>`;
                            html += `<a href="update_reward_order_status.php?id=${encodeURIComponent(order.order_id)}&status=declined" class="btn-decline" onclick="return confirm('Are you sure you want to decline this order?');"><i class="fa-solid fa-ban"></i> Decline</a>`;
                        } else {
                            html += '-';
                        }
                        html += `</td>`;
                    }
                    html += `</tr>`;
                });
                html += '</tbody></table>';
                panel.innerHTML = html;
            } else {
                panel.innerHTML = '<p>No reward orders found.</p>';
            }
        });
}

document.addEventListener('DOMContentLoaded', function() {
    fetchAllOrders();
    const modal = new bootstrap.Modal(document.getElementById('adminActionModal'));

    document.getElementById('ordersPanel').addEventListener('click', function(e) {
        const row = e.target.closest('tr[data-order-id]');
        if (!row) return;
        const orderId = row.getAttribute('data-order-id');
        let newStatus = '';
        if (e.target.classList.contains('accept-btn')) {
            newStatus = 'on delivery';
        } else if (e.target.classList.contains('decline-btn')) {
            newStatus = 'declined';
        } else {
            return;
        }
        fetch('update_reward_order_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `order_id=${encodeURIComponent(orderId)}&status=${encodeURIComponent(newStatus)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update status badge in the table
                let statusBadge = '';
                if (newStatus === 'on delivery') statusBadge = '<span class="badge bg-info text-dark">On Delivery</span>';
                else if (newStatus === 'declined') statusBadge = '<span class="badge bg-danger">Declined</span>';
                row.querySelector('.order-status').innerHTML = statusBadge;
                if (!isViewer) {
                    row.querySelector('td:last-child').innerHTML = '-';
                }
                modal.show();
            } else {
                alert('Failed to update order status.');
            }
        });
    });
});
</script>
</body>
</html> 