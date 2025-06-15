<?php
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Users</title>
</head>

<body>

    <?php foreach ($users as $user): ?>
        <div class="modal fade" id="editUserModal<?php echo htmlspecialchars($user['id']); ?>" tabindex="-1" role="dialog"
            aria-labelledby="editUserModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel<?php echo $user['id']; ?>">
                            Edit User ID: <?php echo htmlspecialchars($user['id']); ?>
                        </h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form action="function.php" method="post">
                            <!-- Hidden User ID -->
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username_<?php echo $user['id']; ?>" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username_<?php echo $user['id']; ?>"
                                    name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email_<?php echo $user['id']; ?>" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email_<?php echo $user['id']; ?>" name="email"
                                    value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="phone_<?php echo $user['id']; ?>" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone_<?php echo $user['id']; ?>" name="phone"
                                    value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                            </div>
                            <!-- Role -->
                            <div class="mb-3">
                                <label for="role_<?php echo $user['id']; ?>" class="form-label">Role</label>
                                <select class="form-control" id="role_<?php echo $user['id']; ?>" name="role" required>
                                    <option disabled>-- Choose a Role --</option>
                                    <option value="Admin" <?php echo ($user['role'] === 'Admin') ? 'selected' : ''; ?>>Admin
                                    </option>
                                    <option value="User" <?php echo ($user['role'] === 'User') ? 'selected' : ''; ?>>User
                                    </option>
                                    <option value="Editor" <?php echo ($user['role'] === 'Editor') ? 'selected' : ''; ?>>
                                        Editor</option>
                                </select>
                            </div>
                            <!-- Submit Button -->
                            <button type="submit" name="update-user" class="btn btn-primary">Update User</button>
                            <a aria-hidden="true" class="btn btn-secondary" data-dismiss="modal"
                                aria-label="Close">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</body>

</html>