<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has super_admin role
if ($_SESSION['admins']['role'] !== 'super_admin') {
    $_SESSION['error_message'] = "Access denied. Only Super Admins can edit admin accounts.";
    header("Location: admin_dashboard.php");
    exit();
}

include_once('connection.php');

$admin_id = (int)($_GET['id'] ?? 0);
$errors = [];

if ($admin_id <= 0) {
    header("Location: add_admins.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $salary = trim($_POST['Salary'] ?? '');
    $role = trim($_POST['Role'] ?? '');

    if (empty($fullname) || empty($username) || empty($email) || empty($salary) || empty($role)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE admins SET fullname = ?, username = ?, email = ?, Salary = ?, Role = ? WHERE admin_id = ?");
            $stmt->execute([$fullname, $username, $email, $salary, $role, $admin_id]);

            $_SESSION['success_message'] = "Admin updated successfully!";
            header("Location: add_admins.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                 if (strpos($e->getMessage(), 'username')) {
                    $errors[] = "This username is already taken.";
                } elseif (strpos($e->getMessage(), 'email')) {
                    $errors[] = "This email is already registered.";
                } else {
                    $errors[] = "An admin with these details already exists.";
                }
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Fetch admin data for the form
try {
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        $_SESSION['error_message'] = "Admin not found.";
        header("Location: add_admins.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database error fetching admin: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
    <style>
        body { background: #f0f4f8; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .edit-card { width: 100%; max-width: 600px; border-radius: 18px; box-shadow: 0 8px 32px rgba(60, 72, 88, 0.15); border: none; }
        .edit-card .card-header { background: linear-gradient(90deg, #0F9E99 0%, #2dd4bf 100%); color: #fff; text-align: center; padding: 30px 20px 18px 20px; border-bottom: none; border-radius: 18px 18px 0 0; }
        .edit-card .card-header h3 { margin-bottom: 0; font-weight: 700; letter-spacing: 1px; }
        .edit-card .btn-primary { background: linear-gradient(90deg, #0F9E99 0%, #2dd4bf 100%); border: none; font-weight: 600; letter-spacing: 1px; transition: background 0.2s, box-shadow 0.2s; box-shadow: 0 2px 8px rgba(60, 72, 88, 0.07); }
        .edit-card .btn-primary:hover, .edit-card .btn-primary:focus { background: linear-gradient(90deg, #2dd4bf 0%, #0F9E99 100%); box-shadow: 0 4px 16px rgba(60, 72, 88, 0.13); }
        .btn-link { color: #0F9E99; }
    </style>
</head>
<body>
<div class="edit-card card">
    <div class="card-header">
        <h3><i class="fa fa-user-shield"></i> Edit Admin</h3>
    </div>
    <div class="card-body p-4">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="update_admins.php?id=<?php echo $admin_id; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="fullname" class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo htmlspecialchars($admin['fullname']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" id="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="Salary" class="form-label">Salary</label>
                    <input type="number" step="0.01" class="form-control" name="Salary" id="Salary" value="<?php echo htmlspecialchars($admin['Salary']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="Role" class="form-label">Role</label>
                    <select class="form-select" name="Role" id="Role" required>
                        <option value="super_admin" <?php if($admin['Role'] == 'super_admin') echo 'selected'; ?>>Super Admin</option>
                        <option value="admin" <?php if($admin['Role'] == 'admin') echo 'selected'; ?>>Admin</option>
                        <option value="stock_encoder" <?php if($admin['Role'] == 'stock_encoder') echo 'selected'; ?>>Stock Encoder</option>
                        <option value="purchasing_officer" <?php if($admin['Role'] == 'purchasing_officer') echo 'selected'; ?>>Purchasing Officer</option>
                         <option value="rewards_manager" <?php if($admin['Role'] == 'rewards_manager') echo 'selected'; ?>>Rewards Manager</option>
                        <option value="viewer" <?php if($admin['Role'] == 'viewer') echo 'selected'; ?>>Viewer</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Admin</button>
</form>
        <div class="text-center mt-3">
            <a href="add_admins.php" class="btn-link">Back to Admin List</a>
        </div>
    </div>
</div>
</body>
</html>
