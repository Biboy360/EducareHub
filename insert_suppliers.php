<?php

if (!isset($_SESSION['admins'])) {
    $_SESSION['error'] = "Access denied. Please log in as an admin.";
    header("Location: admin_login.php");
    exit();
}

$errors = [];
$db = mysqli_connect('localhost', 'root', '', 'educarehub');
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

$admin_id = $_SESSION['admins']['admin_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = trim($_POST['supplier_name'] ?? '');
    $supplier_location = trim($_POST['supplier_location'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact_no = trim($_POST['contact_no'] ?? '');

    if (empty($supplier_name)) {
        $errors['supplier_name'] = "supplier name is required.";
    }
    if (empty($supplier_location)) {
        $errors['supplier_location'] = "Product supplier location is required.";
    }
    if (empty($email)) {
        $errors['email'] = "Product email is required.";
    }
    if (empty($contact_no)) {
        $errors['contact_no'] = "Contact number is required.";
    }
    
    $stmt = $db->prepare("SELECT admin_id FROM admins WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $errors['admin'] = "Invalid admin. Cannot add suppliers.";
    }
    $stmt->close();

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO supplier (supplier_name, supplier_location, email, contact_no, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $supplier_name, $supplier_location, $email, $contact_no, $admin_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Product successfully added.";
            header("Location: add_suppliers.php");
            exit();
        } else {
            $errors['database'] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
