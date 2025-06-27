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
    $_SESSION['error_message'] = "Access denied. Only Stock Encoders and above can manage products.";
    header("Location: admin_dashboard.php");
    exit();
}

$is_viewer = ($_SESSION['admins']['role'] ?? '') === 'viewer';

include('insert_product.php'); ?>
<?php $old = $_POST ?? []; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Product - Admin</title>
  <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="css/style.css?v=4">
  
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
  
  <!-- jQuery (required for Select2) -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="body">
  <div class="dashboard_main_container">
    <?php include('partials/app_topNav.php'); ?>
    <?php include('partials/app_horizontal_nav.php'); ?>
    <div class="dashboard_content">
      <div class="dashboard_content_main">
        <div class="row justify-content-center">
          <div class="column-12 col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="section_header" style="margin-bottom: 0;"><i class="fa fa-plus"></i> Add New Product</h1>
                <a href="view_product.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> View All Products</a>
            </div>
            <div class="section_content">
              <?php if (!$is_viewer): ?>
              <form action="add_product.php" method="POST" class="appForm" enctype="multipart/form-data" novalidate>
                
                <?php if (isset($_SESSION['success'])): ?>
                  <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                  </div>
                <?php endif; ?>
                
                <?php if (isset($errors) && count($errors) > 0): ?>
                  <div class="alert alert-danger">
                    <ul>
                      <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="appForminputContainer">
                            <label for="product_name">Product Name</label>
                            <input type="text" class="appForminput" id="product_name" name="product_name" placeholder="Enter the product name..."
                                   value="<?php echo htmlspecialchars($old['product_name'] ?? '') ?>" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="appForminputContainer">
                            <label for="supplier">Supplier</label>
                            <select id="supplier" name="supplier" class="form-select" data-placeholder="Search and select a supplier..." required>
                                <option></option> <!-- Placeholder for Select2 -->
                                <?php 
                                  $db = mysqli_connect('localhost', 'root', '', 'educarehub');
                                  $suppliers_query = mysqli_query($db, "SELECT supplier_name FROM supplier ORDER BY supplier_name ASC");
                                  while ($supplier_row = mysqli_fetch_assoc($suppliers_query)) {
                                      $supplier_name = htmlspecialchars($supplier_row['supplier_name']);
                                      $selected = (isset($old['supplier']) && $old['supplier'] == $supplier_name) ? 'selected' : '';
                                      echo "<option value=\"$supplier_name\" $selected>$supplier_name</option>";
                                  }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="appForminputContainer">
                    <label for="description">Description</label>
                    <textarea class="appForminput" id="description" name="description" placeholder="Enter a brief description of the product..." rows="3" required><?php echo htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="appForminputContainer">
                            <label for="sku">SKU</label>
                            <input type="text" class="appForminput" id="sku" name="sku" placeholder="Enter SKU..."
                                   value="<?php echo htmlspecialchars($old['sku'] ?? '') ?>" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="appForminputContainer">
                            <label for="category">Category</label>
                            <input type="text" class="appForminput" id="category" name="category" placeholder="Enter category..."
                                   value="<?php echo htmlspecialchars($old['category'] ?? '') ?>" required />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="appForminputContainer">
                            <label for="price">Points</label>
                            <input type="number" class="appForminput" id="price" name="price" placeholder="Enter points required..."
                                   value="<?php echo htmlspecialchars($old['price'] ?? '') ?>" required min="0" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="appForminputContainer">
                            <label for="points">Price (₱)</label>
                            <input type="number" step="0.01" class="appForminput" id="points" name="points" placeholder="0.00"
                                   value="<?php echo htmlspecialchars($old['points'] ?? '') ?>" required />
                        </div>
                    </div>
                </div>
                
                <div class="appForminputContainer">
                  <label for="img">Product Image</label>
                  <input type="file" class="appForminput" id="img" name="img" required />
                </div>

                <button type="submit" class="SubmitBtn">
                  <i class="fa-solid fa-plus"></i> Add Product
                </button>
              </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function() {
      $('#supplier').select2({
        theme: 'bootstrap-5',
        width: '100%'
      });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const priceInput = document.getElementById('price');
        const pointsInput = document.getElementById('points');

        priceInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
        });

        pointsInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
  </script>
</body>
</html>