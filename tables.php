<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: login.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SB Admin 2 - Tables</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .dataTables_length {
            display: none; /* Hide built-in length filter if needed */
        }
    </style>
</head>

<body id="page-top">

<?php

    include 'server/api/get_items.php';
    $items = get_item();
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between flex-wrap">
            <h6 class="m-0 font-weight-bold text-primary">Items</h6>
            <div class="d-flex flex-wrap gap-2">
                <!-- Search Field -->
                <div class="input-group mr-2">
                    <input type="text" id="customSearch" class="form-control bg-light border-0 small" placeholder="Search...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableCustom" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Available</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $item): ?>
                          <tr>
                              <td><?= htmlspecialchars($item['item_name']) ?></td>
                              <td><?= htmlspecialchars($item['category']) ?></td>
                              <td><?= htmlspecialchars($item['total_quantity']) ?></td>
                              <td><?= htmlspecialchars($item['available']) ?></td>
                              <td><?= htmlspecialchars($item['description']) ?></td>
                              <td><?= htmlspecialchars($item['status']) ?></td>
                              <td>
                                  <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal"
                                          data-item='<?= json_encode($item) ?>'>
                                      <i class="fas fa-eye"></i>
                                  </button>


                                  <?php if ($item['status'] !== 'Broken'): ?>

                                      <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal"
                                          data-item='<?= json_encode($item) ?>'>
                                      <i class="fas fa-edit"></i>
                                      </button>

                                      <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#borrowModal"
                                              data-item='<?= json_encode($item) ?>'>
                                          <i class="fas fa-hand-paper"></i>
                                      </button>
                                      

                                      <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#brokenModal"
                                              data-item='<?= json_encode($item) ?>'>
                                          <i class="fas fa-minus"></i>
                                      </button>
                                  <?php endif; ?>
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
                </table>
            </div>  
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">View Item</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p><strong>Name:</strong> <span id="view_name"></span></p>
        <p><strong>Category:</strong> <span id="view_category"></span></p>
        <p><strong>Quantity:</strong> <span id="view_quantity"></span></p>
        <p><strong>Type:</strong> <span id="view_type"></span></p>
        <p><strong>Status:</strong> <span id="view_status"></span></p>
        <p><strong>Description:</strong> <span id="view_description"></span></p>
        <p><strong>Added at:</strong> <span id="added_at"></span></p>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      
      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title">Edit Item</h5>
        <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>


      
      <!-- Modal Body -->
      <div class="modal-body">
        <form id="editForm" action="server/api/update_item.php" method="POST">
          <input type="hidden" name="item_id" id="edit_item_id">

          <div class="mb-3">
            <label for="edit_item_name">Item Name</label>
            <input type="text" class="form-control" name="item_name" id="edit_item_name" required>
          </div>

          <div class="mb-3">
            <label for="edit_category">Category</label>
            <input type="text" class="form-control" name="category" id="edit_category" required>
          </div>

          <div class="mb-3">
            <label for="edit_description">Description</label>
            <textarea class="form-control" name="description" id="edit_description"></textarea>
          </div>

          <div class="mb-3">
            <label for="edit_total_quantity">Total Quantity</label>
            <input type="number" class="form-control" name="total_quantity" id="edit_total_quantity" required>
          </div>

          <div class="mb-3">
            <label for="edit_status">Status</label>
            <input type="text" class="form-control" name="status" id="edit_status" required>
          </div>

          <div class="mb-3">
            <label for="edit_cost_price">Cost Price</label>
            <input type="number" step="0.01" class="form-control" name="cost_price" id="edit_cost_price">
          </div>

          <div class="mb-3">
            <label for="edit_low_stock_threshold">Low Stock Threshold</label>
            <input type="number" class="form-control" name="low_stock_threshold" id="edit_low_stock_threshold">
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>

          </div>
        </form>

      </div>

    </div>
  </div>
</div>

<!-- Borrow Modal -->
<div class="modal fade" id="borrowModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">

      <form id="borrowForm" action="server/api/borrow_item.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Borrow Item</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="item_id" id="borrow_item_id">
          <input type="hidden" name="available_quantity" id="borrow_available_quantity">

          <div class="mb-3">
            <label for="borrower_name">Borrower Name</label>
            <input type="text" class="form-control" name="borrower_name" required>
          </div>

          <div class="mb-3">
            <label for="borrow_quantity">Quantity to Borrow</label>
            <input type="number" class="form-control" name="borrow_quantity" id="borrow_quantity" min="1" required>
          </div>

          <div class="mb-3">
            <label for="return_date">Expected Return Date</label>
            <input type="date" class="form-control" name="return_date" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Confirm Borrow</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>

      </form>
    </div>
  </div>
</div>


 <!-- Broken Modal -->
