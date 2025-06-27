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
        $row = mysqli_fetch_assoc($result_inventory_value);
        $kpi_inventory_value = $row['total_value'] ?? 0;
    }

    // Calculate low, normal, high stock counts for dashboard chart
    $low_threshold = 5;
    $high_threshold = 100;
    $no_stock_count = 0;
    $low_stock_count = 0;
    $normal_stock_count = 0;
    $high_stock_count = 0;
    $stockCountQuery = "SELECT quantity FROM stocks";
    $stockCountResult = mysqli_query($db, $stockCountQuery);
    while ($row = mysqli_fetch_assoc($stockCountResult)) {
        $qty = (int)$row['quantity'];
        if ($qty == 0) {
            $no_stock_count++;
        } elseif ($qty <= $low_threshold) {
            $low_stock_count++;
        } elseif ($qty >= $high_threshold) {
            $high_stock_count++;
        } else {
            $normal_stock_count++;
        }
    }

    // --- Add supplier count and admin role distribution queries ---
    $result_suppliers = mysqli_query($db, "SELECT COUNT(*) as count FROM supplier");
    $kpi_total_suppliers = $result_suppliers ? mysqli_fetch_assoc($result_suppliers)['count'] : 0;

    $roleDistribution = [];
    $result_roles = mysqli_query($db, "SELECT role, COUNT(*) as count FROM admins GROUP BY role");
    if ($result_roles) {
        while ($row = mysqli_fetch_assoc($result_roles)) {
            $roleDistribution[$row['role']] = (int)$row['count'];
        }
    }

    // --- Inventory Trend Data (last 12 months) ---
    $inventoryTrendMonths = [];
    $inventoryTrendData = [];
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $label = date('M', strtotime("-$i months"));
        $inventoryTrendMonths[] = $label;
        $start = $month . '-01 00:00:00';
        $end = date('Y-m-t 23:59:59', strtotime($start));
        $q = "SELECT SUM(quantity) as total FROM stocks WHERE updated_at BETWEEN '$start' AND '$end'";
        $r = mysqli_query($db, $q);
        $row = $r ? mysqli_fetch_assoc($r) : ['total' => 0];
        $inventoryTrendData[] = (int)($row['total'] ?? 0);
    }

    // Close database connection
    // mysqli_close($db); // Removed to prevent closing before all queries are done
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin_dashboard</title>
    <!-- Ensure jQuery is loaded first -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="css/style.css? v=1">
    <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-bg: #f8f9fa; /* Lighter gray background */
            --card-bg: #ffffff;
            --text-heading: #212529;
            --text-body: #6c757d;
            --primary-accent: #7c3aed; /* A modern purple */
            --border-color: #dee2e6;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);

            /* Existing colors for continuity */
            --danger: #e74c3c;
            --warning: #f39c12;
            --success: #2ecc71;
            --info: #3498db;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-body);
        }
        body.dark-mode {
            --primary-bg: #1a202c;
            --card-bg: #2d3748;
            --text-heading: #f7fafc;
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
        .grid-stack-item-content .section-card .chart-grid {
            flex-grow: 1;
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
        .notification-tab-panel {
            display: none;
        }
        .notification-tab-panel.active {
            display: block;
        }
        .activity-widget .activity-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.85rem;
        }
        .activity-widget .activity-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .activity-widget .activity-item .icon {
            color: var(--primary-accent);
        }
        .activity-widget .activity-item .details {
            flex-grow: 1;
        }
        .activity-widget .activity-item .time {
            color: var(--text-body);
            font-size: 0.8rem;
        }
        .system-health .health-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 0;
            font-size: 0.9rem;
        }
        .system-health .health-item .icon {
            color: var(--success);
        }
        .system-health .health-item .icon.error {
            color: var(--danger);
        }

    </style>

