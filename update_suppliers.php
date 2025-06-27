<?php
$db = mysqli_connect('localhost', 'root', '', 'educarehub');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->prepare("SELECT * FROM supplier WHERE supplier_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = $_POST['supplier_name'] ?? '';
    $supplier_location = $_POST['supplier_location'] ?? '';
    $email = $_POST['email']?? '';
    $contact_no = $_POST['contact_no'] ?? ($user['contact_no'] ?? '');


    $stmt = $db->prepare("UPDATE supplier SET supplier_name=?, supplier_location=?, email=?, contact_no=? WHERE supplier_id=?");
    $stmt->bind_param("ssiss", $supplier_name, $supplier_location, $email, $contact_no, $id);

    if ($stmt->execute()) {
        header("Location: view_suppliers.php");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}
?>

<?php if (!empty($user)): ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background: #f0f4f8; min-height: 100vh; }
    .edit-supplier-card { max-width: 500px; margin: 40px auto; border-radius: 18px; box-shadow: 0 8px 32px rgba(60, 72, 88, 0.15); }
    .edit-supplier-card .card-header { background: linear-gradient(90deg, #0F9E99 0%, #2dd4bf 100%); color: #fff; text-align: center; padding: 30px 20px 18px 20px; }
    .edit-supplier-card .card-header h3 { margin-bottom: 0; font-weight: 700; letter-spacing: 1px; }
    .edit-supplier-card .card-header p { margin-bottom: 0; font-size: 1rem; opacity: 0.85; }
    .edit-supplier-card .btn-primary { background: linear-gradient(90deg, #0F9E99 0%, #2dd4bf 100%); border: none; font-weight: 600; letter-spacing: 1px; transition: background 0.2s, box-shadow 0.2s; box-shadow: 0 2px 8px rgba(60, 72, 88, 0.07); }
    .edit-supplier-card .btn-primary:hover, .edit-supplier-card .btn-primary:focus { background: linear-gradient(90deg, #2dd4bf 0%, #0F9E99 100%); box-shadow: 0 4px 16px rgba(60, 72, 88, 0.13); }
</style>
<div class="edit-supplier-card card">
    <div class="card-header">
        <h3><i class="fa fa-edit"></i> Edit Supplier</h3>
        <p>Update the details for this supplier below.</p>
    </div>
    <div class="card-body p-4">
        <form method="POST">
            <div class="mb-3">
                <label for="supplier_name" class="form-label">Supplier Name</label>
                <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="<?php echo htmlspecialchars($user['supplier_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="supplier_location" class="form-label">Supplier Location</label>
                <textarea class="form-control" id="supplier_location" name="supplier_location" rows="2" required><?php echo htmlspecialchars($user['supplier_location']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contact_no" class="form-label">Contact No</label>
                <input type="text" class="form-control" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($user['contact_no'] ?? ''); ?>" required />
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Supplier</button>
        </form>
    </div>
</div>
<script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
<?php else: ?>
  <div class="alert alert-danger mt-4">Supplier not found.</div>
<?php endif; ?>
