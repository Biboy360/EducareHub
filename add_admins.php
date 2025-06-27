<?php
session_start(); // Start the session at the very beginning of the file

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has super_admin role
if ($_SESSION['admins']['role'] !== 'super_admin') {
    $_SESSION['error_message'] = "Access denied. Only Super Admins can manage admin accounts.";
    header("Location: admin_dashboard.php");
    exit();
}

// Include your database connection file
include_once('connection.php');

// Initialize success/error messages from session and clear them
$message_type = $_SESSION['message_type'] ?? '';
$message = $_SESSION['message'] ?? '';

// Clear session messages so they don't reappear on refresh
unset($_SESSION['message_type']);
unset($_SESSION['message']);

// Retrieve old input and errors from session and clear them
$old_input = $_SESSION['old_admin_input'] ?? [];
unset($_SESSION['old_admin_input']);

$errors = $_SESSION['admin_form_errors'] ?? [];
unset($_SESSION['admin_form_errors']);

$session_success = $_SESSION['success_message'] ?? null;
$session_error = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

$is_viewer = ($_SESSION['admins']['role'] ?? '') === 'viewer';

if (isset($_POST['add_admin'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $salary = trim($_POST['Salary'] ?? '');
    $role = trim($_POST['Role'] ?? '');
    $errors = [];

    // Server-side validation
    if (empty($fullname)) { $errors[] = "Full Name is required."; }
    if (empty($username)) { $errors[] = "Username is required."; }
    if (empty($role)) { $errors[] = "Role is required."; }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format. Please enter a valid email address.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($salary === '') { // Use strict comparison for empty string check
        $errors[] = "Salary is required.";
    } elseif (!is_numeric($salary)) {
        $errors[] = "Salary must be a valid number.";
    }

    if (empty($errors)) {
        try {
            $stmt_check = $conn->prepare("SELECT admin_id FROM admins WHERE username = :username OR email = :email");
            $stmt_check->execute([':username' => $username, ':email' => $email]);
            if ($stmt_check->fetch()) {
                $errors[] = "Username or email already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO admins (fullname, username, email, password, Salary, Role) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$fullname, $username, $email, $hashed_password, $salary, $role]);
                $_SESSION['success_message'] = "Admin added successfully!";
                header("Location: add_admins.php");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // Store errors in session to display them after redirect
    if(!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_input'] = $_POST;
        header("Location: add_admins.php");
        exit();
    }
}

$form_errors = $_SESSION['form_errors'] ?? [];
$form_input = $_SESSION['form_input'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_input']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Admins</title>

    <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="css/style.css?v=4">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.5.0/css/rowGroup.bootstrap5.min.css" />

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script> 
    
    <script src="https://cdn.datatables.net/rowgroup/1.5.0/js/dataTables.rowGroup.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .highlight-pending { color: orange; font-weight: bold; }
        .highlight-incomplete { color: red; font-weight: bold; }
        .complete { color: green; font-weight: bold; }
        /* Basic styling for error messages */
        .error-message {
            color: #dc3545; /* Bootstrap danger color */
            font-size: 0.875em;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
        }
        /* Style to make input fields red when there's an error */
        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>

</head>

<body class="body">
    <div class="dashboard_main_container">
        <?php include('partials/app_topNav.php'); ?>
        <?php include('partials/app_horizontal_nav.php'); ?>
        <div class="dashboard_content">
            <div class="dashboard_content_main">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h1 class="section_header mb-0"><i class="fa fa-user-shield"></i> Manage Admins</h1>
                            <div>
                                <?php if (!$is_viewer): ?>
                                <button id="toggleFormBtn" class="btn btn-primary-themed" onclick="toggleForm()">
                                    <i class="fa fa-plus"></i> Add New Admin
                                </button>
                                <?php endif; ?>
                                <button onclick="window.location.href='add_users.php'" class="btn btn-secondary"><i class="fa fa-users"></i> Switch to Users</button>
                            </div>
                        </div>
                        
                        <?php if($session_success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($session_success); ?></div>
                        <?php endif; ?>
                        <?php if($session_error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($session_error); ?></div>
                        <?php endif; ?>

                        <?php if (!$is_viewer): ?>
                        <div class="section_content" id="adminForm" style="display: none;">
                             <?php if(!empty($form_errors)): ?>
                                <div class="alert alert-danger">
                                    <?php foreach($form_errors as $error): ?>
                                        <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <form action="add_admins.php" method="POST" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fullname" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter admin's full name" value="<?= htmlspecialchars($form_input['fullname'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter a username" value="<?= htmlspecialchars($form_input['username'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter admin's email" value="<?= htmlspecialchars($form_input['email'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter a secure password" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="Salary" class="form-label">Salary</label>
                                        <input type="number" step="0.01" class="form-control" id="Salary" name="Salary" placeholder="Enter admin's salary" value="<?= htmlspecialchars($form_input['Salary'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="Role" class="form-label">Role</label>
                                        <select class="form-select" id="Role" name="Role" required>
                                            <option value="">Select a role...</option>
                                            <option value="super_admin" <?= ($form_input['Role'] ?? '') == 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                                            <option value="admin" <?= ($form_input['Role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="stock_encoder" <?= ($form_input['Role'] ?? '') == 'stock_encoder' ? 'selected' : '' ?>>Stock Encoder</option>
                                            <option value="purchasing_officer" <?= ($form_input['Role'] ?? '') == 'purchasing_officer' ? 'selected' : '' ?>>Purchasing Officer</option>
                                            <option value="rewards_manager" <?= ($form_input['Role'] ?? '') == 'rewards_manager' ? 'selected' : '' ?>>Rewards Manager</option>
                                            <option value="viewer" <?= ($form_input['Role'] ?? '') == 'viewer' ? 'selected' : '' ?>>Viewer</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" name="add_admin" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Admin
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>

                        <div class="section_content">
                            <table id="adminsTable" class="table table-striped table-bordered table-themed" style="width:100%; table-layout: fixed;">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;">ID</th>
                                        <th style="width: 18%;">Full Name</th>
                                        <th style="width: 12%;">Username</th>
                                        <th style="width: 20%;">Email</th>
                                        <th style="width: 10%;">Salary</th>
                                        <th style="width: 12%;">Last Login</th>
                                        <th style="width: 8%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->query("SELECT * FROM admins ORDER BY admin_id DESC");
                                    while ($admin = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($admin['admin_id']) . "</td>";
                                        echo "<td class='expandable-text fullname-col'>" . htmlspecialchars($admin['fullname']) . "</td>";
                                        echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
                                        echo "<td class='expandable-text email-col'>" . htmlspecialchars($admin['email']) . "</td>";
                                        echo "<td>" . htmlspecialchars($admin['Salary']) . "</td>";
                                        $last_login_display = !empty($admin['last_login']) ? date('M d, Y g:i A', strtotime($admin['last_login'])) : 'Never';
                                        echo "<td>" . htmlspecialchars($last_login_display) . "</td>";
                                        echo '<td>';
                                        echo '<a href="update_admins.php?id=' . $admin['admin_id'] . '" class="btn-action-edit"><i class="fa-solid fa-pencil"></i> Edit</a> ';
                                        if ($admin['admin_id'] != $_SESSION['admins']['admin_id']) {
                                            echo '<a href="delete_admins.php?id=' . $admin['admin_id'] . '" class="btn-action-delete" onclick="return confirm(\'Are you sure you want to delete this admin?\');"><i class="fa fa-trash"></i> Delete</a>';
                                        }
                                        echo '</td>';
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function toggleForm() {
        var form = document.getElementById('adminForm');
        var btn = document.getElementById('toggleFormBtn');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            btn.innerHTML = '<i class="fa fa-minus"></i> Hide Form';
        } else {
            form.style.display = 'none';
            btn.innerHTML = '<i class="fa fa-plus"></i> Add New Admin';
        }
    }

    // Show form if there are error messages
    <?php if(!empty($form_errors) || $session_success): ?>
    document.addEventListener('DOMContentLoaded', function() {
        toggleForm();
    });
    <?php endif; ?>

    // Handle delete confirmation
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.deleteAdmin');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const adminId = this.dataset.id;
                const adminName = this.dataset.name;
                const selfDelete = this.dataset.self === 'true';

                if (selfDelete) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot Delete Own Account',
                        text: 'Super Admins cannot delete their own account.',
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete admin: ${adminName}. This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `delete_admins.php?id=${adminId}`;
                    }
                });
            });
        });

        // Custom validation script
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="add_admins.php"]');
            if(form){
                const emailInput = form.querySelector('#email');
                const salaryInput = form.querySelector('#Salary');
                const requiredInputs = form.querySelectorAll('input[required], select[required]');

                form.addEventListener('submit', function(e) {
                    // Check for empty required fields
                    for (const input of requiredInputs) {
                        if (!input.value.trim()) {
                            // Since password has no label, handle it separately
                            const fieldName = input.labels.length > 0 ? input.labels[0].textContent : 'Password';
                            alert(`Please fill out the ${fieldName} field.`);
                            input.focus();
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            return; // Stop after first error
                        }
                    }
                    
                    // Then, check email format
                    const emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    if (!emailRegex.test(String(emailInput.value).toLowerCase())) {
                        alert('Please enter a valid email address.');
                        emailInput.focus();
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return;
                    }
                }, true); // Use capture phase

                salaryInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
                });
            }
        });
    });
    </script>
    <script>
        $(document).ready(function() {
            $('#adminsTable').DataTable({
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"lB><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"rt>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fa fa-copy"></i> Copy',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv"></i> CSV',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel"></i> Excel',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf"></i> PDF',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        className: 'btn btn-sm btn-secondary'
                    }
                ],
                order: [[0, 'desc']], // Sort by ID descending
                columnDefs: [
                    { orderable: false, targets: [6] } // Disable sorting on "Actions" column
                ],
                initComplete: function() {
                    // Re-initialize expandable text after DataTable is loaded
                    if (typeof reinitializeExpandableText === 'function') {
                        reinitializeExpandableText();
                    }
                },
                drawCallback: function() {
                    // Re-initialize expandable text after each table redraw
                    if (typeof reinitializeExpandableText === 'function') {
                        reinitializeExpandableText();
                    }
                }
            });
        });
    </script>

    </div>
    
    <script>
</body>
</html>