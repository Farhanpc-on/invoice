<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/functions.php';

// --- ADD THIS LINE FOR TESTING ---
if (!function_exists('authenticateUser')) {
    die("Error: authenticateUser function is not defined. Check functions.php and its path.");
}
// --- END TEST LINE ---

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['username_or_email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($usernameOrEmail) || empty($password)) {
        $message = 'Both username/email and password are required.';
        $message_type = 'error';
    } else {
        // Change loginUser to authenticateUser
        $user = authenticateUser($usernameOrEmail, $password); // Corrected line

        if ($user) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Redirect to dashboard or a protected page
            header('Location: index.php');
            exit();
        } else {
            $message = 'Invalid username/email or password.';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 25px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #4361ee;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #3f37c9;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            font-weight: 500;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .register-link {
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
        .register-link a {
            color: #4361ee;
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to Your Account</h2>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username_or_email">Username or Email:</label>
                <input type="text" id="username_or_email" name="username_or_email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>

        <p class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</body>
</html>