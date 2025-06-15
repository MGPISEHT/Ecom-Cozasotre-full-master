<?php
include 'db/DBconnnect.php';
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bindParam(1, $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header('location: index.php');
}
?>


<section class="sec-product-detail bg0 p-t-20 ">
    <div class="container">
        <div class="row">
            <?php foreach ($products as $row) { ?>

                <div class="col-md-6 col-lg-7 p-b-30">
                    <div
                        class="item-slick3" style="width: 350px; "
                        data-thumb="images/<?php echo $row['image'] ?>">
                        <div class="wrap-pic-w pos-relative">
                            <img
                                src="images/<?php echo $row['image'] ?>"
                                alt="IMG-PRODUCT" />

                            <a
                                class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                href="images/<?php echo $row['image'] ?>">
                                <i class="fa fa-expand"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5 p-b-30">
                    <div class="p-r-50 p-t-5 p-lr-0-lg">
                        <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                            <?php echo $row['name'] ?>
                        </h4>

                        <span class="mtext-106 cl2"> <?php echo $row['price'] ?>$</span>

                        <p class="stext-102 cl3 p-t-23">
                            <?php echo $row['description'] ?>
                        </p>

                        <!--  -->
                        <div class="p-t-33">

                            <div class="flex-w flex-r-m p-b-10">
                                <div class="size-204 flex-w flex-m respon6-next">
                                    <form action="shoping-cart.php" method="post">
                                        <input type="hidden" name="product_id" value="<?php echo $row['id'] ?>">
                                        <input type="hidden" name="product_image" value="<?php echo $row['image'] ?>">
                                        <input type="hidden" name="product_name" value="<?php echo $row['name'] ?>">
                                        <input type="hidden" name="product_price" value="<?php echo $row['price'] ?>">
                                        <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                            <div
                                                class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-minus"></i>
                                            </div>

                                            <input
                                                class="mtext-104 cl3 txt-center num-product"
                                                type="number"
                                                name="product_quantity"
                                                value="1" />

                                            <div
                                                class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-plus"></i>
                                            </div>
                                        </div>

                                        <button
                                            class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 js-addcart-detail"
                                            type="submit"
                                            name="add_to_cart">
                                            Add to cart
                                        </button>
                                    </form>


                                </div>
                            </div>
                        </div>

                        <!--  -->

                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>