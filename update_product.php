<?php
$db = mysqli_connect('localhost', 'root', '', 'educarehub');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

$suppliers = [];
$supplierResult = mysqli_query($db, "SELECT supplier_name FROM supplier ORDER BY supplier_name ASC");
if ($supplierResult) {
    while ($row = mysqli_fetch_assoc($supplierResult)) {
        $suppliers[] = $row['supplier_name'];
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $supplier = $_POST['supplier'] ?? '';
    $sku = $_POST['sku'] ?? ($user['sku'] ?? '');
    $category = $_POST['category'] ?? ($user['category'] ?? '');
    $points = $_POST['points'] ?? ($user['points'] ?? '');

    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $img_tmp_path = $_FILES['img']['tmp_name'];
        $img_name = basename($_FILES['img']['name']);
        $upload_dir = 'img/';
        $img_path = $upload_dir . time() . '_' . $img_name;

        if (!move_uploaded_file($img_tmp_path, $img_path)) {
            die("Failed to upload image.");
        }
    } else {
        $img_path = $user['img'] ?? '';
    }

    $stmt = $db->prepare("UPDATE products SET product_name=?, description=?, img=?, supplier=?, sku=?, category=?, points=? WHERE product_id=?");
    $stmt->bind_param("sssssssi", $product_name, $description, $img_path, $supplier, $sku, $category, $points, $id);

    if ($stmt->execute()) {
        header("Location: view_product.php");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}
?>

<?php if (!empty($user)): ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(120deg, #e0e7ef 0%, #f0f4f8 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        margin: 0;
    }
    .update-product-outer {
        min-height: 100vh;
        width: 100vw;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .update-product-card {
        width: 100%;
        max-width: 540px;
        border-radius: 22px;
        box-shadow: 0 8px 32px rgba(60, 72, 88, 0.18);
        overflow: hidden;
        background: #fff;
        margin: 32px 0;
        animation: fadeIn 0.7s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(40px);}
        to { opacity: 1; transform: translateY(0);}
    }
    .update-product-card .card-header {
        background: linear-gradient(90deg, #0F9E99 0%, #2dd4bf 100%);
        color: #fff;
        text-align: center;
        padding: 36px 24px 20px 24px;
        border-bottom: none;
    }
    .update-product-card .card-header h3 {
        margin-bottom: 8px;
        font-weight: 800;
        font-size: 2rem;
        letter-spacing: 1px;
    }
    .update-product-card .card-header p {
        margin-bottom: 0;
        font-size: 1.1rem;
        opacity: 0.92;
        font-weight: 500;
    }
    .update-product-card .form-label i {
        margin-right: 7px;
        color: #0F9E99;
    }
    .update-product-card .img-preview {
        border: 2px solid #e0e7ef;
        border-radius: 12px;
        margin-top: 10px;
        max-width: 140px;
        max-height: 140px;
        object-fit: cover;
        background: #fff;
        box-shadow: 0 2px 8px rgba(60, 72, 88, 0.09);
        display: block;
    }
    .update-product-card .btn-success {
        background: linear-gradient(90deg, #0F9E99 0%, #2dd4bf 100%);
        border: none;
        font-weight: 700;
        font-size: 1.15rem;
        letter-spacing: 1px;
        transition: background 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(60, 72, 88, 0.09);
        padding: 12px 0;
        border-radius: 30px;
    }
    .update-product-card .btn-success:hover, .update-product-card .btn-success:focus {
        background: linear-gradient(90deg, #2dd4bf 0%, #0F9E99 100%);
        box-shadow: 0 4px 16px rgba(60, 72, 88, 0.13);
    }
    .update-product-card .mb-3 {
        margin-bottom: 1.5rem !important;
    }
</style>
<div class="update-product-outer">
    <div class="update-product-card card">
        <div class="card-header">
            <h3><i class="fa fa-edit"></i> Edit Product</h3>
            <p>Update the details and image for this product below.</p>
        </div>
        <div class="card-body p-5">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="product_name" class="form-label">
                        <i class="fa fa-box"></i> Product Name
                    </label>
                    <input type="text" class="form-control" id="product_name" name="product_name"
                           value="<?php echo htmlspecialchars($user['product_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">
                        <i class="fa fa-align-left"></i> Description
                    </label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($user['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="img" class="form-label">
                        <i class="fa fa-image"></i> Product Image
                    </label>
                    <input type="file" class="form-control" id="img" name="img" accept="image/*">
                    <?php if (!empty($user['img'])): ?>
                        <img src="<?php echo htmlspecialchars($user['img']); ?>" alt="Current Image" class="img-preview">
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="supplier" class="form-label">
                        <i class="fa fa-truck"></i> Supplier
                    </label>
                    <select class="form-control" id="supplier" name="supplier" required>
                        <option value="">Select a supplier</option>
                        <?php foreach ($suppliers as $name): ?>
                            <option value="<?php echo htmlspecialchars($name); ?>" <?php if ($user['supplier'] === $name) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="sku" class="form-label">
                        <i class="fa fa-barcode"></i> SKU
                    </label>
                    <input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($user['sku'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">
                        <i class="fa fa-tags"></i> Category
                    </label>
                    <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($user['category'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="points" class="form-label">
                        <i class="fa fa-star"></i> Points
                    </label>
                    <input type="number" class="form-control" id="points" name="points" value="<?php echo htmlspecialchars($user['points'] ?? ''); ?>" required min="0">
                </div>
                <button type="submit" class="btn btn-success w-100 mt-3">
                    <i class="fa fa-save"></i> Update Product
                </button>
            </form>
        </div>
    </div>
</div>
<script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
<?php else: ?>
  <div class="alert alert-danger mt-4">Product not found.</div>
<?php endif; ?>
