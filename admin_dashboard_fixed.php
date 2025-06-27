<?php
    session_start();
    
    // Check if admin is logged in
    if (!isset($_SESSION['admins'])) {
        header("Location: admin_login.php");
        exit();
    }

    $is_viewer = ($_SESSION['admins']['role'] ?? '') === 'viewer';
    
    $db = mysqli_connect('localhost', 'root', '', 'educarehub');

    if (!$db) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // --- Fetch Recent Activities ---
    $recentActivities = [];
    // Helper function to format time ago
    function time_ago($timestamp) {
        if ($timestamp == 0) return 'some time ago';
        $time_ago = time() - $timestamp;
        if ($time_ago < 60) return 'Just now';
        $minutes = round($time_ago / 60);
        if ($minutes < 60) return $minutes . 'm ago';
        $hours = round($time_ago / 3600);
        if ($hours < 24) return $hours . 'h ago';
        $days = round($time_ago / 86400);
        return $days . 'd ago';
    }
    // 1. New reward orders (pending)
    $queryNewOrders = "SELECT u.firstname, u.lastname, p.product_name, ro.order_date FROM reward_orders ro JOIN users u ON ro.user_id = u.user_id JOIN products p ON ro.product_id = p.product_id WHERE ro.status = 'pending' ORDER BY ro.order_date DESC LIMIT 5";
    $resultNewOrders = mysqli_query($db, $queryNewOrders);
    if ($resultNewOrders) {
        while($row = mysqli_fetch_assoc($resultNewOrders)) {
            $fullname = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
            $recentActivities[] = [
                'type' => 'new_order',
                'icon' => 'fa-gift',
                'color' => 'var(--info)',
                'text' => 'New reward claim from <strong>' . $fullname . '</strong> for ' . htmlspecialchars($row['product_name']),
                'time' => strtotime($row['order_date'])
            ];
        }
    }
    // 2. Low stock alerts
    $queryLowStockActivity = "SELECT p.product_name, s.quantity, s.updated_at FROM stocks s JOIN products p ON s.product_id = p.product_id WHERE s.quantity <= 5 AND s.quantity > 0 ORDER BY s.updated_at DESC LIMIT 5";
    $resultLowStockActivity = mysqli_query($db, $queryLowStockActivity);
    if ($resultLowStockActivity) {
        while($row = mysqli_fetch_assoc($resultLowStockActivity)) {
            $recentActivities[] = [
                'type' => 'low_stock',
                'icon' => 'fa-exclamation-triangle',
                'color' => 'var(--warning)',
                'text' => '<strong>' . htmlspecialchars($row['product_name']) . '</strong> is running low on stock (' . $row['quantity'] . ' left)',
                'time' => strtotime($row['updated_at'])
            ];
        }
    }
    // 3. Out of stock alerts
    $queryOutOfStockActivity = "SELECT p.product_name, s.updated_at FROM stocks s JOIN products p ON s.product_id = p.product_id WHERE s.quantity = 0 ORDER BY s.updated_at DESC LIMIT 5";
    $resultOutOfStockActivity = mysqli_query($db, $queryOutOfStockActivity);
    if ($resultOutOfStockActivity) {
        while($row = mysqli_fetch_assoc($resultOutOfStockActivity)) {
            $recentActivities[] = [
                'type' => 'out_of_stock',
                'icon' => 'fa-box-open',
                'color' => 'var(--danger)',
                'text' => '<strong>' . htmlspecialchars($row['product_name']) . '</strong> is out of stock',
                'time' => strtotime($row['updated_at']) ?: 0
            ];
        }
    }

    // Sort all activities by time
    usort($recentActivities, function($a, $b) {
        return $b['time'] - $a['time'];
    });
    // Limit to top 10 recent activities
    $recentActivities = array_slice($recentActivities, 0, 10);

    // --- Fetch Recent Admin Logins ---
    $recentAdminLogins = [];
    $queryAdminLogins = "SELECT fullname, last_login FROM admins WHERE last_login IS NOT NULL ORDER BY last_login DESC LIMIT 3";
    $resultAdminLogins = mysqli_query($db, $queryAdminLogins);
    if ($resultAdminLogins) {
        while($row = mysqli_fetch_assoc($resultAdminLogins)) {
            $recentAdminLogins[] = $row;
        }
    }

    //Data for Pie Chart
    $queryStatus = "SELECT status, COUNT(*) as count FROM products_supplier GROUP BY status"; 
    $resultsStatus = mysqli_query($db, $queryStatus);

    $statusData = [
        'PENDING' => 0,
        'COMPLETED' => 0,
        'INCOMPLETE' => 0
    ];

    while ($row = mysqli_fetch_assoc($resultsStatus)) {
        $status = strtoupper($row['status']);
        if (array_key_exists($status, $statusData)) {
            $statusData[$status] = (int) $row['count'];
        }
    }

    // Data for Reward Order Status Pie Chart
    $queryRewardStatus = "SELECT status, COUNT(*) as count FROM reward_orders GROUP BY status";
    $resultsRewardStatus = mysqli_query($db, $queryRewardStatus);

    $rewardStatusData = [
        'pending' => 0,
        'on delivery' => 0,
        'declined' => 0,
        'delivered' => 0,
        'unsuccessful' => 0
    ];

    while ($row = mysqli_fetch_assoc($resultsRewardStatus)) {
        $status = strtolower($row['status']);
        if (array_key_exists($status, $rewardStatusData)) {
            $rewardStatusData[$status] = (int) $row['count'];
        }
    }


    //Data for Bar Chart

    // 1. Get all distinct supplier names from the supplier table
    $querySuppliers = "SELECT supplier_name FROM supplier ORDER BY supplier_name ASC";
    $resultsSuppliers = mysqli_query($db, $querySuppliers);

    $supplierNames = [];
    while ($row = mysqli_fetch_assoc($resultsSuppliers)) {
        $supplierNames[] = htmlspecialchars($row['supplier_name']);
    }

    // 2. Calculate total quantity received per supplier from products_supplier
    $queryQuantityReceived = "SELECT supplier_name, SUM(quantity_recieved) as total_received
                              FROM products_supplier
                              GROUP BY supplier_name
                              ORDER BY supplier_name ASC";
    $resultsQuantityReceived = mysqli_query($db, $queryQuantityReceived);

    $quantityReceivedData = [];
    while ($row = mysqli_fetch_assoc($resultsQuantityReceived)) {
        $quantityReceivedData[htmlspecialchars($row['supplier_name'])] = (int) $row['total_received'];
    }

    $barChartCategories = [];
    $barChartSeriesData = [];
    $maxQuantity = 0;

    foreach ($supplierNames as $name) {
        $barChartCategories[] = $name; // Add all supplier names as categories
        $received = $quantityReceivedData[$name] ?? 0;
        $barChartSeriesData[] = $received;
        if ($received > $maxQuantity) {
            $maxQuantity = $received;
        }
    }

    // Determine a sensible max for yAxis, e.g., next multiple of 5 or 10
    $yAxisMax = ceil($maxQuantity / 10) * 10;
    if ($yAxisMax == 0 && $maxQuantity == 0) {
        $yAxisMax = 10; // Default max if no data
    } elseif ($yAxisMax < $maxQuantity) {
        $yAxisMax += 10; // Ensure max is always above highest value
    }
    if ($yAxisMax == 0) $yAxisMax = 1; // Ensure a min of 1 if all quantities are 0

    // Data for Line Chart (Request Approve History)
    $lineChartLabels = [];
    $lineChartData = [];
    $dateData = [];

    // Initialize last 7 days
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $lineChartLabels[] = date('M d', strtotime($date));
        $dateData[$date] = 0;
    }
    
    // Query to get approved orders count
    $queryApprovals = "SELECT COUNT(*) as count, DATE(order_date) as day FROM reward_orders WHERE status IN ('on delivery', 'delivered') AND order_date >= CURDATE() - INTERVAL 6 DAY GROUP BY day ORDER BY day ASC";
    
    $resultApprovals = mysqli_query($db, $queryApprovals);
    
    if ($resultApprovals) {
        while ($row = mysqli_fetch_assoc($resultApprovals)) {
            if (isset($dateData[$row['day']])) {
                $dateData[$row['day']] = (int)$row['count'];
            }
        }
    }
    
    // Prepare the final data array for the chart
    $lineChartData = array_values($dateData);

    // Data for Product Popularity Chart
    $popularityLabels = [];
    $popularityData = [];
    $queryPopularity = "SELECT p.product_name, COUNT(ro.id) AS claim_count FROM reward_orders ro JOIN products p ON ro.product_id = p.product_id GROUP BY p.product_name ORDER BY claim_count DESC LIMIT 5";
    $resultPopularity = mysqli_query($db, $queryPopularity);
    if ($resultPopularity) {
        while ($row = mysqli_fetch_assoc($resultPopularity)) {
            $popularityLabels[] = $row['product_name'];
            $popularityData[] = (int)$row['claim_count'];
        }
    }

    // Data for Low Stock Alert Chart
    $lowStockLabels = [];
    $lowStockData = [];
    $queryLowStock = "SELECT p.product_name, s.quantity FROM stocks s JOIN products p ON s.product_id = p.product_id ORDER BY s.quantity ASC LIMIT 5";
    $resultLowStock = mysqli_query($db, $queryLowStock);
    if ($resultLowStock) {
        while ($row = mysqli_fetch_assoc($resultLowStock)) {
            $lowStockLabels[] = $row['product_name'];
            $lowStockData[] = (int)$row['quantity'];
        }
    }

    // Data for KPI Cards
    $kpi_pending_approvals = 0;
    $kpi_out_of_stock = 0;
    $kpi_total_users = 0;
    $kpi_total_products = 0;
    
    // Get Pending Approvals
    $result_pending = mysqli_query($db, "SELECT COUNT(*) as count FROM reward_orders WHERE status = 'pending'");
    if($result_pending) $kpi_pending_approvals = mysqli_fetch_assoc($result_pending)['count'];

    // Get Items Out of Stock
    $result_stock = mysqli_query($db, "SELECT COUNT(*) as count FROM stocks WHERE quantity = 0");
    if($result_stock) $kpi_out_of_stock = mysqli_fetch_assoc($result_stock)['count'];

    // Get Total Users
    $result_users = mysqli_query($db, "SELECT COUNT(*) as count FROM users");
    if($result_users) $kpi_total_users = mysqli_fetch_assoc($result_users)['count'];

    // Get Total Products
    $result_products = mysqli_query($db, "SELECT COUNT(*) as count FROM products");
    if($result_products) $kpi_total_products = mysqli_fetch_assoc($result_products)['count'];

    // Get Total Inventory Value (Points)
    $kpi_inventory_value = 0;
    $result_inventory_value = mysqli_query($db, "SELECT SUM(p.points * s.quantity) as total_value FROM products p JOIN stocks s ON p.product_id = s.product_id");
    if($result_inventory_value) {
        $value = mysqli_fetch_assoc($result_inventory_value)['total_value'];
        $kpi_inventory_value = $value ? $value : 0;
    }

    // Get Total Suppliers
    $kpi_total_suppliers = 0;
    $result_suppliers = mysqli_query($db, "SELECT COUNT(*) as count FROM supplier");
    if($result_suppliers) $kpi_total_suppliers = mysqli_fetch_assoc($result_suppliers)['count'];

    // Team & Roles data
    $teamAndRolesData = [];
    $queryRoles = "SELECT role, COUNT(*) as count FROM admins GROUP BY role";
    $resultRoles = mysqli_query($db, $queryRoles);
    while($row = mysqli_fetch_assoc($resultRoles)) {
        $teamAndRolesData[] = ['name' => ucfirst(str_replace('_', ' ', $row['role'])), 'y' => (int)$row['count']];
    }

    // Stock Level Distribution
    $stockLevelData = [
        'No Stocks' => 0,
        'Low Stock' => 0,
        'Normal' => 0,
        'High Stock' => 0
    ];
    $queryStockLevels = "
        SELECT 
            CASE
                WHEN quantity = 0 THEN 'No Stocks'
                WHEN quantity <= 5 THEN 'Low Stock'
                WHEN quantity >= 100 THEN 'High Stock'
                ELSE 'Normal'
            END as stock_level,
            COUNT(*) as count
        FROM stocks
        GROUP BY stock_level
    ";
    $resultStockLevels = mysqli_query($db, $queryStockLevels);
    while($row = mysqli_fetch_assoc($resultStockLevels)) {
        if (isset($stockLevelData[$row['stock_level']])) {
            $stockLevelData[$row['stock_level']] = (int)$row['count'];
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EducareHub Dashboard</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="css/style.css?v=12">
    
    <style>
        :root {
            --primary: #0F9E99;
            --primary-accent: #6366f1;
            --primary-bg: #f0fdfa;
            --secondary: #f9fafb;
            --card-bg: #ffffff;
            --text-heading: #1f2937;
            --text-body: #6b7280;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --font-sans: 'Inter', sans-serif;
        }
        body.dark-mode {
            --primary-bg: #1f2937;
            --secondary: #111827;
            --card-bg: #1f2937;
            --text-heading: #f9fafb;
            --text-body: #a0aec0;
            --border-color: #4a5568;
        }
        .dashboard-layout {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .dashboard-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-start;
        }
        .layout-column-main {
            flex: 3;
            min-width: 0; /* Allow shrinking below content size */
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .layout-column-sidebar {
            flex: 1;
            min-width: 0; /* Allow shrinking */
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .dashboard-widget {
            background-color: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            padding: 12px;
            display: flex;
            flex-direction: column;
        }
        .section-card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            padding: 16px;
            margin-bottom: 24px;
        }
        .section_header {
            font-size: 1rem;
            color: var(--text-heading);
            font-weight: 600;
            border-bottom: 2px solid var(--primary-accent);
            padding-bottom: 6px;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        .kpi-bar {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
        }
        .kpi-card {
            background: var(--card-bg);
            padding: 6px;
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        .kpi-card .icon {
            font-size: 1rem;
            padding: 7px;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
        }
        .kpi-card .info {
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .kpi-card .info h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-heading);
        }
        .kpi-card .info p {
            margin: 0;
            color: var(--text-body);
            font-size: 0.7rem;
            font-weight: 400;
        }
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 16px;
        }
        .chart-box {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            padding: 12px;
            display: flex;
            flex-direction: column;
        }
        .highcharts-figure {
            margin: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .highcharts-description {
            text-align: center;
            margin-top: 10px;
            font-style: italic;
            font-size: 0.85rem;
            color: #9ca3af;
        }
        @media (max-width: 900px) {
            .chart-grid { grid-template-columns: 1fr; }
            .kpi-bar { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .kpi-bar { grid-template-columns: 1fr; }
        }
        .dashboard-action-link {
            display: inline-block;
            margin-right: 18px;
            color: var(--primary-accent);
            font-weight: 500;
            text-decoration: none;
            background: transparent;
            padding: 4px 0;
            border-radius: 5px;
            transition: color 0.2s;
            font-size: 0.85rem;
        }
        .dashboard-action-link:hover {
            color: #5b21b6;
        }
        .highcharts-title, .chart-header {
            color: var(--text-heading) !important;
            font-weight: 600 !important;
            font-family: 'Inter', sans-serif;
        }

        .dashboard-top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .dashboard-top-nav .greeting h2 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-heading);
            margin: 0;
        }
        .dashboard-top-nav .greeting p {
            margin: 1px 0 0 0;
            color: var(--text-body);
            font-size: 0.75rem;
        }
        .dashboard-top-nav .user-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dashboard-top-nav .user-actions button,
        .dashboard-top-nav .user-actions select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--card-bg);
            color: var(--text-body);
            cursor: pointer;
            transition: background-color 0.2s, box-shadow 0.2s;
        }
        .dashboard-top-nav .user-actions button:hover,
        .dashboard-top-nav .user-actions select:hover {
            border-color: #a0aec0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .dashboard-top-nav #notificationBell,
        .dashboard-top-nav #darkModeToggle {
            font-size: 1rem;
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .dashboard-top-nav #notificationBell {
             color: var(--primary-accent);
        }
        .dashboard-top-nav #roleSwitcher {
            font-weight: 500;
            padding: 7px 28px 7px 10px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236c757d' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }
        .dashboard-top-nav #darkModeToggle {
             color: var(--text-body);
        }

        .grid-stack {
            margin-top: 16px;
        }
        .grid-stack-item > .grid-stack-item-content {
            overflow: hidden !important; /* Force no scrollbars */
            background-color: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
        }
        .grid-stack-item-content .section-card {
            border: none;
            box-shadow: none;
            margin: 0;
            padding: 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .grid-stack-item-content .section_header {
            cursor: move;
        }
        .chart-box {
            display: flex;
            flex-direction: column;
            flex-grow: 1; /* Make chart-box fill space */
        }
        .chart-box > div {
            flex-grow: 1; /* Make highcharts div fill space */
        }
        [gs-id="orders-rewards"] .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            height: 100%;
            gap: 16px;
        }
        .notification-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 350px;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            z-index: 1500;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .notification-header, .notification-footer {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
        }
        .notification-header h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-heading);
        }
        .notification-footer {
            border-top: 1px solid var(--border-color);
            border-bottom: none;
            text-align: center;
        }
        .notification-footer a {
            color: var(--primary-accent);
            font-weight: 500;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .notification-body {
            max-height: 350px;
            overflow-y: auto;
            flex-grow: 1;
        }
        .notification-body::-webkit-scrollbar {
            width: 6px;
        }
        .notification-body::-webkit-scrollbar-track {
            background: transparent;
        }
        .notification-body::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        body.dark-mode .notification-body::-webkit-scrollbar-thumb {
            background: #555;
        }
        .notification-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            text-decoration: none;
            color: inherit;
            transition: background-color 0.2s;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-item:hover {
            background-color: var(--primary-bg);
        }
        .notification-item .icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }
        .notification-item .text p {
            margin: 0;
            font-size: 0.875rem;
            color: var(--text-heading);
            line-height: 1.4;
        }
        .notification-item .text small {
            color: var(--text-body);
            font-size: 0.75rem;
        }
        .notification-item.empty {
            justify-content: center;
            text-align: center;
            padding: 24px;
        }
        .notification-item.empty p {
            color: var(--text-body);
        }
        .dashboard-top-nav .user-actions > div {
            position: relative;
        }
        .notification-subheader {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-body);
            text-transform: uppercase;
            padding: 12px 16px 4px 16px;
            margin: 0;
        }
        .notification-divider {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 8px 0;
        }
        .notification-tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            padding: 0 16px;
        }
        .notification-tab {
            background: none;
            border: none;
            padding: 12px 16px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-body);
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: color 0.2s, border-color 0.2s;
        }
        .notification-tab:hover {
            color: var(--text-heading);
        }
        .notification-tab.active {
            color: var(--primary-accent);
            border-bottom-color: var(--primary-accent);
        }
        #notificationTabsContent > div {
            display: none;
        }
        #notificationTabsContent > div.active {
            display: block;
        }
        .alert-urgent {
            display: flex;
            align-items: center;
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 12px;
        }
        body.dark-mode .alert-urgent {
            background-color: #450a0a;
            color: #fca5a5;
        }
        .alert-urgent i {
            margin-right: 8px;
        }
    </style>
