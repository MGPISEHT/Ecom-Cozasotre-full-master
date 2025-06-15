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
    <title>Edit Category</title>
</head>
<body>
    <?php foreach ($categories as $row) { ?>
        <div class="modal fade" id="editeCategoryModal<?php echo htmlspecialchars($row['id'] ?? ''); ?>" tabindex="-1"
            role="dialog" aria-labelledby="editeCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editeCategoryModalLabel">Edit Category</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="container" style="height: 470px;">
                        <form action="function.php" method="post">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                            <!-- Category Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Category Title</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="<?= htmlspecialchars($row['title']) ?>" required>
                            </div>
                            <!-- Meta Keyword -->
                            <div class="mb-3">
                                <label for="metaKeyword" class="form-label">Meta Keyword</label>
                                <input type="text" class="form-control" id="metaKeyword" name="metaKeyword"
                                    value="<?= htmlspecialchars($row['meta_keyword'] ?? '') ?>">
                            </div>
                            <!-- Meta Title -->
                            <div class="mb-3">
                                <label for="metaTitle" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="metaTitle" name="metaTitle"
                                    value="<?= htmlspecialchars($row['meta_title'] ?? '') ?>">
                            </div>
                            <!-- Meta Description -->
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                    required><?= htmlspecialchars($row['meta_description']) ?></textarea>
                            </div>
                            <!-- Status Checkbox -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="categoryStatus" name="categoryStatus"
                                    <?= ($row['status']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="categoryStatus">Active</label>
                            </div>
                            <!-- Submit Button -->
                            <button type="submit" name="update-categories" class="btn btn-primary">Update Category</button>
                            <a class="btn btn-secondary" href="../viewCategories.php">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php include 'components/js.php'; ?>
</body>
</html>