</head>
<body class="body">
    <div class="dashboard_main_container">
            <?php include('partials/app_topNav.php'); ?>
            <?php include('partials/app_horizontal_nav.php'); ?>
            
            <!-- Display error messages -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 10px 20px;">
                    <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 10px 20px;">
                    <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <!-- Notification Bell and Role Switcher in Top Nav -->
                    <div class="dashboard-top-nav">
                        <div class="greeting">
                            <h2>Good Morning, <?php echo htmlspecialchars($_SESSION['admins']['fullname'] ?? 'Admin'); ?>!</h2>
                            <p>Here's what's happening in your dashboard today.</p>
                        </div>
                        <div class="user-actions">
                            <div style="position: relative;">
                            <button id="notificationBell" aria-label="Notifications">
                                <i class="fa fa-bell"></i>
                                <span id="notifDot" style="position: absolute; top: 8px; right: 8px; width: 8px; height: 8px; background: var(--danger); border-radius: 50%; display: none;"></span>
                            </button>
                                <div id="notificationDropdown" class="notification-dropdown" style="display:none;">
                                    <div class="notification-tabs">
                                        <button class="notification-tab active" data-tab="activity">Activity</button>
                                        <button class="notification-tab" data-tab="logins">Logins</button>
                                        <button class="notification-tab" data-tab="health">Health</button>
                                    </div>
                                    <div class="notification-body">
                                        <div id="tab-activity" class="notification-tab-panel active">
                                            <?php if (empty($recentActivities)): ?>
                                                <div class="notification-item empty">
                                                    <p>No new notifications</p>
                                                </div>
                                            <?php else: ?>
                                                <?php foreach ($recentActivities as $activity): ?>
                                                    <a href="#" class="notification-item">
                                                        <div class="icon" style="background-color: <?php echo $activity['color']; ?>;">
                                                            <i class="fa <?php echo $activity['icon']; ?>"></i>
                                                        </div>
                                                        <div class="text">
                                                            <p><?php echo $activity['text']; ?></p>
                                                            <small><?php echo time_ago($activity['time']); ?></small>
                                                        </div>
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <div id="tab-logins" class="notification-tab-panel">
                                            <?php if (empty($recentAdminLogins)): ?>
                                                <div class="notification-item empty"><p>No admin logins to show.</p></div>
                                            <?php else: ?>
                                                <?php foreach ($recentAdminLogins as $login): ?>
                                                    <a href="#" class="notification-item">
                                                        <div class="icon" style="background-color: var(--primary-accent);"><i class="fa fa-user-shield"></i></div>
                                                        <div class="text">
                                                            <p><strong><?php echo htmlspecialchars($login['fullname']); ?></strong> logged in</p>
                                                            <small><?php echo date('Y-m-d H:i', strtotime($login['last_login'])); ?></small>
                                                        </div>
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <div id="tab-health" class="notification-tab-panel">
                                            <div class="notification-item">
                                                <div class="icon" style="background-color: var(--success);"><i class="fa fa-check-circle"></i></div>
                                                <div class="text">
                                                    <p><strong>System OK</strong></p>
                                                    <small>No recent errors. Last backup: Today 2:00 AM</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification-footer">
                                        <a href="#">View all notifications</a>
                                    </div>
                                </div>
                            </div>
                            <select id="roleSwitcher" aria-label="Switch Role">
                                <option>Admin</option>
                                <option>Staff</option>
                                <option>User</option>
                            </select>
                            <button id="darkModeToggle" aria-label="Toggle Dark Mode"><i class="fa fa-moon"></i></button>
                        </div>
                    </div>
                    <!-- Dismissible Alert Banner for Urgent Issues -->
                    <div id="urgentAlert" class="alert alert-danger" style="display:none; margin: 0 0 8px 0; border-radius: 6px; font-weight: 500; padding: 5px 10px; font-size: 0.8rem;">
                        <span id="urgentAlertText">Urgent: Some products are out of stock!</span>
                        <button onclick="this.parentElement.style.display='none'" style="float:right; background:none; border:none; font-size:1.2em;">&times;</button>
                    </div>
                    <!-- Restore quick action links above KPIs -->
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
                            <div class="kpi-card" onclick="window.location.href='inventory.php'" style="cursor:pointer;">
                                <div class="icon" style="background: var(--danger);"><i class="fa-solid fa-box-open"></i></div>
                                <div class="info">
                                    <h3><?php echo $kpi_out_of_stock; ?></h3>
                                    <p>Items Out of Stock</p>
                                </div>
                            </div>
                            <div class="kpi-card" onclick="window.location.href='request_approval.php'" style="cursor:pointer;">
                                <div class="icon" style="background: var(--warning);"><i class="fa-solid fa-hourglass-half"></i></div>
                                <div class="info">
                                    <h3><?php echo $kpi_pending_approvals; ?></h3>
                                    <p>Pending Approvals</p>
                                </div>
                            </div>
                            <div class="kpi-card" onclick="window.location.href='view_product.php'" style="cursor:pointer;">
                                <div class="icon" style="background: var(--success);"><i class="fa-solid fa-boxes-stacked"></i></div>
                                <div class="info">
                                    <h3><?php echo $kpi_total_products; ?></h3>
                                    <p>Total Products</p>
                                </div>
                            </div>
                            <div class="kpi-card" onclick="window.location.href='add_users.php'" style="cursor:pointer;">
                                <div class="icon" style="background: var(--info);"><i class="fa-solid fa-users"></i></div>
                                <div class="info">
                                    <h3><?php echo $kpi_total_users; ?></h3>
                                    <p>Total Users</p>
                                </div>
                            </div>
                            <div class="kpi-card" onclick="window.location.href='view_suppliers.php'" style="cursor:pointer;">
                                <div class="icon" style="background: #9b59b6;"><i class="fa-solid fa-truck"></i></div>
                                <div class="info">
                                    <h3><?php echo $kpi_total_suppliers; ?></h3>
                                    <p>Total Suppliers</p>
                                </div>
                            </div>
                            <div class="kpi-card" onclick="window.location.href='inventory.php'" style="cursor:pointer;">
                                <div class="icon" style="background: #2c3e50;"><i class="fa-solid fa-dollar-sign"></i></div>
                                <div class="info">
                                    <h3><?php echo number_format($kpi_inventory_value, 0); ?></h3>
                                    <p>Total Stock Value</p>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-row">
                            <div class="layout-column-main">
                                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                                    <div class="dashboard-widget" style="flex: 1; height: 260px;">
                                        <h3 class="section_header" style="margin-bottom: 10px;">Inventory Trend</h3>
                                        <div id="inventory-trend-chart" style="flex-grow: 1; min-height: 0;"></div>
                                    </div>
                                    <div class="dashboard-widget" style="flex: 1; height: 260px;">
                                        <h3 class="section_header">Team & Roles</h3>
                                        <div id="admin-role-donut" style="flex-grow: 1; min-height: 0;"></div>
                                    </div>
                                </div>

                                <div class="dashboard-widget" style="height: 260px; margin-bottom: 12px;">
                                    <h3 class="section_header">Order & Reward Activity</h3>
                                    <div class="chart-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; flex-grow: 1;">
                                        <figure class="highcharts-figure chart-box" style="border: none; padding: 0; box-shadow: none;">
                                            <div id="orders-funnel-chart" style="flex-grow: 1; min-height: 0;"></div>
                                        </figure>
                                        <figure class="highcharts-figure chart-box" style="border: none; padding: 0; box-shadow: none;">
                                            <div id="reward-status-pie-chart" style="flex-grow: 1; min-height: 0;"></div>
                                        </figure>
                                    </div>
                                </div>

                                <div class="dashboard-widget" style="height: 300px;">
                                    <h3 class="section_header">Daily Activity & Popularity</h3>
                                    <div class="chart-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; flex-grow: 1;">
                                        <figure class="highcharts-figure chart-box" style="border: none; padding: 0; box-shadow: none;">
                                            <div id="line-chart-container" style="flex-grow: 1; min-height: 0;"></div>
                                        </figure>
                                        <figure class="highcharts-figure chart-box" style="border: none; padding: 0; box-shadow: none;">
                                            <div id="product-popularity-chart" style="flex-grow: 1; min-height: 0;"></div>
                                        </figure>
                                    </div>
                                </div>
                            </div>

                            <div class="layout-column-sidebar" style="display: flex; flex-direction: column; gap: 12px;">
                                <div class="dashboard-widget" style="height: 260px; margin-bottom: 12px;">
                                    <h3 class="section_header">Stock Level Distribution</h3>
                                    <div id="stock-status-pie-chart" style="flex-grow: 1; min-height: 0;"></div>
                                    </div>
                                <div class="dashboard-widget" style="height: 260px; margin-bottom: 12px;">
                                    <h3 class="section_header">Top 5 Lowest Stock Products</h3>
                                    <div id="low-stock-chart" style="flex-grow: 1; min-height: 0;"></div>
                                </div>
                                <div class="dashboard-widget" style="height: 300px;">
                                    <h3 class="section_header" style="font-size: 0.9rem; border-bottom-width: 2px;">Total Items Received per Supplier</h3>
                                    <div id="containerbarchart" style="flex-grow: 1; min-height: 0;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Help Button -->
                    <button id="helpBtn" aria-label="Help" style="position: fixed; bottom: 32px; right: 32px; background: #0F9E99; color: #fff; border: none; border-radius: 50%; width: 54px; height: 54px; font-size: 2rem; box-shadow: 0 4px 16px rgba(60,72,88,0.13); z-index: 1200; cursor: pointer;"><i class="fa fa-question"></i></button>
                    <div id="helpModal" style="display:none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.25); z-index: 1300; align-items: center; justify-content: center;">
                        <div style="background: #fff; border-radius: 16px; max-width: 400px; margin: 80px auto; padding: 32px 24px; box-shadow: 0 8px 32px rgba(60,72,88,0.18);">
                            <h3 style="color: #0F9E99;">Need Help?</h3>
                            <ul style="color: #333; padding-left: 18px;">
                                <li><b>How do I add a new product?</b> Use the "Add New Product" button in the Quick Actions bar.</li>
                                <li><b>How do I manage users/admins?</b> Use the sidebar or the switch button in User Management.</li>
                                <li><b>How do I export data?</b> Use the Export/Print buttons above each table or chart.</li>
                                <li><b>How do I get support?</b> Contact IT at <a href="mailto:support@educarehub.com">support@educarehub.com</a>.</li>
                            </ul>
                            <button onclick="document.getElementById('helpModal').style.display='none'" style="margin-top: 18px; background: #0F9E99; color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-size: 1rem;">Close</button>
                        </div>
                    </div>
                    <!-- Export/Print Buttons (UI only) -->
                    <div class="export-bar" style="margin: 18px 0 0 0; display: flex; gap: 12px; justify-content: flex-end;">
                        <button class="action-btn" onclick="alert('Export to PDF (demo)')"><i class="fa fa-file-pdf"></i> Export PDF</button>
                        <button class="action-btn" onclick="alert('Export to Excel (demo)')"><i class="fa fa-file-excel"></i> Export Excel</button>
                        <button class="action-btn" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
                    </div>
                    <div style="position:absolute;top:18px;right:32px;font-size:0.98em;color:#888;z-index:10;">
                        Last updated: <span id="lastUpdated"></span>
                    </div>
                </div>
            </div>
    </div>

