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

$suppliers = [];
$supplierResult = mysqli_query($db, "SELECT supplier_name FROM supplier ORDER BY supplier_name ASC");
if ($supplierResult) {
    while ($row = mysqli_fetch_assoc($supplierResult)) {
        $suppliers[] = $row['supplier_name'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $supplier = trim($_POST['supplier'] ?? '');
    $price = trim($_POST['price'] ?? ''); // Added: Retrieve price from POST
    $sku = trim($_POST['sku'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $points = trim($_POST['points'] ?? '');

    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $img_tmp_path = $_FILES['img']['tmp_name'];
        $img_name = basename($_FILES['img']['name']);
        $upload_dir = 'img/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $img_path = $upload_dir . time() . '_' . $img_name;

        if (!move_uploaded_file($img_tmp_path, $img_path)) {
            $errors['img'] = "Failed to upload image.";
        }
    } else {
        $errors['img'] = "Product image is required.";
    }

    if (empty($product_name)) {
        $errors['product_name'] = "Product name is required.";
    }
    if (empty($description)) {
        $errors['description'] = "Product description is required.";
    }
    // Added: Price validation
    if (empty($price)) {
        $errors['price'] = "Product price is required.";
    } elseif (!is_numeric($price) || $price < 0) {
        $errors['price'] = "Product price must be a non-negative number.";
    }

    if (empty($supplier)) {
        $errors['supplier'] = "Supplier is required.";
    } elseif (!in_array($supplier, $suppliers)) {
        $errors['supplier'] = "Selected supplier is invalid.";
    }

    if (empty($sku)) {
        $errors['sku'] = "SKU is required.";
    }
    if (empty($category)) {
        $errors['category'] = "Category is required.";
    }
    if (empty($points)) {
        $errors['points'] = "Points are required.";
    } elseif (!is_numeric($points) || $points < 0) {
        $errors['points'] = "Points must be a non-negative number.";
    }

    $stmt = $db->prepare("SELECT admin_id FROM admins WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $errors['admin'] = "Invalid admin. Cannot add product.";
    }
    $stmt->close();

    if (empty($errors)) {
        // Modified: Added 'price' to the INSERT query
        $stmt = $db->prepare("INSERT INTO products (product_name, description, img, supplier, price, sku, category, points, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // Modified: Added 'd' for double/decimal type for price in bind_param
        $stmt->bind_param("ssssdsssi", $product_name, $description, $img_path, $supplier, $price, $sku, $category, $points, $admin_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Product successfully added.";
            header("Location: add_product.php");
            exit();
        } else {
            $errors['database'] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>