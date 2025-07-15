<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: login.php");
    exit();
}

// Include these regardless of the alert
include 'server/api/get_items.php';
include 'server/db.php'; // Make sure this defines $conn

// Set timezone and define dates
date_default_timezone_set('Asia/Manila');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$yesterday = date('Y-m-d', strtotime('-1 day'));
$yesterday2 = date('Y-m-d', strtotime('-2 day'));

// Call data-fetching functions
$dueCount = getDueCount($conn);
$dueItems = getDueItems($conn, $tomorrow);
$actCount = getActCount($conn, $yesterday2);
$recentTransactions = getRecentTransactions($conn, $yesterday, $tomorrow);

// Handle profile update
$updateMessage = '';
$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($username) || empty($first_name) || empty($last_name)) {
        $updateMessage = 'All fields except password are required.';
    } else {
        // Check if username already exists (excluding current user)
        $checkStmt = $conn->prepare("SELECT user_id FROM user WHERE user_name = ? AND user_id != ?");
        $checkStmt->bind_param("si", $username, $user_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            $updateMessage = 'Username already exists. Please choose a different username.';
        } else {
            // If password change is requested
            if (!empty($new_password)) {
                if ($new_password !== $confirm_password) {
                    $updateMessage = 'New passwords do not match.';
                } else {
                    // Verify current password
                    $verifyStmt = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
                    $verifyStmt->bind_param("i", $user_id);
                    $verifyStmt->execute();
                    $verifyResult = $verifyStmt->get_result();
                    $userRow = $verifyResult->fetch_assoc();
                    
                    if (!password_verify($current_password, $userRow['password'])) {
                        $updateMessage = 'Current password is incorrect.';
                    } else {
                        // Update with new password
                        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                        $updateStmt = $conn->prepare("UPDATE user SET user_name = ?, f_name = ?, l_name = ?, password = ? WHERE user_id = ?");
                        $updateStmt->bind_param("ssssi", $username, $first_name, $last_name, $hashedPassword, $user_id);
                        
                        if ($updateStmt->execute()) {
                            $_SESSION['username'] = $username;
                            $_SESSION['fullname'] = $first_name . ' ' . $last_name;
                            $updateMessage = 'Profile updated successfully!';
                            $updateSuccess = true;
                        } else {
                            $updateMessage = 'Error updating profile. Please try again.';
                        }
                    }
                }
            } else {
                // Update without password change
                $updateStmt = $conn->prepare("UPDATE user SET user_name = ?, f_name = ?, l_name = ? WHERE user_id = ?");
                $updateStmt->bind_param("sssi", $username, $first_name, $last_name, $user_id);
                
                if ($updateStmt->execute()) {
                    $_SESSION['username'] = $username;
                    $_SESSION['fullname'] = $first_name . ' ' . $last_name;
                    $updateMessage = 'Profile updated successfully!';
                    $updateSuccess = true;
                } else {
                    $updateMessage = 'Error updating profile. Please try again.';
                }
            }
        }
    }
}

