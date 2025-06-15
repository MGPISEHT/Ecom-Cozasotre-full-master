<?php
session_start();
// include db
include '../configs/DBconnect.php';

// include function register account
if (isset($_POST['register_btn'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = 'user'; // Default role
    $status = 'active'; // Default status

    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['message'] = "All fields are required!";
        header("Location: ../register.php");
        exit;
    }

    // Check if username already exists
    $checkUsernameSql = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($checkUsernameSql);
    $stmt->execute([':username' => $username]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Username already exists! Please choose a different username.";
        header("Location: ../register.php");
        exit;
    }

    // Check if email already exists
    $checkEmailSql = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($checkEmailSql);
    $stmt->execute([':email' => $email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Email already exists!";
        header("Location: ../register.php");
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert user into database
        $sql = "INSERT INTO users (username, email, password, role, status) 
                VALUES (:username, :email, :password, :role, :status)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role' => $role,
            ':status' => $status
        ]);

        $_SESSION['message'] = "Account created successfully!";
        header("Location: ../login.php"); // Redirect to login after successful registration
        exit;
    } catch (PDOException $e) {
        $_SESSION['message'] = "Account creation failed! Please try again. Error: " . $e->getMessage();
        header("Location: ../register.php");
        exit;
    }
}
else if (isset($_POST['login_btn'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['message'] = "All fields are required!";
        header("Location: ../login.php");
        exit;
    }

    $checkLogin = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($checkLogin);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login successful - Store user session
        session_regenerate_id(true); // Regenerate session ID for security
        $_SESSION['auth'] = true;
        $_SESSION['user'] = [
            'username' => $user['username'],
            'email' => $user['email'],
        ];

        $_SESSION['message'] = "Login successful!";
        header("Location: ../index.php"); // Redirect to index page
        exit;
    } else {
        $_SESSION['message'] = "Invalid email or password!";
        header("Location: ../login.php");
        exit;
    }
}
// add Category

?>