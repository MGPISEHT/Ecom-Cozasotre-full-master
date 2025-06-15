<?php
// Include the database connection file
include("./configs/DBconnect.php");

// ========================== add Category =================================
if (isset($_POST["add-categories"])) {
    // Retrieve form data
    $categoryTitle = $_POST["categoryTitle"];
    $metaKeyword = $_POST["metaKeyword"];
    $metaTitle = $_POST["metaTitle"];
    $metaDescription = $_POST["metaDescription"];
    $categoryStatus = isset($_POST["categoryStatus"]) ? 1 : 0; // Check if status is active
    try {
        // Prepare the SQL query
        $sql = "INSERT INTO categories (title, meta_keyword, meta_title, meta_description, status)
                VALUES (:title, :meta_keyword, :meta_title, :meta_description, :status)";

        // Prepare and execute the statement
        $stmt = $conn->prepare($sql); // Use $conn instead of $pdo
        $stmt->execute([
            ':title' => $categoryTitle,
            ':meta_keyword' => $metaKeyword,
            ':meta_title' => $metaTitle,
            ':meta_description' => $metaDescription,
            ':status' => $categoryStatus
        ]);
        header("Location: viewCategories.php?message=User added successfully!");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
// ========================== Edit Category =================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update-categories"])) {
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

    if (!$id) {
        $_SESSION['message'] = "Invalid category ID!";
        header("Location: viewCategories.php");
        exit();
    }
    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
    $metaDescription = htmlspecialchars(trim($_POST['meta_description'] ?? ''));
    $metaKeyword = htmlspecialchars(trim($_POST['metaKeyword'] ?? ''));
    $metaTitle = htmlspecialchars(trim($_POST['metaTitle'] ?? ''));
    $categoryStatus = isset($_POST["categoryStatus"]) ? 1 : 0;

    if (empty($title) || empty($metaDescription)) {
        $_SESSION['message'] = "Category Title and Meta Description are required!";
        header("Location: editCategories.php?id=$id");
        exit();
    }
    try {
        $stmt = $conn->prepare("UPDATE categories SET title = ?, meta_keyword = ?, meta_title = ?, meta_description = ?, status = ? WHERE id = ?");
        $stmt->execute([$title, $metaKeyword, $metaTitle, $metaDescription, $categoryStatus, $id]);

        $_SESSION['message'] = "Category updated successfully!";
        header("Location: viewCategories.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error updating category: " . $e->getMessage();
        header("Location: editCategories.php?id=$id");
        exit();
    }
}

// ========================== add Products =================================
function addProducts($conn, $name, $description, $price, $stock, $categoryId, $image)
{
    try {
        $sql = "INSERT INTO products (name, description, price, stock_quantity, category_id, image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $description, $price, $stock, $categoryId, $image]);
        return true;
    } catch (PDOException $e) {
        error_log("Error adding product: " . $e->getMessage());
        return false;
    }
}
if (isset($_POST['add-product'])) {
    $name = $_POST['proName'];
    $description = $_POST['proDescription'];
    $price = $_POST['proPrice'];
    $stock = $_POST['stock_quantity'];
    $categoryId = $_POST['category_id'];
    $image = null;

    if (empty($name) || empty($description) || empty($price) || empty($stock) || empty($categoryId)) {
        header("Location: addProducts.php?error=All fields are required.");
        exit();
    }

    // Handle file upload
    if (isset($_FILES['proImage']) && $_FILES['proImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = uniqid() . '_' . basename($_FILES['proImage']['name']);
        $imagePath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['proImage']['tmp_name'], $imagePath)) {
            $image = $imagePath;
        } else {
            header("Location: addProducts.php?error=Failed to upload image.");
            exit();
        }
    }
    if (addProducts($conn, $name, $description, $price, $stock, $categoryId, $image)) {
        header("Location: viewProducts.php?message=Product added successfully.");
        exit();
    } else {
        header("Location: addProducts.php?error=Failed to add product.");
        exit();
    }
}
// ==================================== update products ===========================================================
if (isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $stock = filter_var($_POST['stock_quantity'], FILTER_SANITIZE_NUMBER_INT);
    $category_id = filter_var($_POST['category_id'], FILTER_SANITIZE_NUMBER_INT);
    $current_image = $_POST['current_image'];
    $image = $current_image;
    $uploadError = null;

    // Validate required fields
    if (empty($name) || empty($description) || empty($price) || $price === false || empty($stock) || $stock === false || empty($category_id) || $category_id === false) {
        header("Location: editProduct.php?id=$product_id&error=All fields are required and must be valid.");
        exit();
    }

    // Handle file upload
    if (isset($_FILES['proImage']) && $_FILES['proImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
            }
        }
        $imageName = uniqid() . '_' . basename($_FILES['proImage']['name']);
        $imagePath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['proImage']['tmp_name'], $imagePath)) {
            $image = $imagePath;
        } else {
            header("Location: addProducts.php?error=Failed to upload image.");
            exit();
        }
        if ($uploadError) {
            header("Location: editProduct.php?id=$product_id&error=" . urlencode($uploadError));
            exit();
        }
    }
    try {
        $stmt = $conn->prepare("UPDATE products SET name = :name, description = :description, price = :price, category_id = :category_id, stock_quantity = :stock_quantity, image = :image WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':stock_quantity', $stock, PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);

        $stmt->execute();

        header("Location: viewProducts.php?message=Product updated successfully.");
        exit();
    } catch (PDOException $e) {
        header("Location: editProduct.php?id=$product_id&error=" . urlencode("Database error: " . $e->getMessage()));
        exit();
    }
}
// ========================== Delete Products ==================================
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productIdToDelete = $_GET['id'];
    try {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $productIdToDelete, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            header("Location: viewProducts.php?message=Product deleted successfully!");
            exit();
        } else {
            header("Location: viewProducts.php?error=Product not found!");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: viewProducts.php?error=Error deleting product: " . urlencode($e->getMessage()));
        exit();
    }
}

