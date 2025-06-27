<?php

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['error'] = "Access denied. Please log in as an admin.";
    header("Location: admin_login.php");
    exit();
}

$errors = [];
$db = mysqli_connect('localhost', 'root', '', 'educarehub');
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

$admin_id = $_SESSION['admin_id'];

$productOptions = [];
$sql = "
    SELECT 
        p.product_id, 
        p.product_name, 
        p.supplier AS supplier_name,
        s.supplier_id,
        s.contact_no,
        p.category,
        p.sku,
        p.points
    FROM products p
    LEFT JOIN supplier s ON p.supplier = s.supplier_name
    ORDER BY p.product_name ASC
";

$result = mysqli_query($db, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $productOptions[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log the POST data
    error_log("POST data received: " . print_r($_POST, true));
    
    $product_ids = $_POST['product_id'] ?? [];
    $supplier_names = $_POST['supplier_name'] ?? [];
    $categories = $_POST['category'] ?? [];
    $skus = $_POST['sku'] ?? [];
    $pointsArr = $_POST['points'] ?? [];
    $quantities_ordered = $_POST['quantity_ordered'] ?? [];

    // Debug: Log the processed arrays
    error_log("Processed arrays - product_ids: " . print_r($product_ids, true));
    error_log("Processed arrays - supplier_names: " . print_r($supplier_names, true));
    error_log("Processed arrays - quantities_ordered: " . print_r($quantities_ordered, true));

    $batch = strtoupper(bin2hex(random_bytes(4))); // Shared batch code
    $has_error = false;

    foreach ($product_ids as $index => $product_id_raw) {
        $product_id = (int)$product_id_raw;
        $supplier_name = trim($supplier_names[$index] ?? '');
        $category = trim($categories[$index] ?? '');
        $sku = trim($skus[$index] ?? '');
        $points = trim($pointsArr[$index] ?? '');
        $quantity_ordered = (int)($quantities_ordered[$index] ?? 0);
        $quantity_received = 0;
        $quantity_remaining = $quantity_ordered - $quantity_received;
        $status = 'Pending';

        // Validation
        if ($product_id === 0 || $supplier_name === '' || $quantity_ordered <= 0) {
            $errors[$index] = "Missing or invalid data for order #" . ($index + 1);
            $has_error = true;
            continue;
        }

        // Get supplier_id
        $stmt = $db->prepare("SELECT supplier_id FROM supplier WHERE supplier_name = ?");
        $stmt->bind_param("s", $supplier_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $supplierData = $result->fetch_assoc();
        $stmt->close();

        if (!$supplierData) {
            $errors[$index] = "Supplier not found for order #" . ($index + 1);
            $has_error = true;
            continue;
        }

        $supplier_id = $supplierData['supplier_id'];

        // Insert order
        $stmt = $db->prepare("INSERT INTO products_supplier 
            (supplier, supplier_name, product, quantity_ordered, quantity_recieved, quantity_remaining, status, batch, created_by, category, sku, points) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "isiiiississs",
            $supplier_id,
            $supplier_name,
            $product_id,
            $quantity_ordered,
            $quantity_received,
            $quantity_remaining,
            $status,
            $batch,
            $admin_id,
            $category,
            $sku,
            $points
        );

        if (!$stmt->execute()) {
            $errors[$index] = "Database error for order #" . ($index + 1) . ": " . $stmt->error;
            $has_error = true;
        }

        $stmt->close();
    }

    if (!$has_error) {
        error_log("All orders processed successfully. Batch code: $batch");
        $_SESSION['success'] = "All orders inserted successfully. Batch code: $batch";
        header("Location: add_orders.php");
        exit();
    } else {
        error_log("Errors occurred during order processing: " . print_r($errors, true));
    }
}
?>
