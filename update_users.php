<?php
session_start();
include_once('connection.php');

$user_id = (int)($_GET['id'] ?? 0);
$errors = [];

if ($user_id <= 0) {
    header("Location: add_users.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birthdate = $_POST['birthdate'] ?? '';

    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($birthdate)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, username = ?, email = ?, birthdate = ? WHERE user_id = ?");
            $stmt->execute([$firstname, $lastname, $username, $email, $birthdate, $user_id]);

            $_SESSION['success_message'] = "User updated successfully!";
            header("Location: add_users.php");
            exit();
        } catch (PDOException $e) {
            // Check for duplicate entry
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'username')) {
                    $errors[] = "This username is already taken.";
                } elseif (strpos($e->getMessage(), 'email')) {
                    $errors[] = "This email is already registered.";
                } else {
                    $errors[] = "A user with these details already exists.";
                }
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Fetch user data for the form
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['error_message'] = "User not found.";
        header("Location: add_users.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database error fetching user: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
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
        <h3><i class="fa fa-user-edit"></i> Edit User</h3>
    </div>
    <div class="card-body p-4">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="update_users.php?id=<?php echo $user_id; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="firstname" class="form-label">First Name</label>
                    <input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="lastname" class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="birthdate" class="form-label">Birthdate</label>
                <input type="date" class="form-control" name="birthdate" id="birthdate" value="<?php echo htmlspecialchars($user['birthdate']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update User</button>
        </form>
        <div class="text-center mt-3">
            <a href="add_users.php" class="btn-link">Back to User List</a>
        </div>
    </div>
</div>
</body>
</html>