<script src="js/script.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/funnel.js"></script>
<script src="https://code.highcharts.com/modules/non-cartesian-zoom.js"></script>
<script src="https://code.highcharts.com/modules/mouse-wheel-zoom.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    console.log('Highcharts dashboard script running');
    // Chart data from PHP
    var statusData = <?php echo json_encode($statusData); ?>;
    var rewardStatusData = <?php echo json_encode($rewardStatusData); ?>;
    var lineChartLabels = <?php echo json_encode($lineChartLabels); ?>;
    var lineChartData = <?php echo json_encode($lineChartData); ?>;
    var popularityLabels = <?php echo json_encode($popularityLabels); ?>;
    var popularityData = <?php echo json_encode($popularityData); ?>;
    var lowStockLabels = <?php echo json_encode($lowStockLabels); ?>;
    var lowStockData = <?php echo json_encode($lowStockData); ?>;
    var barChartCategories = <?php echo json_encode($barChartCategories); ?>;
    var barChartSeriesData = <?php echo json_encode($barChartSeriesData); ?>;
    var yAxisMax = <?php echo $yAxisMax; ?>;
    var roleDistribution = <?php echo json_encode($roleDistribution); ?>;
    var roleData = Object.keys(roleDistribution).map(function(role) { return { name: role, y: roleDistribution[role] }; });

    // Inventory Trend Chart
    if (document.getElementById('inventory-trend-chart')) {
        Highcharts.chart('inventory-trend-chart', {
            chart: { 
                type: 'area', 
                animation: { duration: 900, easing: 'easeOutBounce' },
                spacing: [10, 10, 10, 10],
                reflow: true
            },
            title: {
                text: 'Inventory Trend (Last 12 Months)',
                align: 'center'
            },
            xAxis: {
                categories: <?php echo json_encode($inventoryTrendMonths); ?>,
            title: { text: null },
                labels: { style: { fontSize: '9px' } }
            },
            yAxis: {
                min: 0,
                title: { text: null },
                allowDecimals: false,
                labels: { style: { fontSize: '9px' } }
            },
            tooltip: { shared: true, valueSuffix: '' },
            plotOptions: { area: { marker: { enabled: false }, fillOpacity: 0.5 } },
            series: [{
                name: 'Total Stock',
                data: <?php echo json_encode($inventoryTrendData); ?>,
                color: '#1976d2'
            }],
            credits: { enabled: false },
            legend: { enabled: false }
        });
    }
    // Stock Status Pie Chart
    if (document.getElementById('stock-status-pie-chart')) {
        Highcharts.chart('stock-status-pie-chart', { 
            chart: { 
                type: 'pie', 
                animation: { duration: 900, easing: 'easeOutBounce' },
                spacing: [10, 10, 10, 10],
                reflow: true
            }, 
            title: {
                text: 'Stock Level Distribution',
                align: 'center'
            },
            tooltip: { pointFormat: '{series.name}: <b>{point.y}</b>' }, 
            accessibility: { point: { valueSuffix: '' } }, 
            plotOptions: { 
                pie: { 
                    allowPointSelect: true, 
                    cursor: 'pointer', 
                    dataLabels: { 
                        enabled: true, 
                        format: '{point.name}: {point.y}', 
                        style: { fontSize: '9px', textOverflow: 'clip' },
                        distance: 20,
                        connectorColor: '#666',
                        connectorWidth: 1,
                        softConnector: true,
                        crop: false,
                        overflow: 'allow'
                    }, 
                    point: { 
                        events: { 
                            click: function() { 
                                window.location.href = 'inventory.php?filter=' + encodeURIComponent(this.name); 
                            } 
                        } 
                    } 
                } 
            }, 
            series: [{ 
                name: 'Count', 
                colorByPoint: true, 
                data: [ 
                    { name: 'No Stocks', y: <?php echo $no_stock_count; ?>, color: '#232526' }, 
                    { name: 'Low Stock', y: <?php echo $low_stock_count; ?>, color: '#e74c3c' }, 
                    { name: 'Normal', y: <?php echo $normal_stock_count; ?>, color: '#3498db' }, 
                    { name: 'High Stock', y: <?php echo $high_stock_count; ?>, color: '#2ecc71' } 
                ] 
            }], 
            credits: { enabled: false }, 
            legend: { enabled: false } 
        });
    }
    // Low Stock Chart
    if (document.getElementById('low-stock-chart')) {
        Highcharts.chart('low-stock-chart', { 
            chart: { 
                type: 'bar', 
                animation: { duration: 900, easing: 'easeOutBounce' },
                spacing: [10, 10, 10, 10],
                reflow: true
            }, 
            title: {
                text: 'Top 5 Lowest Stock Products',
                align: 'center'
            },
            xAxis: {
                categories: <?php echo json_encode($lowStockLabels); ?>,
            title: { text: null }, 
                labels: { style: { fontSize: '9px' } }
            },
            yAxis: {
                min: 0,
                title: { text: null },
                allowDecimals: false,
                labels: { style: { fontSize: '9px' } }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    dataLabels: { enabled: true, style: { fontSize: '9px' }, allowOverlap: true, crop: false },
                    point: { events: { click: function() { showDrilldownModal('Low Stock Detail', 'Product: ' + this.category + '<br>Quantity: ' + this.y); } } }
                }
            },
            series: [{
                name: 'Stock',
                data: <?php echo json_encode($lowStockData); ?>,
                color: '#f44336'
            }],
            credits: { enabled: false }, 
            legend: { enabled: false }, 
            animation: true 
        });
    }
    // Quantity Received Per Supplier (Bar Chart)
    if (document.getElementById('containerbarchart')) {
        Highcharts.chart('containerbarchart', { 
            chart: { 
                type: 'column',
                spacing: [10, 10, 10, 10],
                reflow: true
            }, 
            title: {
                text: 'Total Items Received per Supplier',
                align: 'center'
            },
            xAxis: {
                categories: <?php echo json_encode($barChartCategories); ?>,
                crosshair: true,
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '9px'
                    }
                },
                scrollbar: {
                    enabled: true
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Quantity Received'
                },
                max: <?php echo $yAxisMax; ?>,
                tickInterval: <?php echo $yAxisMax; ?> > 10 ? Math.ceil(<?php echo $yAxisMax; ?> / 5) : 1,
                labels: {
                    style: {
                        fontSize: '9px'
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    return '<b>' + this.x + '</b><br/>Received: ' + Highcharts.numberFormat(this.y, 0);
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    borderRadius: 5,
                    pointWidth: 20,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}',
                        style: {
                            fontSize: '9px'
                        },
                        allowOverlap: true,
                        crop: false
                    }
                }
            },
            series: [{
                name: 'Quantity Received',
                data: <?php echo json_encode($barChartSeriesData); ?>,
                color: '#1e90ff'
            }],
            credits: {
                enabled: false
            },
            legend: {
                enabled: false
            }
        });
    }
    // Orders Funnel Chart
    if (document.getElementById('orders-funnel-chart')) {
        Highcharts.chart('orders-funnel-chart', { 
            chart: { 
                type: 'funnel', 
                animation: { duration: 900, easing: 'easeOutBounce' },
                spacing: [10, 10, 10, 10],
                reflow: true
            }, 
            title: {
                text: 'Purchase Order Status',
                align: 'center'
            },
            plotOptions: { 
                series: { 
                    dataLabels: { 
                        enabled: true, 
                        format: '<b>{point.name}</b>: {point.y}', 
                        softConnector: true, 
                        style: { fontSize: '9px', textOverflow: 'clip' },
                        crop: false,
                        allowOverlap: true
                    }, 
                    neckWidth: '30%', 
                    neckHeight: '25%', 
                    width: '80%'
                } 
            }, 
            tooltip: { pointFormat: '<b>{point.y}</b> orders' }, 
            series: [{ 
                name: 'Orders', 
                data: [ 
                    ['Pending', statusData.PENDING], 
                    ['Incomplete', statusData.INCOMPLETE], 
                    ['Completed', statusData.COMPLETED] 
                ], 
                colors: ['#fbc02d', '#e74c3c', '#43a047'] 
            }], 
            credits: { enabled: false }, 
            legend: { enabled: false }
        });
    }
    // Reward Status Pie Chart
    if (document.getElementById('reward-status-pie-chart')) {
        Highcharts.chart('reward-status-pie-chart', { 
            chart: { 
                type: 'pie',
                animation: { duration: 900, easing: 'easeOutBounce' },
                spacing: [10, 10, 10, 10],
                reflow: true
            }, 
            title: {
                text: 'Reward Order Status',
                align: 'center'
            },
            tooltip: { pointFormat: '{point.name}: <b>{point.y} ({point.percentage:.1f}%)</b>' }, 
            plotOptions: { 
                pie: { 
                    allowPointSelect: true, 
                    cursor: 'pointer', 
                    dataLabels: { 
                        enabled: true, 
                        format: '{point.name}: {point.y}', 
                        distance: 15,
                        style: { fontSize: '9px', textOutline: 'none', color: 'black', textOverflow: 'clip' },
                        connectorColor: '#666',
                        connectorWidth: 1,
                        softConnector: true,
                        crop: false,
                        overflow: 'allow'
                    }, 
                    showInLegend: false 
                } 
            }, 
            series: [{ 
                name: 'Status', 
                data: [ 
                    { name: 'Pending', y: rewardStatusData['pending'], color: '#f59e42' }, 
                    { name: 'On Delivery', y: rewardStatusData['on delivery'], color: '#3498db' }, 
                    { name: 'Declined', y: rewardStatusData['declined'], color: '#9b59b6' }, 
                    { name: 'Delivered', y: rewardStatusData['delivered'], color: '#2ecc71' }, 
                    { name: 'Unsuccessful', y: rewardStatusData['unsuccessful'], color: '#e74c3c' } 
                ] 
            }], 
            credits: { enabled: false } 
        });
    }
    // Line Chart
    if (document.getElementById('line-chart-container')) {
        Highcharts.chart('line-chart-container', { 
            chart: { 
                type: 'line',
                animation: { duration: 900, easing: 'easeOutBounce' },
                spacing: [10, 10, 10, 10],
                reflow: true
            }, 
            title: {
                text: 'Reward Approval History (Last 7 Days)',
                align: 'center'
            },
            xAxis: {
                categories: <?php echo json_encode($lineChartLabels); ?>,
            title: { text: null }, 
                labels: { style: { fontSize: '9px' } }
            },
            yAxis: {
                title: { text: null },
                min: 0,
                allowDecimals: false,
                labels: { style: { fontSize: '9px' } }
            },
            tooltip: { pointFormat: '<b>{series.name}</b>: {point.y}' }, 
            plotOptions: {
                line: {
                    dataLabels: { enabled: false },
                    enableMouseTracking: true
                },
                series: {
                    marker: { enabled: true, radius: 3 },
                    lineWidth: 2
                }
            },
            series: [{
                name: 'Approved Orders',
                data: <?php echo json_encode($lineChartData); ?>,
                color: '#28a745'
            }],
            credits: { enabled: false }, 
            legend: { enabled: false } 
        });
    }
    // Product Popularity Chart
    if (document.getElementById('product-popularity-chart')) {
        Highcharts.chart('product-popularity-chart', { 
            chart: { 
                type: 'bar',
                animation: { duration: 900, easing: 'easeOutBounce' },
                spacing: [10, 10, 10, 10],
                reflow: true
            }, 
            title: {
                text: 'Top 5 Most Claimed Products',
                align: 'center'
            },
            xAxis: {
                categories: popularityLabels.slice().reverse(),
            title: { text: null }, 
                labels: { style: { fontSize: '9px' } }
            },
            yAxis: {
                min: 0,
                title: { text: null, align: 'high' },
                labels: { overflow: 'justify', style: { fontSize: '9px' } },
                allowDecimals: false
            },
            tooltip: { valueSuffix: ' claims' }, 
            plotOptions: {
                bar: {
                    dataLabels: { enabled: true, style: { fontSize: '9px' }, allowOverlap: true, crop: false }
                }
            },
            legend: { enabled: false }, 
            credits: { enabled: false }, 
            series: [{
                name: 'Claims',
                data: popularityData.slice().reverse(),
                color: '#ff9800'
            }]
        });
    }
    // Admin Role Donut Chart
    if (document.getElementById('admin-role-donut')) {
        Highcharts.chart('admin-role-donut', { 
            chart: { 
                type: 'pie', 
                backgroundColor: null,
                spacing: [10, 10, 10, 10],
                reflow: true
            }, 
            title: {
                text: 'Team & Roles',
                align: 'center'
            },
            plotOptions: {
                pie: {
                    innerSize: '60%',
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {point.y}',
                        style: { fontSize: '10px', color: 'black', textOverflow: 'clip' },
                        distance: 20,
                        crop: false,
                        overflow: 'allow'
                    },
                    showInLegend: false
                }
            },
            tooltip: { pointFormat: '{series.name}: <b>{point.y}</b>' }, 
            series: [{
                name: 'Admins',
                data: roleData
            }],
            credits: { enabled: false } 
        });
    }

    // Toggle Notification Dropdown
    const notifBell = document.getElementById('notificationBell');
    const notifDropdown = document.getElementById('notificationDropdown');
    notifBell.addEventListener('click', function(e) {
        e.stopPropagation();
        if (notifDropdown.style.display === 'none' || notifDropdown.style.display === '') {
            notifDropdown.style.display = 'flex';
            notifDot.style.display = 'none'; // Hide dot on open
        } else {
            notifDropdown.style.display = 'none';
        }
    });

    // Handle Notification Tabs
    const tabs = document.querySelectorAll('.notification-tab');
    const tabPanels = document.querySelectorAll('.notification-tab-panel');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Deactivate all tabs and panels
            tabs.forEach(t => t.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));

            // Activate the clicked tab and corresponding panel
            tab.classList.add('active');
            const targetPanel = document.getElementById('tab-' + tab.dataset.tab);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        });
    });

    // Hide dropdown if click outside
    window.addEventListener('click', function(e) {
        if (notifDropdown.style.display === 'flex' && !notifDropdown.contains(e.target) && e.target !== notifBell && !notifBell.contains(e.target)) {
            notifDropdown.style.display = 'none';
        }
    });

    // Notification Bell Demo
    const notifDot = document.getElementById('notifDot');
    const hasActivities = <?php echo !empty($recentActivities) ? 'true' : 'false'; ?>;
    if (hasActivities) {
        notifDot.style.display = 'block';
    }

    if (<?php echo (int)$kpi_out_of_stock; ?> > 0) {
        document.getElementById('urgentAlert').style.display = 'block';
    }

    // Help Modal
    const helpBtn = document.getElementById('helpBtn');
    const helpModal = document.getElementById('helpModal');
    helpBtn.onclick = function() { helpModal.style.display = 'flex'; };

    // Dark Mode Toggle
    const darkModeToggle = document.getElementById('darkModeToggle');
    darkModeToggle.onclick = function() {
        document.body.classList.toggle('dark-mode');
        // Highcharts dark mode
        Highcharts.setOptions({
            chart: {
                backgroundColor: document.body.classList.contains('dark-mode') ? '#2d3748' : '#fff',
                style: { 
                    fontFamily: 'Inter, sans-serif',
                    color: document.body.classList.contains('dark-mode') ? '#f7fafc' : '#212529'
                }
            },
            title: {
                style: { 
                    color: document.body.classList.contains('dark-mode') ? '#f7fafc' : '#212529',
                    fontWeight: '600'
                }
            },
            xAxis: {
                labels: { style: { color: document.body.classList.contains('dark-mode') ? '#a0aec0' : '#6c757d' } },
                title: { style: { color: document.body.classList.contains('dark-mode') ? '#a0aec0' : '#6c757d' } }
            },
            yAxis: {
                labels: { style: { color: document.body.classList.contains('dark-mode') ? '#a0aec0' : '#6c757d' } },
                title: { style: { color: document.body.classList.contains('dark-mode') ? '#a0aec0' : '#6c757d' } }
            },
            legend: {
                itemStyle: { color: document.body.classList.contains('dark-mode') ? '#f7fafc' : '#212529' }
            },
            tooltip: {
                backgroundColor: document.body.classList.contains('dark-mode') ? 'rgba(26, 32, 44, 0.85)' : 'rgba(255, 255, 255, 0.85)',
                style: {
                    color: document.body.classList.contains('dark-mode') ? '#f7fafc' : '#212529'
                }
            }
        });
        // Redraw all charts
        Highcharts.charts.forEach(function(chart) { if (chart) chart.redraw(); });
    };

    // Accessibility: ARIA labels for main dashboard sections
    const sectionHeaders = document.querySelectorAll('.section_header');
    sectionHeaders.forEach(function(header) {
        header.setAttribute('tabindex', '0');
        header.setAttribute('role', 'heading');
        header.setAttribute('aria-level', '2');
    });

    // After all charts are created, reflow them to fit their containers
    setTimeout(function() {
        Highcharts.charts.forEach(function(chart) {
            if (chart) {
                try {
                    chart.reflow();
                } catch (e) {
                    // Could fail if chart container is not visible, ignore.
                }
            }
        });
    }, 200);
});
</script>

<div id="themePicker" style="position:fixed;bottom:24px;left:24px;z-index:1200;background:#fff;padding:12px 18px;border-radius:10px;box-shadow:0 2px 12px rgba(60,72,88,0.13);">
    <label for="themeSelect" style="font-weight:600;color:#0F9E99;">Theme:</label>
    <select id="themeSelect" onchange="document.body.className=this.value" style="margin-left:8px;padding:4px 10px;border-radius:6px;">
        <option value="">Light</option>
        <option value="dark-mode">Dark</option>
    </select>
    <span style="margin-left:18px;color:#888;font-size:0.95em;">(Drag-and-drop coming soon)</span>
</div>

</body>
</html>