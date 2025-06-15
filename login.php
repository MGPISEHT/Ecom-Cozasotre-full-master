<?php
include './db/DBconnnect.php';

function login_btn()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); 

        $password = trim(htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8'));

        if (empty($email) || empty($password)) {
            return "សូមបញ្ចូលអាសយដ្ឋានអ៊ីមែល និងពាក្យសម្ងាត់។"; // Please enter both email address and password.
        }

        try {
            global $conn;
            $stmt = $conn->prepare("SELECT id, name, email, password FROM customers WHERE email = :email");

            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            if ($user) {
               
                if (password_verify($password, $user['password'])) {
                   
                    session_start();
                     $_SESSION['valid'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name']; 
                    $_SESSION['email'] = $user['email'];

                    header("Location: index.php");
                    exit();
                } else {
                    return "អាសយដ្ឋានអ៊ីមែល ឬពាក្យសម្ងាត់មិនត្រឹមត្រូវ។"; 
                }
            } else {
                return "អាសយដ្ឋានអ៊ីមែល ឬពាក្យសម្ងាត់មិនត្រឹមត្រូវ។"; 
            }
        } catch (PDOException $e) {
            return "មានបញ្ហាបច្ចេកទេសកើតឡើង។ សូមព្យាយាមម្តងទៀតនៅពេលក្រោយ។"; 
        }
    }
    return "";
}

$login_message = login_btn();

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Account </title>
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
    </style>
</head>

<body class="bg-gray-100">
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical">
        <div class="position-relative overflow-hidden radial-gradient-custom min-vh-100 d-flex align-items-center justify-content-center p-4">
            <div class="d-flex align-items-center justify-content-center" style="width: 1000px;">
                <div class="row justify-content-center w-100" style="max-width: 1000px;">
                    <div class="col-md-8 col-lg-6 col-xxl-4" style="width: 650px;">
                        <div class="card mb-0">
                            <div class="card-body p-sm-5">
                                <a class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <h1 class="fw-bold mb-0 " style="font-size: 2rem;color: orangered;">Cozastore</h1>
                                </a>
                                <p class="text-center text-secondary mb-4">Login to your Account</p>
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-semibold">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-semibold">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                                            <label class="form-check-label text-dark" for="flexCheckChecked">
                                                Remember me
                                            </label>
                                        </div>
                                        <!-- <a class="text-primary fw-semibold" href="#">Forgot Password?</a> -->
                                    </div>
                                    <button name="login_btn" name="login_btn" type="submit" class="btn btn-primary w-100 py-2 fs-5 mb-4 rounded-2">Login</button>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <p class="fs-5 mb-0 fw-semibold">New to Cozastore?</p>
                                        <a href="register.php" class="text-primary fw-semibold ms-2" href="#">Create an account</a>
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