</head>

<body class="body">
    <div class="dashboard_main_container">
        <?php include_once('partials/app_topNav.php'); ?>
        <?php include_once('partials/app_horizontal_nav.php'); ?>

        <div class="dashboard_content">
            <div class="dashboard_content_main">
                <div class="dashboard-top-nav">
                    <div class="greeting">
                        <h2>Good Morning, <?= htmlspecialchars($_SESSION['admins']['fullname']) ?>!</h2>
                        <p>Here's what's happening in your dashboard today.</p>
                    </div>
                </div>

                <div class="dashboard-layout">
                    <div class="dashboard-widget" style="padding: 0; border: none; box-shadow: none; background: transparent; border-bottom: 1px solid var(--border-color);">
                        <?php if (!$is_viewer): ?>
                        <div class="dashboard-actions" style="padding: 4px 0 12px 0;">
                            <a href="add_product.php" class="dashboard-action-link"><i class="fa fa-plus"></i>&nbsp;Add New Product</a>
                            <a href="add_suppliers.php" class="dashboard-action-link"><i class="fa fa-truck"></i>&nbsp;Add Supplier</a>
                            <a href="inventory.php#restock" class="dashboard-action-link"><i class="fa fa-sync"></i>&nbsp;Restock Now</a>
                            <a href="view_orders.php?filter=pending" class="dashboard-action-link"><i class="fa fa-list"></i>&nbsp;Review Pending Orders</a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="kpi-bar" style="margin: 0;">
                        <div class="kpi-card">
                            <div class="icon" style="background: #ef4444;"><i class="fa fa-box-open"></i></div>
                            <div class="info">
                                <h3><?= $kpi_out_of_stock ?></h3>
                                <p>Items Out of Stock</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="icon" style="background: #f97316;"><i class="fa fa-hourglass-half"></i></div>
                            <div class="info">
                                <h3><?= $kpi_pending_approvals ?></h3>
                                <p>Pending Approvals</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="icon" style="background: #22c55e;"><i class="fa fa-box"></i></div>
                            <div class="info">
                                <h3><?= $kpi_total_products ?></h3>
                                <p>Total Products</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="icon" style="background: #3b82f6;"><i class="fa fa-users"></i></div>
                            <div class="info">
                                <h3><?= $kpi_total_users ?></h3>
                                <p>Total Users</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="icon" style="background: #8b5cf6;"><i class="fa fa-truck-ramp-box"></i></div>
                            <div class="info">
                                <h3><?= $kpi_total_suppliers ?></h3>
                                <p>Total Suppliers</p>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="icon" style="background: #14b8a6;"><i class="fa fa-dollar-sign"></i></div>
                            <div class="info">
                                <h3>$<?= number_format($kpi_inventory_value) ?></h3>
                                <p>Total Stock Value</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid-stack">
                    <div class="grid-stack-item" gs-id="inventory-trend" gs-x="0" gs-y="0" gs-w="8" gs-h="4">
                        <div class="grid-stack-item-content">
                             <div class="section-card">
                                <h2 class="section_header" style="cursor: move;">Inventory Trend</h2>
                                <div class="chart-box">
                                    <figure class="highcharts-figure">
                                        <div id="inventoryTrendChart"></div>
                                        <p class="highcharts-description">
                                            This chart shows the monthly trend of stock received from suppliers.
                                        </p>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid-stack-item" gs-id="team-roles" gs-x="8" gs-y="0" gs-w="4" gs-h="4">
                        <div class="grid-stack-item-content">
                            <div class="section-card">
                                <h2 class="section_header" style="cursor: move;">Team & Roles</h2>
                                <div class="chart-box">
                                    <figure class="highcharts-figure">
                                        <div id="teamRolesChart"></div>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid-stack-item" gs-id="stock-distribution" gs-x="8" gs-y="4" gs-w="4" gs-h="4">
                        <div class="grid-stack-item-content">
                            <div class="section-card">
                                <h2 class="section_header" style="cursor: move;">Stock Level Distribution</h2>
                                <div class="chart-box">
                                    <figure class="highcharts-figure">
                                        <div id="stockLevelChart"></div>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid-stack-item" gs-id="orders-rewards" gs-x="0" gs-y="4" gs-w="8" gs-h="8">
                        <div class="grid-stack-item-content">
                             <div class="section-card">
                                <h2 class="section_header" style="cursor: move;">Order & Reward Activity</h2>
                                <div class="chart-grid">
                                    <div class="chart-box">
                                        <h3 class="chart-header" style="font-size: 0.9rem; text-align: center;">Purchase Order Status</h3>
                                        <figure class="highcharts-figure">
                                            <div id="orderStatusChart"></div>
                                        </figure>
                                    </div>
                                    <div class="chart-box">
                                        <h3 class="chart-header" style="font-size: 0.9rem; text-align: center;">Reward Order Status</h3>
                                        <figure class="highcharts-figure">
                                            <div id="rewardStatusChart"></div>
                                        </figure>
                                    </div>
                                    <div class="chart-box">
                                        <h3 class="chart-header" style="font-size: 0.9rem; text-align: center;">Request Approval History (Last 7 Days)</h3>
                                        <figure class="highcharts-figure">
                                            <div id="requestHistoryChart"></div>
                                        </figure>
                                    </div>
                                    <div class="chart-box">
                                        <h3 class="chart-header" style="font-size: 0.9rem; text-align:.center">Product Popularity (Top 5)</h3>
                                        <figure class="highcharts-figure">
                                            <div id="productPopularityChart"></div>
                                        </figure>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid-stack-item" gs-id="lowest-stock" gs-x="8" gs-y="8" gs-w="4" gs-h="4">
                        <div class="grid-stack-item-content">
                            <div class="section-card">
                                <h2 class="section_header" style="cursor: move;">Top 5 Lowest Stock Products</h2>
                                <div class="chart-box">
                                    <figure class="highcharts-figure">
                                        <div id="lowStockChart"></div>
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid-stack-item" gs-id="recent-activity" gs-x="0" gs-y="12" gs-w="6" gs-h="4">
                        <div class="grid-stack-item-content">
                            <div class="section-card">
                                <h2 class="section_header" style="cursor: move;">Recent Activity</h2>
                                <div class="notification-body" style="max-height: none; overflow-y: auto;">
                                    <?php if (empty($recentActivities)): ?>
                                        <div class="notification-item empty">
                                            <p>No recent activities to show.</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($recentActivities as $activity): ?>
                                            <a href="#" class="notification-item">
                                                <div class="icon" style="background-color: <?= $activity['color'] ?>;">
                                                    <i class="fas <?= $activity['icon'] ?>"></i>
                                                </div>
                                                <div class="text">
                                                    <p><?= $activity['text'] ?></p>
                                                    <small><?= time_ago($activity['time']) ?></small>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid-stack-item" gs-id="admin-logins" gs-x="6" gs-y="12" gs-w="6" gs-h="4">
                        <div class="grid-stack-item-content">
                            <div class="section-card">
                                <h2 class="section_header" style="cursor: move;">Recent Admin Logins</h2>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($recentAdminLogins as $login): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center" style="background: transparent; border-color: var(--border-color); color: var(--text-heading);">
                                        <?= htmlspecialchars($login['fullname']) ?>
                                        <small style="color: var(--text-body);"><?= time_ago(strtotime($login['last_login'])) ?></small>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="theme-switcher">
        <label for="theme-select">Theme:</label>
        <select id="theme-select">
            <option value="light">Light</option>
            <option value="dark">Dark</option>
            <option value="corporate">Corporate</option>
            <option value="retro">Retro</option>
        </select>
        <p style="font-size: 0.8em; color: #888;">(Drag-and-drop coming soon)</p>
    </div>

    <!-- Help & Support Button -->
    <button class="help-fab" onclick="showHelpModal()">
        <i class="fas fa-question"></i>
    </button>

    <!-- Help Modal -->
    <div id="helpModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dashboard Help & Support</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Welcome to the EducareHub Admin Dashboard. Here's a quick guide to understanding the available widgets:</p>
                    <ul>
                        <li><strong>Inventory Trend:</strong> Visualizes the trend of received stock from suppliers over the last 12 months.</li>
                        <li><strong>Team & Roles:</strong> A pie chart showing the distribution of staff roles within the system.</li>
                        <li><strong>Stock Level Distribution:</strong> Shows the current status of product stock levels (e.g., No Stock, Low, Normal, High).</li>
                        <li><strong>Order & Reward Activity:</strong> Contains four mini-charts:
                            <ul>
                                <li><strong>Purchase Order Status:</strong> Tracks the status of orders from suppliers.</li>
                                <li><strong>Reward Order Status:</strong> Tracks the status of rewards claimed by users.</li>
                                <li><strong>Request Approval History:</strong> Shows the number of approved reward requests over the last 7 days.</li>
                                <li><strong>Product Popularity:</strong> Displays the top 5 most claimed reward products.</li>
                            </ul>
                        </li>
                        <li><strong>Top 5 Lowest Stock Products:</strong> A bar chart highlighting the products that are running lowest in stock.</li>
                        <li><strong>Recent Activity:</strong> A live feed of important events, such as new reward claims and stock alerts.</li>
                        <li><strong>Recent Admin Logins:</strong> Shows the most recent login times for administrators.</li>
                    </ul>
                    <p><strong>Customization:</strong> Soon, you will be able to drag, drop, and resize these widgets to create a personalized dashboard layout.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- GridStack -->
    <script src="https://cdn.jsdelivr.net/npm/gridstack@9.0.0/dist/gridstack-all.js"></script>

    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
    function showHelpModal() {
        var helpModal = new bootstrap.Modal(document.getElementById('helpModal'));
        helpModal.show();
    }

    // --- GRIDSTACK LOGIC ---
    document.addEventListener('DOMContentLoaded', function () {
        const grid = GridStack.init({
            cellHeight: 80,
            margin: 12,
            disableResize: false, // Allow resizing
            disableDrag: false, // Allow dragging
            handle: '.section_header' // Use header to drag
        });

        // Example of saving layout
        // grid.on('change', function(event, items) {
        //     const serializedData = grid.save();
        //     localStorage.setItem('dashboard-layout', JSON.stringify(serializedData));
        // });

        // Example of loading layout
        // const savedLayout = localStorage.getItem('dashboard-layout');
        // if (savedLayout) {
        //     grid.load(JSON.parse(savedLayout));
        // }


        // --- HIGHCHARTS ---
        const highchartsOptions = {
            chart: {
                backgroundColor: 'transparent',
                style: {
                    fontFamily: 'Inter, sans-serif'
                }
            },
            title: {
                style: {
                    color: 'var(--text-heading)',
                    fontWeight: '600'
                }
            },
            subtitle: {
                style: {
                    color: 'var(--text-body)'
                }
            },
            credits: {
                enabled: false
            },
            legend: {
                itemStyle: {
                    color: 'var(--text-body)'
                },
                itemHoverStyle: {
                    color: 'var(--text-heading)'
                }
            },
            xAxis: {
                labels: {
                    style: {
                        color: 'var(--text-body)'
                    }
                },
                lineColor: 'var(--border-color)',
                tickColor: 'var(--border-color)',
                title: {
                    style: {
                        color: 'var(--text-heading)'
                    }
                }
            },
            yAxis: {
                labels: {
                    style: {
                        color: 'var(--text-body)'
                    }
                },
                gridLineColor: 'var(--border-color)',
                title: {
                    style: {
                        color: 'var(--text-heading)'
                    }
                }
            },
            plotOptions: {
                series: {
                    dataLabels: {
                        color: 'var(--text-heading)'
                    }
                }
            }
        };

        Highcharts.setOptions(highchartsOptions);

        // Inventory Trend Chart
        Highcharts.chart('inventoryTrendChart', {
            chart: {
                type: 'areaspline'
            },
            title: {
                text: 'Inventory Trend (Last 12 Months)',
                align: 'left'
            },
            xAxis: {
                categories: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            },
            yAxis: {
                title: {
                    text: 'Quantity Received'
                }
            },
            series: [{
                name: 'Stock Received',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 800, 1200, 2100],
                color: 'var(--primary-accent)',
                fillOpacity: 0.1
            }]
        });

        // Team & Roles Chart
        Highcharts.chart('teamRolesChart', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Team & Roles',
                align: 'left'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y}</b> ({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y}',
                        distance: -50,
                        filter: {
                            property: 'percentage',
                            operator: '>',
                            value: 4
                        }
                    },
                    showInLegend: false
                }
            },
            series: [{
                name: 'Admins',
                data: <?= json_encode($teamAndRolesData) ?>
            }]
        });

        // Stock Level Chart
        Highcharts.chart('stockLevelChart', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Stock Level Distribution',
                align: 'left'
            },
            series: [{
                name: 'Products',
                colorByPoint: true,
                data: [
                    { name: 'No Stocks', y: <?= $stockLevelData['No Stocks'] ?>, color: '#1f2937' },
                    { name: 'Low Stock', y: <?= $stockLevelData['Low Stock'] ?>, color: '#ef4444' },
                    { name: 'Normal', y: <?= $stockLevelData['Normal'] ?>, color: '#3b82f6' },
                    { name: 'High Stock', y: <?= $stockLevelData['High Stock'] ?>, color: '#22c55e' }
                ]
            }]
        });

        // Order Status Chart
        Highcharts.chart('orderStatusChart', {
            chart: {
                type: 'pie',
                height: '80%'
            },
            title: { text: null },
            plotOptions: {
                pie: {
                    innerSize: '60%',
                    dataLabels: { enabled: false },
                    showInLegend: true,
                }
            },
            series: [{
                name: 'Orders',
                data: [
                    { name: 'Pending', y: <?= $statusData['PENDING'] ?>, color: '#f97316' },
                    { name: 'Completed', y: <?= $statusData['COMPLETED'] ?>, color: '#22c55e' },
                    { name: 'Incomplete', y: <?= $statusData['INCOMPLETE'] ?>, color: '#ef4444' }
                ]
            }]
        });

        // Reward Status Chart
        Highcharts.chart('rewardStatusChart', {
            chart: {
                type: 'pie',
                height: '80%'
            },
            title: { text: null },
            plotOptions: {
                pie: {
                    innerSize: '60%',
                    dataLabels: { enabled: false },
                    showInLegend: true,
                }
            },
            series: [{
                name: 'Rewards',
                data: [
                    { name: 'Pending', y: <?= $rewardStatusData['pending'] ?>, color: '#f59e0b' },
                    { name: 'On Delivery', y: <?= $rewardStatusData['on delivery'] ?>, color: '#3b82f6' },
                    { name: 'Unsuccessful', y: <?= $rewardStatusData['unsuccessful'] ?>, color: '#ef4444' },
                    { name: 'Delivered', y: <?= $rewardStatusData['delivered'] ?>, color: '#16a34a' }
                ]
            }]
        });

        // Request History Chart
        Highcharts.chart('requestHistoryChart', {
            chart: {
                type: 'column',
                height: '80%'
            },
            title: { text: null },
            xAxis: {
                categories: <?= json_encode($lineChartLabels) ?>,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: { text: null }
            },
            legend: { enabled: false },
            series: [{
                name: 'Approved Rewards',
                data: <?= json_encode($lineChartData) ?>,
                color: 'var(--primary-accent)'
            }]
        });

        // Product Popularity Chart
        Highcharts.chart('productPopularityChart', {
            chart: {
                type: 'bar',
                height: '80%'
            },
            title: { text: null },
            xAxis: {
                categories: <?= json_encode($popularityLabels) ?>,
            },
            yAxis: {
                min: 0,
                title: { text: null }
            },
            legend: { enabled: false },
            series: [{
                name: 'Claims',
                data: <?= json_encode($popularityData) ?>,
                color: '#10b981'
            }]
        });

        // Low Stock Chart
        Highcharts.chart('lowStockChart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Top 5 Lowest Stock Products',
                align: 'left'
            },
            xAxis: {
                categories: <?= json_encode($lowStockLabels) ?>
            },
            yAxis: {
                min: 0,
                title: { text: 'Quantity' }
            },
            legend: { enabled: false },
            series: [{
                name: 'Stock Quantity',
                data: <?= json_encode($lowStockData) ?>,
                color: '#f43f5e'
            }]
        });

        // --- NOTIFICATION DROPDOWN LOGIC ---
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.createElement('div');
        notificationDropdown.className = 'notification-dropdown';
        notificationDropdown.style.display = 'none';
        notificationDropdown.innerHTML = `
            <div class="notification-header">
                <h4>Notifications</h4>
            </div>
            <div class="notification-body">
                <div class="notification-item empty">
                    <p>No new notifications.</p>
                </div>
            </div>
            <div class="notification-footer">
                <a href="#">View all notifications</a>
            </div>
        `;
        notificationBell.parentElement.appendChild(notificationDropdown);

        notificationBell.addEventListener('click', function(event) {
            event.stopPropagation();
            const isVisible = notificationDropdown.style.display === 'block';
            notificationDropdown.style.display = isVisible ? 'none' : 'block';
        });

        document.addEventListener('click', function() {
            notificationDropdown.style.display = 'none';
        });

        notificationDropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });

        // --- THEME SWITCHER LOGIC ---
        const themeSelect = document.getElementById('theme-select');
        const body = document.body;

        const savedTheme = localStorage.getItem('theme') || 'light';
        body.className = 'body ' + savedTheme + '-mode';
        themeSelect.value = savedTheme;

        themeSelect.addEventListener('change', function() {
            const selectedTheme = this.value;
            body.className = 'body ' + selectedTheme + '-mode';
            localStorage.setItem('theme', selectedTheme);
        });
    });
    </script>
</body>
</html> 