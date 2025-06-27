<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has purchasing_officer role or higher (purchase management)
$allowed_roles = ['purchasing_officer', 'admin', 'super_admin'];
if (!in_array($_SESSION['admins']['role'], $allowed_roles)) {
    $_SESSION['error_message'] = "Access denied. Only Purchasing Officers and above can manage purchase orders.";
    header("Location: admin_dashboard.php");
    exit();
}

$is_viewer = ($_SESSION['admins']['role'] ?? '') === 'viewer';

include('insert_orders.php'); ?>
<?php $old = $_POST ?? []; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="css/style.css?v=1">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body class="body">
  <div class="dashboard_main_container">
    <?php include('partials/app_topNav.php'); ?>
    <?php include('partials/app_horizontal_nav.php'); ?>
    <div class="dashboard_content">
      <div class="dashboard_content_main">
        <div class="row">
          <div class="column-12">
            <div class="d-flex justify-content-between align-items-center">
              <h2 class="section_header"><i class="fa fa-plus"></i> CREATE ORDER</h2>
              <a href="view_orders.php" class="btn btn-secondary"><i class="fa fa-eye"></i> View Orders</a>
            </div>
            <div class="section_content">
              <div class="po_lists">
                <div class="po_list_header">

                  <?php if (!empty($errors)): ?>
                    <ul style="color:red;">
                      <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php elseif (isset($_SESSION['success'])): ?>
                    <p style="color:green;"><?= $_SESSION['success'] ?></p>
                    <?php unset($_SESSION['success']); ?>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['error'])): ?>
                    <p style="color:red;"><?= $_SESSION['error'] ?></p>
                    <?php unset($_SESSION['error']); ?>
                  <?php endif; ?>

                  <?php if (!$is_viewer): ?>
                    <form method="POST" action="add_orders.php" class="appForm">
                      <div id="orders-container">
                        <div class="order-group" style="display: flex; flex-wrap: wrap; gap: 16px; background: #f5fafd; border-radius: 8px; padding: 16px 12px; margin-bottom: 18px; align-items: flex-end;">
                          <div style="flex: 1 1 220px; min-width: 180px;">
                            <label>Product:</label>
                            <select name="product_id[]" onchange="setSupplierAndFields(this)" required style="width: 100%">
                              <option value="">-- Select Product --</option>
                              <?php foreach ($productOptions as $item): ?>
                                <option 
                                  value="<?= $item['product_id'] ?>" 
                                  data-supplier-name="<?= htmlspecialchars($item['supplier_name']) ?>"
                                  data-category="<?= htmlspecialchars($item['category'] ?? '') ?>"
                                  data-sku="<?= htmlspecialchars($item['sku'] ?? '') ?>"
                                  data-points="<?= htmlspecialchars($item['points'] ?? '') ?>"
                                >
                                  <?= htmlspecialchars($item['product_name']) ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                          <div style="flex: 1 1 180px; min-width: 140px;">
                            <label>Supplier:</label>
                            <input type="text" name="supplier_name[]" class="supplier-input" readonly required style="width: 100%">
                          </div>
                          <div style="flex: 1 1 120px; min-width: 100px;">
                            <label>Category:</label>
                            <input type="text" name="category[]" class="category-input" readonly required style="width: 100%">
                          </div>
                          <div style="flex: 1 1 120px; min-width: 100px;">
                            <label>SKU:</label>
                            <input type="text" name="sku[]" class="sku-input" readonly required style="width: 100%">
                          </div>
                          <div style="flex: 1 1 100px; min-width: 80px;">
                            <label>Price:</label>
                            <input type="number" name="points[]" class="points-input" readonly required style="width: 100%">
                          </div>
                          <div style="flex: 1 1 120px; min-width: 100px;">
                            <label>Quantity Ordered:</label>
                            <input type="number" name="quantity_ordered[]" min="1" required style="width: 100%">
                          </div>
                          <div style="flex: 0 0 auto; align-self: center;">
                            <button type="button" onclick="removeOrder(this)" style="background: #fff; color: #e74c3c; border: 1px solid #e74c3c; border-radius: 6px; padding: 6px 14px; margin-left: 8px; cursor: pointer; font-size: 15px;">Remove</button>
                          </div>
                        </div>
                      </div>

                      <button type="button" onclick="addOrder()">+ Add Another Order</button>
                      <br><br>

                      <button type="submit" class="SubmitBtn">
                        <i class="fa-solid fa-plus"></i> Submit All Orders
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="js/script.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    function setSupplierAndFields(select) {
      const selectedOption = select.options[select.selectedIndex];
      const supplierName = selectedOption.getAttribute('data-supplier-name') || '';
      const category = selectedOption.getAttribute('data-category') || '';
      const sku = selectedOption.getAttribute('data-sku') || '';
      const points = selectedOption.getAttribute('data-points') || '';
      const group = select.closest('.order-group');
      group.querySelector('.supplier-input').value = supplierName;
      group.querySelector('.category-input').value = category;
      group.querySelector('.sku-input').value = sku;
      group.querySelector('.points-input').value = points;
    }

    function addOrder() {
      const container = document.getElementById('orders-container');
      const original = container.querySelector('.order-group');
      const clone = original.cloneNode(true);

      // Find the select element in the clone and remove the old Select2 container
      const selectInClone = clone.querySelector("select[name='product_id[]']");
      $(selectInClone).next(".select2-container").remove();

      // Reset values
      selectInClone.selectedIndex = 0;
      clone.querySelector('.supplier-input').value = '';
      clone.querySelector('.category-input').value = '';
      clone.querySelector('.sku-input').value = '';
      clone.querySelector('.points-input').value = '';
      clone.querySelector('input[name="quantity_ordered[]"]').value = '';

      container.appendChild(clone);

      // Initialize Select2 ONLY on the new select element
      $(selectInClone).select2({
        width: '100%',
        placeholder: '-- Select Product --',
        allowClear: true
      });
    }

    function removeOrder(button) {
      const container = document.getElementById('orders-container');
      if (container.children.length > 1) {
        button.closest('.order-group').remove();
      }
    }

    // Form validation before submission
    function validateForm() {
      const orderGroups = document.querySelectorAll('.order-group');
      let isValid = true;
      
      console.log('Validating form with', orderGroups.length, 'order groups');
      
      orderGroups.forEach((group, index) => {
        const productSelect = group.querySelector('select[name="product_id[]"]');
        const supplierInput = group.querySelector('.supplier-input');
        const quantityInput = group.querySelector('input[name="quantity_ordered[]"]');
        
        console.log(`Order ${index + 1}:`, {
          product: productSelect.value,
          supplier: supplierInput.value,
          quantity: quantityInput.value
        });
        
        if (!productSelect.value) {
          alert(`Please select a product for order #${index + 1}`);
          isValid = false;
          return false;
        }
        
        if (!supplierInput.value) {
          alert(`Supplier is required for order #${index + 1}`);
          isValid = false;
          return false;
        }
        
        if (!quantityInput.value || quantityInput.value <= 0) {
          alert(`Please enter a valid quantity for order #${index + 1}`);
          isValid = false;
          return false;
        }
      });
      
      console.log('Form validation result:', isValid);
      return isValid;
    }

    $(document).ready(function() {
      // Initialize Select2 on the initial select element(s)
      $("select[name='product_id[]']").select2({
        width: '100%',
        placeholder: '-- Select Product --',
        allowClear: true
      });
      
      // Add form validation on submit
      $('form.appForm').on('submit', function(e) {
        console.log('Form submission attempted');
        if (!validateForm()) {
          console.log('Form validation failed, preventing submission');
          e.preventDefault();
          return false;
        }
        console.log('Form validation passed, allowing submission');
      });
    });
  </script>
</body>
</html>
