<?php
session_start();
$db = mysqli_connect('localhost', 'root', '', 'educarehub');
if (!$db) die("Connection failed: " . mysqli_connect_error());

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $db->prepare("DELETE FROM products_supplier WHERE p_supplier_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Order deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting order: " . $stmt->error;
    }
    $stmt->close();
}

header("Location: view_orders.php");
exit();
