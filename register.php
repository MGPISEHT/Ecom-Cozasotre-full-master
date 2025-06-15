<?php
include './db/DBconnnect.php';

session_start();

$message = '';

if (isset($_POST['register_btn'])) { 
    $name = trim(htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'));
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $plain_password = $_POST['password']; 
    $confirm_password = $_POST['confirm_password']; 

    if ($plain_password !== $confirm_password) {
        $message = "The password don't match."; 
    } elseif (strlen($plain_password) < 5) {
        $message = "The password must be at least 5 characters long."; 
    } else {
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
        try {
            global $conn;
            $check_stmt = $conn->prepare("SELECT COUNT(*) FROM customers WHERE email = :email OR name = :name");
            $check_stmt->execute([':email' => $email, ':name' => $name]);
            if ($check_stmt->fetchColumn() > 0) {
                $message = "This customer name or email address already exists.";
            } else {
                $sql = "INSERT INTO customers (name, password, email)
                        VALUES (:name, :password, :email)";
                $stmt = $conn->prepare($sql);

                // Bind parameters
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':password', $hashed_password); 
                $stmt->bindParam(':email', $email);

                $stmt->execute();
                $_SESSION['success_message'] = "User registration successful! Please log in."; 
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Register PDO Error: " . $e->getMessage()); 
            $message = "Technical specifications are coming up. Please wait a moment."; 
        }
    }
}

if (isset($_GET['error'])) {
    $message = htmlspecialchars($_GET['error']);
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Register - Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .radial-gradient-custom {
            background: radial-gradient(circle, rgba(240, 240, 255, 1) 0%, rgba(220, 220, 245, 1) 100%);
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }

        .btn-primary {
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .message-box {
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 15px;
        }
        .error-message {
            color: #d9534f; 
            background-color: #f2dede; 
            border: 1px solid #ebccd1;
        }
        .success-message {
            color: #28a745; 
            background-color: #d4edda; 
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical">
        <div class="position-relative overflow-hidden radial-gradient-custom min-vh-100 d-flex align-items-center justify-content-center p-4">
            <div class="d-flex align-items-center justify-content-center" style="width: 100%; max-width: 1000px; height: 600px;">
                <div class="row justify-content-center w-100">
                    <div class="col-12" style="max-width: 650px;">
                        <div class="card mb-0">
                            <div class="card-body p-sm-5">
                                <a class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <h1 class="fw-bold mb-0" style="font-size: 2rem; color: orangered;">Cozastore</h1>
                                </a>
                                <p class="text-center text-secondary mb-4">Create Your Account</p>
                                <?php if (!empty($message)): ?>
                                    <p class="message-box error-message"><?php echo $message; ?></p>
                                <?php endif; ?>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-semibold">Username</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name." required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-semibold">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address." required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-semibold">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                                    </div>

                                    <button name="register_btn" type="submit" class="btn btn-primary w-100 py-2 fs-5 mb-4 rounded-2">Register</button>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <p class="fs-5 mb-0 fw-semibold">Already Your Account?</p>
                                        <a href="login.php" class="text-primary fw-semibold ms-2">Login Account</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
