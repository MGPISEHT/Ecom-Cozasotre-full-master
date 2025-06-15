<?php
    include '../configs/DBconnect.php';
    // Check if 'id' is set and is a valid integer
    if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
        $id = $_GET['id'];

        try {
            // Prepare the SQL query to delete the category
            $sql = "DELETE FROM categories WHERE id = :id";
            $stmt = $conn->prepare($sql);

            // Bind the parameter
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Check if a row was actually deleted
            if ($stmt->rowCount() > 0) {
                ?>
            <script type="text/javascript">
                alert("Category Delete successfully.");
                window.location.href = "../viewCategories.php";
            </script>
            
        <?php
            } else {
               echo '<script>alert("Error: Unable to delete category.")</script>';
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        ?>
        <!-- alert want add products successfully -->
            <script type="text/javascript">
                alert("Error: <?php echo $e->getMessage(); ?>");
                window.location.href = "../addCategories.php";
            </script>
    <?php
        }
    }

?>