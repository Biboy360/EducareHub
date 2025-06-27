    <link rel="stylesheet" href="css/style.css?v=4">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.bootstrap5.min.css" />

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.js"></script> 
    
    <script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// Function to check if a menu item should be marked as active
if (!function_exists('is_menu_active')) {
    function is_menu_active($pages) {
        $current_page = basename($_SERVER['PHP_SELF']);
        if (is_array($pages)) {
            return in_array($current_page, $pages) ? 'active' : '';
        } else {
            return $current_page === $pages ? 'active' : '';
        }
    }
}

// Function to check admin role permissions
if (!function_exists('has_permission')) {
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
}
?>

<style>
    /* Horizontal Navigation Bar Styles */
    .horizontal-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        padding: 0 20px;
        border-bottom: 1px solid #e0e0e0;
        list-style: none;
        margin: 0;
        height: 50px;
        width: 100%;
        box-sizing: border-box;
    }
    
    .nav-left {
        display: flex;
        align-items: center;
    }
    
    .nav-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .user-info {
        text-align: right;
        margin-right: 10px;
    }
    
    .user-name {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    .user-role {
        font-size: 12px;
        color: #666;
        text-transform: capitalize;
        margin: 0;
    }
    
    .logout-btn {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .logout-btn:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }
    
    .horizontal-nav-item {
        position: relative;
    }
    .horizontal-nav-item > a {
        display: flex;
        align-items: center;
        padding: 0 15px;
        height: 50px;
        color: #333;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s, color 0.3s;
    }
    .horizontal-nav-item > a:hover,
    .horizontal-nav-item > a.active {
        background-color: #f0f0f0;
        color: #0F9E99;
    }
    .horizontal-nav-item > a i {
        margin-right: 8px;
    }
    .horizontal-nav-item .sub-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-top: none;
        list-style: none;
        margin: 0;
        padding: 0;
        min-width: 200px;
        z-index: 1000;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .horizontal-nav-item .sub-menu.open {
        display: block;
    }
    .horizontal-nav-item .sub-menu li a {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        transition: background-color 0.3s;
    }
    .horizontal-nav-item .sub-menu li a:hover {
        background-color: #f0f0f0;
    }
</style>
<nav class="horizontal-nav">
    <div class="nav-left">
        <li class="horizontal-nav-item">
            <a href="admin_dashboard.php" class="<?php echo is_menu_active('admin_dashboard.php'); ?>">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
        </li>
        <?php if (has_permission('inventory_only')): ?>
        <li class="horizontal-nav-item">
            <a href="#" class="<?php echo is_menu_active(['add_product.php', 'view_product.php']); ?>">
                <i class="fa-solid fa-box"></i> Products <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
            </a>
            <ul class="sub-menu">
                <li><a href="add_product.php"><i class="fa-solid fa-plus"></i> Add Product</a></li>
                <li><a href="view_product.php"><i class="fa-solid fa-eye"></i> View Products</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (has_permission('orders_only')): ?>
        <li class="horizontal-nav-item">
            <a href="#" class="<?php echo is_menu_active(['add_orders.php', 'view_orders.php']); ?>">
                <i class="fa-solid fa-cart-shopping"></i> Purchase Order <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
            </a>
            <ul class="sub-menu">
                <li><a href="add_orders.php"><i class="fa-solid fa-plus"></i> Create Order</a></li>
                <li><a href="view_orders.php"><i class="fa-solid fa-eye"></i> View Orders</a></li>
            </ul>
        </li>
        <li class="horizontal-nav-item">
            <a href="#" class="<?php echo is_menu_active(['add_suppliers.php', 'view_suppliers.php']); ?>">
                <i class="fa-solid fa-truck"></i> Suppliers <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
            </a>
            <ul class="sub-menu">
                <li><a href="add_suppliers.php"><i class="fa-solid fa-plus"></i> Add Supplier</a></li>
                <li><a href="view_suppliers.php"><i class="fa-solid fa-eye"></i> View Suppliers</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (has_permission('inventory_only')): ?>
        <li class="horizontal-nav-item">
            <a href="inventory.php" class="<?php echo is_menu_active('inventory.php'); ?>">
                <i class="fa-solid fa-warehouse"></i> Inventory
            </a>
        </li>
        <?php endif; ?>
        <?php if (has_permission('rewards_only')): ?>
         <li class="horizontal-nav-item">
            <a href="request_approval.php" class="<?php echo is_menu_active('request_approval.php'); ?>">
                <i class="fa-solid fa-check-to-slot"></i> Rewards Approval
            </a>
        </li>
        <?php endif; ?>
        <?php if (has_permission('user_management')): ?>
        <li class="horizontal-nav-item">
            <a href="#" class="<?php echo is_menu_active(['add_users.php', 'add_admins.php']); ?>">
                <i class="fa-solid fa-users"></i> Users <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
            </a>
            <ul class="sub-menu">
                <?php if (has_permission('super_admin')): ?>
                <li><a href="add_admins.php"><i class="fa-solid fa-user-shield"></i> View Admins</a></li>
                <?php endif; ?>
                <li><a href="add_users.php"><i class="fa-solid fa-user"></i> View Users</a></li>
            </ul>
        </li>
        <?php endif; ?>
    </div>
    
    <!-- User Info and Logout Section -->
    <div class="nav-right">
        <div class="user-info">
            <div class="user-name"><?php echo htmlspecialchars($_SESSION['admins']['fullname'] ?? 'Admin'); ?></div>
            <div class="user-role"><?php echo htmlspecialchars($_SESSION['admins']['role'] ?? 'User'); ?></div>
        </div>
        <button onclick="confirmLogout()" class="logout-btn">
            <i class="fa fa-sign-out-alt"></i>
            Logout
        </button>
    </div>
</nav>

<script>
$(document).ready(function(){
    // Use event delegation from the parent nav for robustness
    $('.horizontal-nav').on('click', '.horizontal-nav-item > a', function(e) {
        var $this = $(this);
        var $subMenu = $this.next('.sub-menu');
        if ($subMenu.length === 0) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        var wasOpen = $subMenu.hasClass('open');
        $('.horizontal-nav .sub-menu.open').removeClass('open');
        if (!wasOpen) {
            $subMenu.addClass('open');
        }
    });
    $(document).on('click', function(e) {
        if ($(e.target).closest('.horizontal-nav').length === 0) {
            $('.horizontal-nav .sub-menu.open').removeClass('open');
        }
    });
});

function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}
</script> 