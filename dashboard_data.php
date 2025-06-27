<?php
header('Content-Type: application/json');
$db = mysqli_connect('localhost', 'root', '', 'educarehub');
if (!$db) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}
// KPIs
$kpi_pending_approvals = 0;
$kpi_out_of_stock = 0;
$kpi_total_users = 0;
$kpi_total_products = 0;
$kpi_total_suppliers = 0;

$result_pending = mysqli_query($db, "SELECT COUNT(*) as count FROM reward_orders WHERE status = 'pending'");
if($result_pending) $kpi_pending_approvals = mysqli_fetch_assoc($result_pending)['count'];

$result_stock = mysqli_query($db, "SELECT COUNT(*) as count FROM stocks WHERE quantity = 0");
if($result_stock) $kpi_out_of_stock = mysqli_fetch_assoc($result_stock)['count'];

$result_users = mysqli_query($db, "SELECT COUNT(*) as count FROM users");
if($result_users) $kpi_total_users = mysqli_fetch_assoc($result_users)['count'];

$result_products = mysqli_query($db, "SELECT COUNT(*) as count FROM products");
if($result_products) $kpi_total_products = mysqli_fetch_assoc($result_products)['count'];

$result_suppliers = mysqli_query($db, "SELECT COUNT(*) as count FROM supplier");
if($result_suppliers) $kpi_total_suppliers = mysqli_fetch_assoc($result_suppliers)['count'];

// Low Stock Chart
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

echo json_encode([
    'kpi_out_of_stock' => $kpi_out_of_stock,
    'kpi_pending_approvals' => $kpi_pending_approvals,
    'kpi_total_products' => $kpi_total_products,
    'kpi_total_users' => $kpi_total_users,
    'kpi_total_suppliers' => $kpi_total_suppliers,
    'lowStockLabels' => $lowStockLabels,
    'lowStockData' => $lowStockData
]); 