<div class="modal fade" id="brokenModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Report Broken Item</h5>
        <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <form id="brokenForm" action="server/api/broken_item.php" method="POST">
          <input type="hidden" name="item_id" id="broken_item_id">
          <input type="hidden" name="available_quantity" id="broken_available_quantity">
          
          <div class="mb-3">
            <label for="broken_item_name">Item Name</label>
            <input type="text" class="form-control" id="broken_item_name" readonly>
          </div>
          
          <div class="mb-3">
            <label for="broken_quantity">Quantity Broken</label>
            <input type="number" class="form-control" name="broken_quantity" id="broken_quantity" min="1" required>
            <small class="text-muted">Enter how many items are broken</small>
            <div id="broken-warning" class="text-danger" style="display: none;">
              Warning: Broken quantity exceeds available items!
            </div>
          </div>
          
          <div class="mb-3">
            <label for="broken_notes">Notes (Optional)</label>
            <textarea class="form-control" name="broken_notes" id="broken_notes" placeholder="Describe the damage or issue" required></textarea>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Report Broken</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script>
function enableBrokenMode() {
    // Disable all main inputs
    const fieldsToDisable = [
        'item_name', 'category', 'description',
        'total_quantity', 'status', 'cost_price',
        'low_stock_threshold'
    ];

    fieldsToDisable.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.disabled = true;
    });

    // Show the broken quantity form
    document.getElementById('brokenForm').style.display = 'block';
}

function checkBrokenLimit() {
    const available = parseInt(document.getElementById('available_quantity').value);
    const broken = parseInt(document.getElementById('broken_quantity').value);
    const warning = document.getElementById('broken-warning');

    if (broken > available) {
        warning.style.display = 'block';
    } else {
        warning.style.display = 'none';
    }
}
</script>



<script>
    $(document).ready(function () {
        const table = $('#dataTableCustom').DataTable({
            "paging": true,
            "info": true,
            "lengthChange": true,
            "dom": 'lrtip' // hides default search box
        });

        // Custom search
        $('#customSearch').on('keyup', function () {
            table.search(this.value).draw();
        });

        // Generate column toggle checkboxes
        table.columns().every(function (index) {
            const column = this;
            const title = $(column.header()).text();

            const checkbox = $(`<div class="dropdown-item">
                <input type="checkbox" class="mr-2 column-toggle" data-column="${index}" checked> ${title}
            </div>`);
            $('#columnFilter').append(checkbox);
        });

        // Handle column toggle
        $('#columnFilter').on('change', '.column-toggle', function () {
            const column = table.column($(this).data('column'));
            column.visible(!column.visible());
        });
    });

    // Modal Scripts (same as your original)
    $('#viewModal').on('show.bs.modal', function (event) {
        const item = $(event.relatedTarget).data('item');
        $('#view_name').text(item.item_name);
        $('#view_category').text(item.category);
        $('#view_quantity').text(item.total_quantity);
        $('#view_type').text(item.type);
        $('#view_status').text(item.status);
        $('#view_description').text(item.description || 'N/A');
        $('#added_at').text(item.created_at || 'N/A');
    });

    $('#editModal').on('show.bs.modal', function (event) {
        const item = $(event.relatedTarget).data('item');
        $('#edit_item_id').val(item.item_id);
        $('#edit_item_name').val(item.item_name);
        $('#edit_category').val(item.category);
        $('#edit_description').val(item.description);
        $('#edit_total_quantity').val(item.total_quantity);
        $('#edit_status').val(item.status);
        $('#edit_cost_price').val(item.cost_price);
        $('#edit_low_stock_threshold').val(item.low_stock_threshold);
    });

    $('#borrowModal').on('show.bs.modal', function (event) {
        const item = $(event.relatedTarget).data('item');
        $('#borrow_item_id').val(item.item_id);
        $('#borrow_available_quantity').val(item.total_quantity);
        $('#borrow_quantity').attr('max', item.total_quantity);
    });

    $('#brokenModal').on('show.bs.modal', function (event) {
    const item = $(event.relatedTarget).data('item');
    $('#broken_item_id').val(item.item_id);
    $('#broken_item_name').val(item.item_name);
    $('#broken_available_quantity').val(item.available);
    $('#broken_quantity').attr('max', item.available);
    
    // Reset warning and form
    $('#broken-warning').hide();
    $('#broken_quantity').val(1);
    $('#broken_notes').val('');
});

// Add this to your existing script for checking broken quantity
$(document).on('input', '#broken_quantity', function() {
    const available = parseInt($('#broken_available_quantity').val());
    const broken = parseInt($(this).val());
    const warning = $('#broken-warning');
    
    if (broken > available) {
        warning.show();
    } else {
        warning.hide();
    }
});
</script>
<!-- Required for DataTables -->
<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>


</body>
</html>
