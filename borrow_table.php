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
    <title>Borrowed Items Management</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

<?php
    include 'server/api/get_borrowed_items.php';
    $borrowedItems = get_borrowed_items();
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- Borrowed Items Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Borrowed Items</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Borrower</th>
                            <th>Borrowed Quantity (remaining)</th>
                            <th>Returned Quantity</th>
                            <th>Borrow Date</th>
                            <th>Expected Return</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($borrowedItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td><?= htmlspecialchars($item['borrower_name']) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td><?= htmlspecialchars($item['returned_quantity']) ?></td>
                                <td><?= htmlspecialchars($item['borrow_date']) ?></td>
                                <td><?= htmlspecialchars($item['return_date']) ?></td>
                                <td><?= htmlspecialchars($item['status']) ?></td>
                                <td>
                                    <?php if ($item['status'] !== 'Returned'): ?>
                                        <button class="btn btn-primary btn-sm return-btn" 
                                                data-toggle="modal" 
                                                data-target="#returnModal"
                                                data-borrow-id="<?= $item['borrow_id'] ?>"
                                                data-item-id="<?= $item['item_id'] ?>"
                                                data-item-name="<?= htmlspecialchars($item['item_name']) ?>"
                                                data-borrower-name="<?= htmlspecialchars($item['borrower_name']) ?>"
                                                data-borrowed-quantity="<?= $item['quantity'] ?>">
                                            <i class="fas fa-undo"></i> Return
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">Completed</span>
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

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <form id="returnForm" action="server/api/process_return.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Process Item Return</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <div class="modal-body">
          <input type="hidden" name="borrow_id" id="return_borrow_id">
          <input type="hidden" name="item_id" id="return_item_id">
          
          <div class="mb-3">
            <label>Item Name</label>
            <input type="text" class="form-control" id="return_item_name" readonly>
          </div>
          
          <div class="mb-3">
            <label>Borrower Name</label>
            <input type="text" class="form-control" id="return_borrower_name" readonly>
          </div>
          
          <div class="mb-3">
            <label>Borrowed Quantity</label>
            <input type="number" class="form-control" id="return_borrowed_quantity" readonly>
          </div>
          
          <div class="mb-3">
            <label for="return_type">Return Type</label>
            <select class="form-control" name="return_type" id="return_type" required>
              <option value="">Select return type</option>
              <option value="full">Full Return</option>
              <option value="partial">Partial Return</option>
            </select>
          </div>
          
          <div class="mb-3" id="returned_quantity_container" style="display: none;">
            <label for="returned_quantity">Returned Quantity</label>
            <input type="number" class="form-control" name="returned_quantity" id="returned_quantity" min="1">
          </div>
          
          <div class="mb-3">
            <label for="return_condition">Item Condition</label>
            <select class="form-control" name="return_condition" id="return_condition" required>
              <option value="">Select condition</option>
              <option value="Excellent">Excellent (No issues)</option>
              <option value="Good">Good (Minor wear)</option>
              <option value="Fair">Fair (Noticeable wear but functional)</option>
              <option value="Poor">Poor (Damaged but repairable)</option>
              <option value="Broken">Broken (Needs replacement)</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label for="return_notes">Notes</label>
            <textarea class="form-control" name="return_notes" id="return_notes" rows="3" placeholder="Any additional notes about the returned item..."></textarea>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Process Return</button>
        </div>
      </form>
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
<script src="js/demo/datatables-demo.js"></script>

<!-- Modal Script -->
<script>
    $('#returnModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const modal = $(this);
        
        modal.find('#return_borrow_id').val(button.data('borrow-id'));
        modal.find('#return_item_id').val(button.data('item-id'));
        modal.find('#return_item_name').val(button.data('item-name'));
        modal.find('#return_borrower_name').val(button.data('borrower-name'));
        modal.find('#return_borrowed_quantity').val(button.data('borrowed-quantity'));
        modal.find('#returned_quantity').attr('max', button.data('borrowed-quantity'));
    });
    
    $('#return_type').change(function() {
        const returnType = $(this).val();
        const quantityContainer = $('#returned_quantity_container');
        const returnedQuantity = $('#returned_quantity');
        
        if (returnType === 'partial') {
            quantityContainer.show();
            returnedQuantity.prop('required', true);
        } else {
            quantityContainer.hide();
            returnedQuantity.prop('required', false);
            returnedQuantity.val('');
        }
    });
</script>

</body>
</html>