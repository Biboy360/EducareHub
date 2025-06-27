<?php
session_start();
$db = mysqli_connect('localhost', 'root', '', 'educarehub');
if (!$db) die("Connection failed: " . mysqli_connect_error());

$id = (int)($_GET['id'] ?? 0);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity_received_new = (int)($_POST['quantity_received_new'] ?? 0);
    $supplier_name = trim($_POST['supplier_name'] ?? '');
    // Auto-calculate status
    $stmtQ = $db->prepare("SELECT quantity_ordered FROM products_supplier WHERE p_supplier_id = ?");
    $stmtQ->bind_param("i", $id);
    $stmtQ->execute();
    $resultQ = $stmtQ->get_result();
    $orderQ = $resultQ->fetch_assoc();
    $stmtQ->close();
    $quantity_ordered = $orderQ['quantity_ordered'] ?? 0;
    
    // Get current received quantity
    $stmtCurrent = $db->prepare("SELECT quantity_recieved FROM products_supplier WHERE p_supplier_id = ?");
    $stmtCurrent->bind_param("i", $id);
    $stmtCurrent->execute();
    $resultCurrent = $stmtCurrent->get_result();
    $currentData = $resultCurrent->fetch_assoc();
    $stmtCurrent->close();
    $current_received = (int)($currentData['quantity_recieved'] ?? 0);
    
    // Add new received quantity to existing
    $total_received = $current_received + $quantity_received_new;
    $quantity_remaining = max(0, $quantity_ordered - $total_received);
    
    if ($total_received == 0) {
        $status = 'Pending';
    } elseif ($quantity_remaining == 0) {
        $status = 'Completed';
    } else {
        $status = 'Incomplete';
    }

    if ($supplier_name === '') {
        $errors[] = "Supplier name cannot be empty.";
    }

    if (empty($errors)) {
        // --- Start Transaction ---
        $db->begin_transaction();

        try {
            // Get old data before update for calculating diff
            $stmtOld = $db->prepare("SELECT quantity_recieved, product FROM products_supplier WHERE p_supplier_id = ?");
            $stmtOld->bind_param("i", $id);
            $stmtOld->execute();
            $resultOld = $stmtOld->get_result();
            $oldData = $resultOld->fetch_assoc();
            $stmtOld->close();

            if (!$oldData) {
                throw new Exception("Order not found.");
            }

            $oldQuantityReceived = (int)$oldData['quantity_recieved'];
            $productId = (int)$oldData['product'];
            $loggedInAdminId = $_SESSION['admins']['admin_id'] ?? null;

            if (!$loggedInAdminId) {
                throw new Exception("Unable to identify the logged-in admin. Session may have expired.");
            }

            // Get supplier ID from supplier name
            $stmtSupplier = $db->prepare("SELECT supplier_id FROM supplier WHERE supplier_name = ?");
            $stmtSupplier->bind_param("s", $supplier_name);
            $stmtSupplier->execute();
            $resultSupplier = $stmtSupplier->get_result();
            $supplier = $resultSupplier->fetch_assoc();
            $stmtSupplier->close();

            if (!$supplier) {
                throw new Exception("Supplier not found in database.");
            }
            $supplier_id = $supplier['supplier_id'];

            // 1. Update the order itself
            $updateOrder = $db->prepare("UPDATE products_supplier SET quantity_recieved = ?, quantity_remaining = ?, status = ?, supplier = ?, supplier_name = ? WHERE p_supplier_id = ?");
            $updateOrder->bind_param("iisisi", $total_received, $quantity_remaining, $status, $supplier_id, $supplier_name, $id);
            if (!$updateOrder->execute()) {
                throw new Exception("Failed to update order: " . $updateOrder->error);
            }
            $updateOrder->close();

            // 2. Update the inventory (stocks table)
            $diff = $total_received - $oldQuantityReceived;
            if ($diff !== 0) {
                // Find stock record based only on product_id
                $stmtStock = $db->prepare("SELECT stocks_id, quantity FROM stocks WHERE product_id = ?");
                $stmtStock->bind_param("i", $productId);
                $stmtStock->execute();
                $resultStock = $stmtStock->get_result();

                if ($resultStock->num_rows > 0) {
                    $stock = $resultStock->fetch_assoc();
                    $newQuantity = $stock['quantity'] + $diff;
                    $updateStock = $db->prepare("UPDATE stocks SET quantity = ?, updated_at = NOW() WHERE stocks_id = ?");
                    $updateStock->bind_param("ii", $newQuantity, $stock['stocks_id']);
                    if (!$updateStock->execute()) {
                        throw new Exception("Failed to update stock quantity: " . $updateStock->error);
                    }
                    $updateStock->close();
                } else {
                    $insertStock = $db->prepare("INSERT INTO stocks (product_id, created_by, quantity, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                    $insertStock->bind_param("iii", $productId, $loggedInAdminId, $diff);
                    if (!$insertStock->execute()) {
                        throw new Exception("Failed to create new stock record: " . $insertStock->error);
                    }
                    $insertStock->close();
                }
                $stmtStock->close();
            }

            // --- Commit Transaction ---
            $db->commit();
            $_SESSION['success'] = "Products received successfully and inventory updated.";
            header("Location: view_orders.php");
            exit();

        } catch (Exception $e) {
            // --- Rollback Transaction ---
            $db->rollback();
            $errors[] = "An error occurred: " . $e->getMessage();
        }
    }
}

