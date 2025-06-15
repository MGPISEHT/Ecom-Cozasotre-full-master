<!doctype html>
<html lang="en">

<?php include 'components/head.php'; ?>
<title>Add Orders</title>

<body>
    <div class="modal fade" id="addModalCustomer" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">X</span>
                    </button>
                </div>
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <strong>Hey!</strong> <?= $_SESSION['message'] ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                                <?php unset($_SESSION['message']); ?>
                            <?php endif; ?>

                            <div class="card mb-0">
                                <div class="container-fluid">
                                    <form action="function.php" method="post">
                                        <!-- Customer Name -->
                                        <div class="mb-3">
                                            <label for="cusName" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="cusName" name="cusName"
                                                required>
                                        </div>
                                        <!-- Customer Email -->
                                        <div class="mb-3">
                                            <label for="cusEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="cusEmail" name="cusEmail"
                                                required>
                                        </div>
                                        <!-- Customer Phone -->
                                        <div class="mb-3">
                                            <label for="cusPhone" class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="cusPhone" name="cusPhone"
                                                required>
                                        </div>
                                        <!-- Customer Password -->
                                        <div class="mb-3">
                                            <label for="cusPassword" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="cusPassword"
                                                name="cusPassword" required>
                                        </div>
                                        <!-- Customer Address -->
                                        <div class="mb-3">
                                            <label for="cusAddress" class="form-label">Shipping Address</label>
                                            <textarea class="form-control" id="cusAddress" name="cusAddress" rows="3"
                                                required></textarea>
                                        </div>
                                        <!-- Submit Button -->
                                        <button type="submit" name="add-customer" class="btn btn-primary">Add
                                            Customer</button>
                                        <a href="viewCustomer.php" class="btn btn-secondary">Cancel</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>