<?php

$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
</head>

<body>
    <div>
        <?php foreach ($products as $product) { ?>
            <div class="modal fade" id="editModal<?php echo htmlspecialchars($product['id'] ?? ''); ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container">

                                <form action="function.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="product_id" 
                                    value="<?php echo htmlspecialchars($product['id'] ?? ''); ?>">

                                    <div class="mb-3">
                                        <label for="current_image">Current Image:</label>
                                        <div>
                                            <img width="50" src="<?php echo htmlspecialchars($product['image'] ?? ''); ?>"
                                                alt="Product Image" style="max-width: 150px; height: auto; margin-bottom: 5px;">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="proImage" class="form-label">Product Image Update (Optional)</label>
                                        <input type="file" class="form-control" id="proImage" name="proImage" accept="image/*">
                                        <small class="form-text text-muted">Select a new image to update (optional).</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="name">Name:</label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description">Description:</label>
                                        <textarea id="description" name="description" class="form-control" required>
                                            <?php echo htmlspecialchars($product['description'] ?? ''); ?>
                                        </textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="price">Price:</label>
                                        <input type="number" id="price" name="price" class="form-control" 
                                        value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" 
                                        step="0.01" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="stock_quantity">Stock:</label>
                                        <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" 
                                        value="<?php echo htmlspecialchars($product['stock_quantity'] ?? ''); ?>" 
                                        step="1" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="category_id">Category:</label>
                                        <select id="category_id" name="category_id" class="form-control" required>
                                            <?php foreach ($categories as $category) { ?>
                                                <option 
                                                    value="<?php echo $category['id']; ?>" <?php 
                                                    echo ($category['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['title']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>