// ========================== add Customer ================================
if (isset($_POST['add-customer'])) {
    $name = $_POST['cusName'];
    $email = $_POST['cusEmail'];
    $phone = $_POST['cusPhone'];
    $shipping_address = $_POST['cusAddress'];
    $password = password_hash($_POST['cusPassword'], PASSWORD_DEFAULT);
    $status = 'active';
    $created_at = date('Y-m-d H:i:s');

    try {
        $sql = "INSERT INTO customers (name, email, phone, shipping_address, password, status, created_at)
                VALUES (:name, :email, :phone, :shipping_address, :password, :status, :created_at)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':shipping_address', $shipping_address);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->execute();

        $_SESSION['message'] = "Customer added successfully!";
        header("Location: viewCustomer.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        header("Location: addCustomer.php");
        exit();
    }
}

// ========================== Update Customer ================================
if (isset($_POST['update-customer'])) {
    $id = $_POST['customer_id'];
    $name = $_POST['cusName'];
    $email = $_POST['cusEmail'];
    $phone = $_POST['cusPhone'];
    $address = $_POST['cusAddress'];
    $password = $_POST['cusPassword'];

    try {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE customers SET name = :name, email = :email, phone = :phone, shipping_address = :address, password = :password WHERE id = :id";
        } else {
            $sql = "UPDATE customers SET name = :name, email = :email, phone = :phone, shipping_address = :address WHERE id = :id";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if (!empty($password)) {
            $stmt->bindParam(':password', $hashedPassword);
        }

        $stmt->execute();
        $_SESSION['message'] = "Customer updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Update error: " . $e->getMessage();
    }

    header("Location: viewCustomer.php");
    exit();
}
// ========================== Delete Customer ================================
if (isset($_GET['delete_customer']) && is_numeric($_GET['delete_customer'])) {
    $id = $_GET['delete_customer'];
    try {
        $stmt = $conn->prepare("DELETE FROM customers WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['message'] = "Customer deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Delete error: " . $e->getMessage();
    }

    header("Location: viewCustomer.php");
    exit();
}

// ========================== add Users ================================
if (isset($_POST['add-user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];

    // Check if role is valid
    if ($role === "ChooseRole") {
        header("Location: viewUsers.php?error=Please select a valid role");
        exit();
    }

    try {
        $sql = "INSERT INTO users (username, password, email, role, phone) 
                VALUES (:username, :password, :email, :role, :phone)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();

        header("Location: viewUsers.php?message=User added successfully!");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
// ========================== Update Users =================================
if (isset($_POST['update-user'])) {
    $userId = $_POST['user_id']; 
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $phone = trim($_POST['phone']);

    // Basic validation
    if (empty($username) || empty($email) || empty($role)) {
        header("Location: editUser.php?id=$userId&error=All fields are required.");
        exit();
    }
    if (!in_array($role, ['Admin', 'User', 'Editor'])) {
        header("Location: editUser.php?id=$userId&error=Invalid role selected.");
        exit();
    }

    try {
        // Check if the username already exists for another user
        $checkSql = "SELECT COUNT(*) FROM users WHERE username = :username AND id != :id";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':username', $username);
        $checkStmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $checkStmt->execute();
        $userCount = $checkStmt->fetchColumn();

        if ($userCount > 0) {
            header("Location: editUser.php?id=$userId&error=Username already exists. Please choose another.");
            exit();
        }
        $sql = "UPDATE users 
                SET username = :username, email = :email, phone = :phone, role = :role 
                WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: viewUsers.php?message=User updated successfully.");
        exit();

    } catch (PDOException $e) {
        header("Location: editUser.php?id=$userId&error=Database error: " . urlencode($e->getMessage()));
        exit();
    }
}
// ========================== Delete Users =================================
if (isset($_GET['delete_user']) && is_numeric($_GET['delete_user'])) {
    $userIdToDelete = $_GET['delete_user'];

    try {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $userIdToDelete, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            header("Location: viewUsers.php?message=User deleted successfully!");
            exit();
        } else {
            header("Location: viewUsers.php?error=User not found!");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: viewUsers.php?error=Error deleting user: " . urlencode($e->getMessage()));
        exit();
    }
}

// ========================== Add Order =================================
if (isset($_POST['add-order'])) {
    $user_id = $_POST['user_id'];
    $order_number = $_POST['order_number'];
    $total_amount = $_POST['total_amount'];
    $status = $_POST['status'];
    $shipping_address_id = $_POST['shipping_address_id'];
    $payment_id = $_POST['payment_id'];
    $shipping_method = $_POST['shipping_method'];
    $created_at = date('Y-m-d H:i:s');

    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_number, total_amount, status, shipping_address_id, payment_id, shipping_method, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $order_number, $total_amount, $status, $shipping_address_id, $payment_id, $shipping_method, $created_at]);

        $_SESSION['message'] = "Order added successfully.";
        header("Location: viewOrders.php");
    } catch (PDOException $e) {
        $_SESSION['message'] = "Failed to add order: " . $e->getMessage();
        header("Location: addOrder.php");
    }
    exit();
}

// ========================== place-order =================================
if (isset($_POST['place-order'])) {
    try {
        $conn->beginTransaction();

        // Step 1: Insert order
        $customer_id = $_POST['customer_id'];
        $status = "Pending";
        $created_at = date('Y-m-d H:i:s');
        $updated_at = $created_at;

        $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$customer_id, 0, $status, $created_at, $updated_at]);
        $order_id = $conn->lastInsertId();

        // Step 2: Insert order items
        $items = $_POST['items']; // Should be an array of [product_id, quantity]
        $total = 0;

        $stmtProduct = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, created_at) VALUES (?, ?, ?, ?, ?)");

        foreach ($items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];

            $stmtProduct->execute([$product_id]);
            $product = $stmtProduct->fetch();
            $price = $product['price'];

            $subtotal = $price * $quantity;
            $total += $subtotal;

            $stmtItem->execute([$order_id, $product_id, $quantity, $price, $created_at]);
        }

        // Step 3: Update total in orders table
        $stmt = $conn->prepare("UPDATE orders SET total_amount = ? WHERE id = ?");
        $stmt->execute([$total, $order_id]);

        // Step 4: Insert payment
        $payment_method = $_POST['payment_method'];
        $payment_status = "Pending";
        $stmt = $conn->prepare("INSERT INTO payments (order_id, method, amount, status, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$order_id, $payment_method, $total, $payment_status, $created_at]);

        // Step 5: Insert shipping address
        $recipient = $_POST['recipient_name'];
        $line1 = $_POST['address_line1'];
        $city = $_POST['city'];
        $country = $_POST['country'];
        $phone = $_POST['phone'];

        $stmt = $conn->prepare("INSERT INTO shipping_addresses (order_id, recipient_name, address_line1, city, country, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$order_id, $recipient, $line1, $city, $country, $phone]);

        $conn->commit();

        $_SESSION['message'] = "Order placed successfully!";
        header("Location: viewOrders.php");
        exit();

    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['message'] = "Error placing order: " . $e->getMessage();
        header("Location: addOrder.php");
        exit();
    }
}
