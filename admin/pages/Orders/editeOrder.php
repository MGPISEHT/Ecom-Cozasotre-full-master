<?php
try {
    $stmt = $conn->prepare("SELECT * FROM order_items");
    $stmt->execute();
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching order items for edit modal: " . $e->getMessage();
    $orderItems = []; 
}
?>

<!doctype html>
<html lang="en">

<?php include 'components/head.php'; ?>
<title>Edit Orders</title>

<body>
    <?php foreach ($orderItems as $item): ?>
        <div class="modal fade" id="editModalOrder<?php echo htmlspecialchars($item['item_id'] ?? ''); ?>" tabindex="-1" role="dialog" aria-labelledby="editeModalOrderLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editeModalOrderLabel">Edit Order (ID: <?php echo htmlspecialchars($item['item_id'] ?? ''); ?>)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">X</span>
                        </button>
                    </div>

                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                if (isset($_SESSION['message'])) {
                                ?>
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <strong>Hey!</strong> <?= $_SESSION['message'] ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php
                                    unset($_SESSION['message']);
                                }
                                ?>
                                <div class="card mb-0">
                                    <div class="container-fluid">
                                        <form action="updateOrder.php" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id'] ?? ''); ?>">

                                            <div class="mb-3">
                                                <label for="order_id_<?php echo htmlspecialchars($item['item_id']); ?>" class="form-label">Order ID</label>
                                                <input type="text" class="form-control" id="order_id_<?php echo htmlspecialchars($item['item_id']); ?>" name="order_id"
                                                    value="<?php echo htmlspecialchars($item['order_id'] ?? ''); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="product_name_<?php echo htmlspecialchars($item['item_id']); ?>" class="form-label">Product Name</label>
                                                <input type="text" class="form-control" id="product_name_<?php echo htmlspecialchars($item['item_id']); ?>" name="product_name"
                                                    value="<?php echo htmlspecialchars($item['product_name'] ?? ''); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="quantity_<?php echo htmlspecialchars($item['item_id']); ?>" class="form-label">Quantity</label>
                                                <input type="number" step="1" class="form-control" id="quantity_<?php echo htmlspecialchars($item['item_id']); ?>" name="quantity"
                                                    value="<?php echo htmlspecialchars($item['quantity'] ?? ''); ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="price_<?php echo htmlspecialchars($item['item_id']); ?>" class="form-label">Price</label>
                                                <input type="number" step="0.01" class="form-control" id="price_<?php echo htmlspecialchars($item['item_id']); ?>" name="price"
                                                    value="<?php echo htmlspecialchars($item['price'] ?? ''); ?>" required>
                                            </div>

                                            <button type="submit" name="update-order" class="btn btn-primary">Update Order</button>
                                            <a aria-hidden="true" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</a>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</body>

</html>