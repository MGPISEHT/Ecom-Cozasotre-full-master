<?php
$stmt = $conn->prepare("SELECT * FROM customers");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!doctype html>
<html lang="en">

<?php include 'components/head.php'; ?>
<title>Add Customer</title>

<body>
    <?php foreach ($customers as $key): ?>
        <div class="modal fade" id="editModalCustomer<?= htmlspecialchars($key['id']) ?>" tabindex="-1" role="dialog"
            aria-labelledby="editModalLabel<?= $customer['id'] ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="function.php" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Customer</h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>

                        </div>
                        <div class="modal-body p-3">
                            <input type="hidden" name="customer_id" value="<?= htmlspecialchars($key['id']) ?>">

                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="cusName"
                                    value="<?= htmlspecialchars($key['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="cusEmail"
                                    value="<?= htmlspecialchars($key['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="cusPhone"
                                    value="<?= htmlspecialchars($key['phone']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="cusPassword"
                                    placeholder="Leave blank to keep current password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shipping Address</label>
                                <textarea class="form-control" name="cusAddress"
                                    required><?= htmlspecialchars($key['shipping_address']) ?></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="update-customer" class="btn btn-primary">Update</button>
                                <!-- <a href="function.php?delete_customer=<?= $key['id'] ?>"
                                    onclick="return confirm('Delete this customer?')" class="btn btn-danger">Delete</a> -->
                                <a href="viewCustomer.php" class="btn btn-secondary">Cancel</a>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</body>

</html>