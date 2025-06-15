<!doctype html>
<html lang="en">
<?php include 'components/head.php'; ?>
<title>Add Category</title>

<body>
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card">
                    <div class="card mb-0">
                        <div class="card-body">
                            <?php if (isset($_GET['message'])): ?>
                                <div class="alert alert-success"><?= htmlspecialchars($_GET['message']) ?></div>
                            <?php endif; ?>

                            <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                            <?php endif; ?>
                            <form action="function.php" method="post" enctype="multipart/form-data">
                                <!-- Category Title -->
                                <div class="mb-3">
                                    <label for="categoryTitle" class="form-label">Category Title</label>
                                    <input type="text" class="form-control" id="categoryTitle" name="categoryTitle"
                                        placeholder="Enter category title" required>
                                </div>
                                <!-- Meta Keyword -->
                                <div class="mb-3">
                                    <label for="metaKeyword" class="form-label">Meta Keyword</label>
                                    <input type="text" class="form-control" id="metaKeyword" name="metaKeyword"
                                        placeholder="Enter meta keyword">
                                </div>
                                <!-- Meta Title -->
                                <div class="mb-3">
                                    <label for="metaTitle" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="metaTitle" name="metaTitle"
                                        placeholder="Enter meta title">
                                </div>
                                <!-- Meta Description -->
                                <div class="mb-3">
                                    <label for="metaDescription" class="form-label">Meta Description</label>
                                    <textarea class="form-control" id="metaDescription" name="metaDescription" rows="3"
                                        placeholder="Enter meta description"></textarea>
                                </div>
                                <!-- Status Toggle -->
                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="categoryStatus"
                                        name="categoryStatus" checked>
                                    <label class="form-check-label" for="categoryStatus">Active</label>
                                </div>
                                <!-- Submit Button -->
                                <button type="submit" name="add-categories" class="btn btn-primary">Add
                                    Category</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'components/js.php'; ?>
</body>

</html>