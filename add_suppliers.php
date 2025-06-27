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
    $_SESSION['error_message'] = "Access denied. Only Purchasing Officers and above can manage suppliers.";
    header("Location: admin_dashboard.php");
    exit();
}

$is_viewer = ($_SESSION['admins']['role'] ?? '') === 'viewer';

include('insert_suppliers.php'); ?>
<?php $old = $_POST ?? []; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>admin_dashboard</title>
  <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="css/style.css?v=4">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
              <h1 class="section_header" style="margin-bottom: 0;"><i class="fa fa-plus"></i> Add New Supplier</h1>
              <a href="view_suppliers.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> View All Suppliers</a>
            </div>
            <div class="section_content">
              <?php if (!$is_viewer): ?>
                <form action="add_suppliers.php" method="POST" class="appForm" novalidate>
                  
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
                        <label for="supplier_name">Supplier Name</label>
                        <input type="text" class="appForminput" id="supplier_name" name="supplier_name" placeholder="Enter supplier name..." required
                               value="<?php echo htmlspecialchars($old['supplier_name'] ?? '') ?>" />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="appForminputContainer">
                        <label for="supplier_location">Supplier Location</label>
                        <input type="text" class="appForminput" id="supplier_location" name="supplier_location" placeholder="Enter supplier location..." required
                               value="<?php echo htmlspecialchars($old['supplier_location'] ?? '') ?>" />
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="appForminputContainer">
                        <label for="email">Email</label>
                        <input type="email" class="appForminput" id="email" name="email" placeholder="Enter supplier email..." required
                               value="<?php echo htmlspecialchars($old['email'] ?? '') ?>" />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="appForminputContainer">
                        <label for="contact_no">Contact No</label>
                        <input type="tel" class="appForminput" id="contact_no" name="contact_no" placeholder="Enter contact number..." value="<?php echo htmlspecialchars($old['contact_no'] ?? '') ?>" required />
                      </div>
                    </div>
                  </div>

                  <button type="submit" class="SubmitBtn" style="margin-top: 20px;">
                    <i class="fa-solid fa-plus"></i> Add Supplier
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form.appForm');
        const emailInput = document.getElementById('email');
        const contactInput = document.getElementById('contact_no');

        // This listener runs BEFORE the generic one in script.js
        form.addEventListener('submit', function(e) {
            // 1. Validate Email
            const emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if (!emailRegex.test(String(emailInput.value).toLowerCase())) {
                alert('Please enter a valid email address.');
                emailInput.focus();
                e.preventDefault(); // Stop submission
                e.stopImmediatePropagation(); // IMPORTANT: Stop other submit listeners (like in script.js)
                return;
            }

            // 2. Validate Contact Number
            if (contactInput.value.trim() !== '' && !/^\d+$/.test(contactInput.value)) {
                 alert('Contact number must only contain digits.');
                 contactInput.focus();
                 e.preventDefault(); // Stop submission
                 e.stopImmediatePropagation(); // IMPORTANT: Stop other submit listeners
                 return;
            }
        }, true); // Use capturing phase to ensure this runs first

        // Real-time numeric-only validation for contact number
        contactInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
  </script>
  <script src="js/script.js"></script>
</body>
</html>
