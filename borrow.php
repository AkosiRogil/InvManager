<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Redirect to login if not logged in
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: login.php");
    exit();
}
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
$actCount = getActCount($conn);
$recentTransactions = getRecentTransactions($conn);

if (isset($_GET['success'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        <?php if ($_GET['success'] == 1): ?>
            alert("✅ Item borrrowed successfully!");
        <?php else: ?>
            alert("❌ Failed to borrow item. Please try again.");
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

    <title>SB Admin 2 - Dashboard</title>

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
                <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Inventory:</h6>
                        <a class="collapse-item" href="items.php">Items</a>
                        <a class="collapse-item active" href="borrow.php">Borrow</a>
                        <a class="collapse-item" href="history.php">History</a>
                    </div>
                </div>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">


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
                    <div class="card">
                        <div class="card-header">
                            ITEMS
                        </div>
                        <div class="card-body">
                            <?php include __DIR__ . '/borrow_table.php'; ?>

                        </div>
                        </div>

                    

            </div>
            <!-- End of Main Content -->
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

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

    
        <script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- DataTables JavaScript -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

</body>

</html>