$query = "SELECT * FROM products_supplier WHERE p_supplier_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; min-height: 100vh; }
        .edit-order-card { max-width: 500px; margin: 40px auto; border-radius: 18px; box-shadow: 0 8px 32px rgba(60, 72, 88, 0.15); }
        .edit-order-card .card-header { background: linear-gradient(90deg, #0F9E99 0%, #2dd4bf 100%); color: #fff; text-align: center; padding: 30px 20px 18px 20px; }
        .edit-order-card .card-header h3 { margin-bottom: 0; font-weight: 700; letter-spacing: 1px; }
        .edit-order-card .card-header p { margin-bottom: 0; font-size: 1rem; opacity: 0.85; }
        .edit-order-card .btn-primary { background: linear-gradient(90deg, #0F9E99 0%, #2dd4bf 100%); border: none; font-weight: 600; letter-spacing: 1px; transition: background 0.2s, box-shadow 0.2s; box-shadow: 0 2px 8px rgba(60, 72, 88, 0.07); }
        .edit-order-card .btn-primary:hover, .edit-order-card .btn-primary:focus { background: linear-gradient(90deg, #2dd4bf 0%, #0F9E99 100%); box-shadow: 0 4px 16px rgba(60, 72, 88, 0.13); }
    </style>
</head>
<body>
<div class="edit-order-card card">
    <div class="card-header">
        <h3><i class="fa fa-box"></i> Receive Products</h3>
        <p>Update received quantity for Order ID: <?= htmlspecialchars($id) ?></p>
    </div>
    <div class="card-body p-4">
        <?php foreach ($errors as $e) echo "<div class='alert alert-danger'>" . htmlspecialchars($e) . "</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="supplier_name" class="form-label">Supplier Name</label>
                <input type="text" class="form-control" name="supplier_name" id="supplier_name" value="<?= htmlspecialchars($order['supplier_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Current Received Quantity</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($order['quantity_recieved'] ?? 0) ?>" readonly style="background-color: #f8f9fa;">
                <small class="form-text text-muted">This is the total quantity already received for this order.</small>
            </div>
            <div class="mb-3">
                <label for="quantity_received_new" class="form-label">Additional Quantity to Receive</label>
                <input type="number" class="form-control" name="quantity_received_new" id="quantity_received_new" min="0" value="0" required>
                <small class="form-text text-muted">Enter the additional quantity being received now.</small>
            </div>
            <button type="submit" class="btn btn-primary w-100">Receive Products</button>
        </form>
        <a href="view_orders.php" class="btn btn-link mt-3 w-100">Back to Orders</a>
    </div>
</div>
<script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
</body>
</html>
