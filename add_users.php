<?php
session_start(); // Ensure session is started at the very top

// Check if admin is logged in
if (!isset($_SESSION['admins'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if admin has admin role or higher
$allowed_roles = ['admin', 'super_admin'];
if (!in_array($_SESSION['admins']['role'], $allowed_roles)) {
    $_SESSION['error_message'] = "Access denied. Only Admins and above can manage users.";
    header("Location: admin_dashboard.php");
    exit();
}

// Include database connection
include_once('connection.php');

$session_success = $_SESSION['success_message'] ?? null;
$session_error = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Handle form submission
if(isset($_POST['add_user'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $birthdate = $_POST['birthdate'];
    
    // Basic validation
    if(empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password) || empty($birthdate)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: add_users.php");
        exit();
    }

    try {
        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check_stmt->execute([$email]);
        
        if($check_stmt->rowCount() > 0) {
            $_SESSION['error_message'] = "Email already exists.";
            header("Location: add_users.php");
            exit();
        }
        
        // Check if username already exists
        $check_username_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $check_username_stmt->execute([$username]);
        
        if($check_username_stmt->rowCount() > 0) {
            $_SESSION['error_message'] = "Username already exists.";
            header("Location: add_users.php");
            exit();
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user with correct column names
        $insert_stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, email, password, birthdate, tier, points, coins, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 'Fresh Ink', 100, 0, NOW(), NOW())");
        $insert_stmt->execute([$firstname, $lastname, $username, $email, $hashed_password, $birthdate]);
        
        $_SESSION['success_message'] = "User added successfully!";
        header("Location: add_users.php");
        exit();
        
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header("Location: add_users.php");
        exit();
    }
}

$is_viewer = ($_SESSION['admins']['role'] ?? '') === 'viewer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Management</title>

    <script src="https://kit.fontawesome.com/4e3dcd3b49.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css?v=4"> 
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css"/>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <style>
        /* Modal styling */
        #userInfoModal .modal-body {
            padding: 0;
        }
        .details-separator {
            padding: 1.5rem 2rem;
            background: linear-gradient(to right, #eef5f9, #f8fafc);
            border-left: 6px solid var(--theme-color);
        }
        .details-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .details-icon {
            flex-shrink: 0;
        }
        .details-info {
            flex-grow: 1;
        }
        .details-title {
            font-weight: 700;
            font-size: 1.5rem;
            color: #1a3b5a;
            margin-bottom: 0.75rem;
        }
        .summary-list {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
        }
        .summary-item {
            font-size: 0.9rem;
            color: #34495e;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
                            <h2 class="section_header mb-0"><i class="fa fa-users"></i> User Management</h2>
                            <div>
                                <button type="button" class="btn btn-primary-themed" onclick="toggleForm()" id="toggleFormBtn">
                                    <i class="fa fa-plus"></i> Add New User
                                </button>
                                <button onclick="window.location.href='add_admins.php'" class="btn btn-secondary"><i class="fa fa-user-shield"></i> Switch to Admins</button>
                            </div>
                        </div>

                        <?php if($session_success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($session_success); ?></div>
                        <?php endif; ?>
                        <?php if($session_error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($session_error); ?></div>
                        <?php endif; ?>

                        <div class="section_content" id="userForm" style="display:none;">
                            <?php if (!$is_viewer): ?>
                            <form action="add_users.php" method="POST" class="appForm" novalidate>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="firstname" class="form-label">First Name</label>
                                            <input type="text" id="firstname" name="firstname" class="form-control" placeholder="Enter first name..." value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="lastname" class="form-label">Last Name</label>
                                            <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Enter last name..." value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" id="username" name="username" class="form-control" placeholder="Enter username..." value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter email..." value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter password..." required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="birthdate" class="form-label">Birthdate</label>
                                            <input type="date" id="birthdate" name="birthdate" class="form-control" value="<?php echo htmlspecialchars($_POST['birthdate'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" name="add_user" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add User
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>

                        <div class="section_content">
                            <table id="usersTable" class="table table-striped table-bordered table-themed" style="width:100%; table-layout: fixed;">
                                <thead>
                                    <tr>
                                        <th style="width: 6%;">ID</th>
                                        <th style="width: 12%;">Full Name</th>
                                        <th style="width: 10%;">Username</th>
                                        <th style="width: 18%;">Email</th>
                                        <th style="width: 8%;">Coins</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 12%;">Last Login</th>
                                        <th style="width: 6%;">More Info</th>
                                        <th style="width: 8%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
                                        $stmt->execute();
                                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        if(count($result) > 0){
                                            foreach($result as $row){
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['coins'] ?? '0'); ?></td>
                                        <td><?php echo htmlspecialchars($row['tier'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['last_login'] ?? 'N/A'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info toggle-info-btn" 
                                                    data-username="<?php echo htmlspecialchars($row['username']); ?>"
                                                    data-created-at="<?php echo htmlspecialchars($row['created_at']); ?>"
                                                    data-updated-at="<?php echo htmlspecialchars($row['updated_at']); ?>">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <?php if (!$is_viewer): ?>
                                            <a href="update_users.php?id=<?php echo $row['user_id']; ?>" class="btn-action-edit"><i class="fa-solid fa-pencil"></i> Edit</a>
                                            <a href="delete_users.php?id=<?php echo $row['user_id']; ?>" class="btn-action-delete" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-trash"></i> Delete</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php }} else { ?>
                                        <tr>
                                            <td colspan="9" style="text-align: center;">No users found</td>
                                        </tr>
                                    <?php } } catch(PDOException $e) { ?>
                                        <tr>
                                            <td colspan="9" style="text-align: center; color: red;">Error loading users: <?php echo htmlspecialchars($e->getMessage()); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- More Info Modal -->
    <div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userInfoModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be injected by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            var form = document.getElementById('userForm');
            var btn = document.getElementById('toggleFormBtn');
            if (form.style.display === 'none') {
                form.style.display = 'block';
                btn.innerHTML = '<i class="fa fa-minus"></i> Hide Form';
            } else {
                form.style.display = 'none';
                btn.innerHTML = '<i class="fa fa-plus"></i> Add New User';
            }
        }
        
        <?php if($session_error || $session_success): ?>
        document.addEventListener('DOMContentLoaded', function() {
            toggleForm();
        });
        <?php endif; ?>
        
        // Custom validation script
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="add_users.php"]');
            if(form){
                const emailInput = form.querySelector('#email');

                form.addEventListener('submit', function(e) {
                    const emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    if (!emailRegex.test(String(emailInput.value).toLowerCase())) {
                        alert('Please enter a valid email address.');
                        emailInput.focus();
                        e.preventDefault();
                        e.stopImmediatePropagation();
                    }
                }, true);
            }
        });
    </script>
    <script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            responsive: true,
            dom: '<"row"<"col-sm-12 col-md-6"lB><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"rt>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                { extend: 'copy', text: '<i class="fa fa-copy"></i> Copy', className: 'btn-secondary-themed' },
                { extend: 'csv', text: '<i class="fa fa-file-csv"></i> CSV', className: 'btn-secondary-themed' },
                { extend: 'excel', text: '<i class="fa fa-file-excel"></i> Excel', className: 'btn-secondary-themed' },
                { extend: 'pdf', text: '<i class="fa fa-file-pdf"></i> PDF', className: 'btn-secondary-themed' },
                { extend: 'print', text: '<i class="fa fa-print"></i> Print', className: 'btn-secondary-themed' }
            ],
            order: [[0, 'desc']], // Default sort by the first column (ID) descending
            columnDefs: [
                { "orderable": false, "targets": [7, 8] } 
            ]
        });

        // Modal script
        $('#usersTable tbody').on('click', '.toggle-info-btn', function () {
            var button = $(this);
            var username = button.data('username');
            var createdAtRaw = button.data('created-at');
            var updatedAtRaw = button.data('updated-at');

            var createdDate = new Date(createdAtRaw).toLocaleDateString('en-US');
            var updatedDate = new Date(updatedAtRaw).toLocaleDateString('en-US');

            var modalTitle = $('#userInfoModal .modal-title');
            modalTitle.text('Details for ' + username);

            var modalBody = $('#userInfoModal .modal-body');

            const iconStyles = `
                font-size: 2rem; color: #ffffff; background: linear-gradient(45deg, var(--theme-color), #17a2b8);
                padding: 1rem; border-radius: 50%; display: flex; align-items: center; justify-content: center;
                width: 70px; height: 70px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                border: 2px solid rgba(255, 255, 255, 0.3);
            `;

            var modalContent = `
            <div class="details-separator">
                <div class="details-header">
                    <div class="details-icon" style="${iconStyles}">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="details-info">
                        <div class="details-title">${username}</div>
                        <div class="summary-list">
                            <span class="summary-item"><i class="fa fa-calendar-plus"></i> <strong>Created:</strong>&nbsp;${createdDate}</span>
                            <span class="summary-item"><i class="fa fa-calendar-check"></i> <strong>Updated:</strong>&nbsp;${updatedDate}</span>
                        </div>
                    </div>
                </div>
            </div>
            `;
            
            modalBody.html(modalContent);
            
            var myModal = new bootstrap.Modal(document.getElementById('userInfoModal'));
            myModal.show();
        });
    });
    </script>
</body>
</html>