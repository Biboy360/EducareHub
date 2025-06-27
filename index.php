<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Function to check admin role permissions
function has_permission($required_role) {
    if (!isset($_SESSION['admins']['role'])) {
        return false;
    }
    
    $user_role = $_SESSION['admins']['role'];
    
    // Super admin has access to everything
    if ($user_role === 'super_admin') {
        return true;
    }
    
    // Role-based permissions based on requirements
    switch ($required_role) {
        case 'admin':
            return in_array($user_role, ['admin', 'super_admin']);
        case 'stock_encoder':
            return in_array($user_role, ['stock_encoder', 'admin', 'super_admin']);
        case 'purchasing_officer':
            return in_array($user_role, ['purchasing_officer', 'admin', 'super_admin']);
        case 'rewards_manager':
            return in_array($user_role, ['rewards_manager', 'admin', 'super_admin']);
        case 'viewer':
            return in_array($user_role, ['viewer', 'stock_encoder', 'purchasing_officer', 'rewards_manager', 'admin', 'super_admin']);
        case 'inventory_only':
            return in_array($user_role, ['stock_encoder', 'admin', 'super_admin']);
        case 'orders_only':
            return in_array($user_role, ['purchasing_officer', 'admin', 'super_admin']);
        case 'rewards_only':
            return in_array($user_role, ['rewards_manager', 'admin', 'super_admin']);
        case 'user_management':
            return in_array($user_role, ['admin', 'super_admin']);
        default:
            return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EducareHub</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">

  <!-- Icons & Fonts -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body class="pt-3 d-flex flex-row" style="font-family: 'Poppins', sans-serif;">

  <!-- Sidebar -->
  <nav class="d-flex flex-column justify-content-between h-100" style="width: 250px; margin-left: 20px;">
    <?php include('panels/menu-bar.php'); ?>
  </nav>

  <!-- Pomodoro Settings Toggle -->
  <?php include('menus/toggles/pomodoro-settings.php'); ?>

  <!-- Main Area -->
  <div class="herotab w-100">

    <!-- Top Bar -->
    <?php include('panels/uppertab.php'); ?>

    <!-- Main Content and Side Panel -->
    <div class="lowertab d-flex flex-row justify-content-between" style="margin:25px;height: 85vh;">

      <!-- Main Content -->
      <div class="main-content" style="width: 750px; max-width: 750px;">
        <?php
          $page = $_GET['page'] ?? 'dashboard';
          $allowed_pages = ['dashboard', 'rewards', 'task_manager', 'account_setting', 'peer_forum'];

          // Role-based access control for content panels
          $page_permissions = [
              'dashboard' => 'viewer', // Everyone can see dashboard
              'rewards' => 'rewards_only', // Only rewards managers and above
              'task_manager' => 'admin', // Only admins and above
              'account_setting' => 'admin', // Only admins and above
              'peer_forum' => 'admin' // Only admins and above
          ];

          if (in_array($page, $allowed_pages)) {
              // Check if user has permission for this page
              if (isset($page_permissions[$page]) && !has_permission($page_permissions[$page])) {
                  echo "<div class='alert alert-danger'>Access denied. You do not have permission to view this page.</div>";
              } else {
                  include("contents_panels/{$page}.php");
              }
          } else {
              echo "<div class='alert alert-danger'>Page not found.</div>";
          }
        ?>
      </div>

      <!-- Side Panel (Pomodoro) -->
      <div class="pomodoro-notifs d-flex justify-content-end flex-column">
        <div class="d-flex flex-column align-items-end mb-2">
          <?php include('panels/side-bar-pomodoro.php'); ?>
        </div>
      </div>

    </div>
  </div>

  <!-- JS Dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  <script src="script.js"></script>
</body>
</html>