// Get current user data
$user_id = $_SESSION['user_id'];
$userStmt = $conn->prepare("SELECT user_name, f_name, l_name FROM user WHERE user_id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();
?>

<?php if (isset($_GET['success'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        <?php if ($_GET['success'] == 1): ?>
            alert("✅ Item added successfully!");
        <?php elseif ($_GET['success'] == 0): ?>
            alert("✅ Logged in successfully!");
        <?php elseif ($_GET['success'] == 2): ?>
            alert("✅ Item updated successfully!");
        <?php else: ?>
            alert("❌ Failed to complete the action. Please try again.");
        <?php endif; ?>
    });
</script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>InvManager - Profile</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <i class="fa fa-warehouse"></i>
                </div>
                <div class="sidebar-brand-text mx-3">InvManager</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-bars"></i>
                    <span>Items</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Inventory:</h6>
                        <a class="collapse-item" href="items.php">Items</a>
                        <a class="collapse-item" href="borrow.php">Borrow</a>
                        <a class="collapse-item" href="history.php">History</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">
            
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <?php if($dueCount > 0): ?>
                                <span class="badge badge-danger badge-counter"><?php echo "+$dueCount"; ?></span>
                                <?php endif; ?>
                            </a>

                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts 
                                </h6>

                                <?php if (!empty($dueItems['due_tomorrow']) || !empty($dueItems['overdue']) || !empty($dueItems['toBeReturn'])): ?>

                                    <?php foreach ($dueItems['overdue'] as $item): ?>
                                        <a class="dropdown-item d-flex align-items-center" href="borrow.php">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-danger">
                                                    <i class="fas fa-times text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="small text-gray-500"><?php echo htmlspecialchars($item['return_date']); ?> (Overdue)</div>
                                                <span class="font-weight-bold">
                                                    <?php echo htmlspecialchars($item['borrower_name']); ?> was supposed to return <?php echo htmlspecialchars($item['item_name']); ?>
                                                </span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>

                                    <?php foreach ($dueItems['toBeReturn'] as $item): ?>
                                        <a class="dropdown-item d-flex align-items-center" href="borrow.php">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-info">
                                                    <i class="fas fa-spinner text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="small text-gray-500"><?php echo htmlspecialchars($item['return_date']); ?> (Due)</div>
                                                <span class="font-weight-bold">
                                                    <?php echo htmlspecialchars($item['borrower_name']); ?> is Expected to return the <?php echo htmlspecialchars($item['item_name']); ?>
                                                </span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>

                                    <?php foreach ($dueItems['due_tomorrow'] as $item): ?>
                                        <a class="dropdown-item d-flex align-items-center" href="borrow.php">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-warning">
                                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="small text-gray-500"><?php echo htmlspecialchars($item['return_date']); ?> (Due Tomorrow)</div>
                                                <span class="font-weight-bold">
                                                    <?php echo htmlspecialchars($item['borrower_name']); ?> is due to return <?php echo htmlspecialchars($item['item_name']); ?>
                                                </span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>

                                    <br>
                                <?php else: ?>
                                    <p class="dropdown-item text-center small text-gray-500">Nothing to worry about</p>
                                <?php endif; ?>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                 <?php if($actCount > 0): ?>
                                <span class="badge badge-danger badge-counter"><?php echo"+$actCount"; ?></span>
                                <?php endif; ?>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="messagesDropdown">
                            <h6 class="dropdown-header">
                                Latest Activity
                            </h6>

                            <?php if (!empty($recentTransactions)): ?>
                                <?php foreach ($recentTransactions as $txn): ?>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div class="dropdown-list-image mr-3">
                                            <img class="rounded-circle" src="img/undraw_profile_1.svg" alt="...">
                                            <div class="status-indicator bg-success"></div>
                                        </div>
                                        <div class="font-weight-bold">
                                            <div class="text-truncate">
                                                <?php echo htmlspecialchars($txn['user']) . ' performed a ' . htmlspecialchars($txn['type']) . ' transaction.'; ?>
                                            </div>
                                            <div class="small text-gray-500">
                                                <?php echo date("F j, Y, g:i a", strtotime($txn['time'])); ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <a class="dropdown-item text-center small text-gray-500" href="history.php">No recent transactions</a>
                            <?php endif; ?>

                            <a class="dropdown-item text-center small text-gray-500" href="history.php">Read More Activities</a>
                        </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['fullname'] ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="Profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="history.php">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Profile</h1>
                    </div>

                    <!-- Profile Content -->
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Profile Information Card -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($updateMessage)): ?>
                                        <div class="alert alert-<?php echo $updateSuccess ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                            <?php echo htmlspecialchars($updateMessage); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" id="profileForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="username">Username</label>
                                                    <input type="text" class="form-control" id="username" name="username" 
                                                           value="<?php echo htmlspecialchars($userData['user_name']); ?>" 
                                                           readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="user_id">User ID</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_id); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="first_name">First Name</label>
                                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                                           value="<?php echo htmlspecialchars($userData['f_name']); ?>" 
                                                           readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="last_name">Last Name</label>
                                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                                           value="<?php echo htmlspecialchars($userData['l_name']); ?>" 
                                                           readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="passwordSection" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="current_password">Current Password</label>
                                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="new_password">New Password</label>
                                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="confirm_password">Confirm New Password</label>
                                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary" id="editBtn">Edit Profile</button>
                                            <button type="submit" class="btn btn-success" id="saveBtn" name="update_profile" style="display: none;">Save Changes</button>
                                            <button type="button" class="btn btn-secondary" id="cancelBtn" style="display: none;">Cancel</button>
                                            <button type="button" class="btn btn-warning" id="changePasswordBtn" style="display: none;">Change Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Profile Picture Card -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Profile Picture</h6>
                                </div>
                                <div class="card-body text-center">
                                    <img class="img-profile rounded-circle mb-3" src="img/undraw_profile.svg" style="width: 150px; height: 150px;">
                                    <h5 class="font-weight-bold"><?php echo htmlspecialchars($userData['f_name'] . ' ' . $userData['l_name']); ?></h5>
                                    <p class="text-muted">@<?php echo htmlspecialchars($userData['user_name']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End of Page Content -->

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <script>
        $(document).ready(function() {
            var originalValues = {};
            var isEditing = false;
            var isChangingPassword = false;

            // Store original values
            function storeOriginalValues() {
                originalValues = {
                    username: $('#username').val(),
                    first_name: $('#first_name').val(),
                    last_name: $('#last_name').val()
                };
            }

            // Restore original values
            function restoreOriginalValues() {
                $('#username').val(originalValues.username);
                $('#first_name').val(originalValues.first_name);
                $('#last_name').val(originalValues.last_name);
                $('#current_password').val('');
                $('#new_password').val('');
                $('#confirm_password').val('');
            }

            // Edit button click
            $('#editBtn').click(function() {
                storeOriginalValues();
                isEditing = true;
                
                // Enable form fields
                $('#username, #first_name, #last_name').prop('readonly', false);
                
                // Toggle buttons
                $('#editBtn').hide();
                $('#saveBtn, #cancelBtn, #changePasswordBtn').show();
            });

            // Cancel button click
            $('#cancelBtn').click(function() {
                restoreOriginalValues();
                isEditing = false;
                isChangingPassword = false;
                
                // Disable form fields
                $('#username, #first_name, #last_name').prop('readonly', true);
                
                // Toggle buttons
                $('#editBtn').show();
                $('#saveBtn, #cancelBtn, #changePasswordBtn').hide();
                $('#passwordSection').hide();
            });

            // Change password button click
            $('#changePasswordBtn').click(function() {
                isChangingPassword = true;
                $('#passwordSection').show();
                $(this).hide();
            });

            // Form validation
            $('#profileForm').submit(function(e) {
                var username = $('#username').val().trim();
                var firstName = $('#first_name').val().trim();
                var lastName = $('#last_name').val().trim();
                
                if (username === '' || firstName === '' || lastName === '') {
                    alert('Please fill in all required fields.');
                    e.preventDefault();
                    return false;
                }

                if (isChangingPassword) {
                    var currentPassword = $('#current_password').val();
                    var newPassword = $('#new_password').val();
                    var confirmPassword = $('#confirm_password').val();
                    
                    if (currentPassword === '' || newPassword === '' || confirmPassword === '') {
                        alert('Please fill in all password fields.');
                        e.preventDefault();
                        return false;
                    }
                    
                    if (newPassword !== confirmPassword) {
                        alert('New passwords do not match.');
                        e.preventDefault();
                        return false;
                    }
                    
                    if (newPassword.length < 6) {
                        alert('New password must be at least 6 characters long.');
                        e.preventDefault();
                        return false;
                    }
                }
                
                return true;
            });
        });
    </script>

</body>

</html>