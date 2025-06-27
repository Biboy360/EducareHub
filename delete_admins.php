<?php
session_start(); // Always start the session at the beginning

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has super_admin role
if ($_SESSION['admins']['role'] !== 'super_admin') {
    $_SESSION['error_message'] = "Access denied. Only Super Admins can delete admin accounts.";
    header("Location: admin_dashboard.php");
    exit();
}

// Include your database connection file
include_once('connection.php');

$admin_id = (int)($_GET['id'] ?? 0);

// Prevent super admin from deleting themselves
if ($admin_id == $_SESSION['admins']['admin_id']) {
    $_SESSION['error_message'] = "You cannot delete your own account.";
    header("Location: add_admins.php");
    exit();
}

if ($admin_id <= 0) {
    $_SESSION['error_message'] = "Invalid admin ID.";
    header("Location: add_admins.php");
    exit();
}

if ($admin_id > 0) {
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Get the current super admin ID to reassign records
        $superAdminStmt = $conn->prepare("SELECT admin_id FROM admins WHERE role = 'super_admin' AND admin_id != ? LIMIT 1");
        $superAdminStmt->execute([$admin_id]);
        $superAdmin = $superAdminStmt->fetch(PDO::FETCH_ASSOC);
        $superAdminStmt->closeCursor();
        
        if (!$superAdmin) {
            throw new Exception("No other super admin found to reassign records. Cannot delete admin.");
        }
        
        $reassignAdminId = $superAdmin['admin_id'];
        
        // Update products created by this admin
        $updateProductsStmt = $conn->prepare("UPDATE products SET created_by = ? WHERE created_by = ?");
        $updateProductsStmt->execute([$reassignAdminId, $admin_id]);
        $updateProductsStmt->closeCursor();
        
        // Update stocks created by this admin
        $updateStocksStmt = $conn->prepare("UPDATE stocks SET created_by = ? WHERE created_by = ?");
        $updateStocksStmt->execute([$reassignAdminId, $admin_id]);
        $updateStocksStmt->closeCursor();
        
        // Update products_supplier orders created by this admin
        $updateOrdersStmt = $conn->prepare("UPDATE products_supplier SET created_by = ? WHERE created_by = ?");
        $updateOrdersStmt->execute([$reassignAdminId, $admin_id]);
        $updateOrdersStmt->closeCursor();
        
        // Update suppliers created by this admin
        $updateSuppliersStmt = $conn->prepare("UPDATE supplier SET created_by = ? WHERE created_by = ?");
        $updateSuppliersStmt->execute([$reassignAdminId, $admin_id]);
        $updateSuppliersStmt->closeCursor();
        
        // Now delete the admin
        $deleteAdminStmt = $conn->prepare("DELETE FROM admins WHERE admin_id = ?");
        $deleteAdminStmt->execute([$admin_id]);

        if ($deleteAdminStmt->rowCount() > 0) {
            $conn->commit();
            $_SESSION['success_message'] = "Admin deleted successfully! All related records have been reassigned.";
        } else {
            $conn->rollback();
            $_SESSION['error_message'] = "Admin not found or could not be deleted.";
        }
        
        $deleteAdminStmt->closeCursor();
        
    } catch (PDOException $e) {
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        $_SESSION['error_message'] = "Error deleting admin: " . $e->getMessage();
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollback();
        }
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
}

// Redirect back to the admin list page
header("Location: add_admins.php");
exit(); // Always call exit() after header() to ensure script termination
?>