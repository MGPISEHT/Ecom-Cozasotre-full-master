<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Thank You!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-10">
                <div class="order-summary-card">
                    <div class="text-center mb-4">
                        <h1 class="display-4 text-success">
                            <i class="bi bi-check-circle-fill"></i> Thank You for Your Order!
                        </h1>
                        <p class="lead">Your order #<?php echo htmlspecialchars($order_id ?? ''); ?> has been successfully placed.</p>
                    </div>
                    <p class="text-center mt-4 lead">
                        Your order is being processed. You will receive a confirmation email shortly.
                    </p>
                    <div class="d-grid gap-2 col-6 mx-auto mt-4">
                        <a href="product.php" class="btn btn-primary btn-lg">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>