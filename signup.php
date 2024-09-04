<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate username
    if (empty($username)) {
        echo "Username is required.";
        exit;
    }
    if (strlen($username) < 5 || strlen($username) > 20) {
        echo "Username must be between 5 and 20 characters.";
        exit;
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        echo "Username can only contain letters, numbers, and underscores.";
        exit;
    }

    // Validate password
    if (empty($password)) {
        echo "Password is required.";
        exit;
    }
    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters long.";
        exit;
    }

    // Validate confirm password
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    // Check if username already exists
    $users = file_get_contents('secret/logs/users.txt');
    $users_array = explode(PHP_EOL, $users);

    foreach ($users_array as $user) {
        $user_details = explode(":", $user);
        if ($user_details[0] === $username) {
            echo "Username already exists.";
            exit;
        }
    }

    // Hash password and store user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents('secret/logs/users.txt', $username . ':' . $hashed_password . PHP_EOL, FILE_APPEND);
    $_SESSION['username'] = $username;
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Aligns the form to the left */
            min-height: 100vh;
            margin: 0;
            padding-left: 20%;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px; /* Curved edges */
            border: 1px solid #cccccc; /* Border around the form */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 300px; /* Adjust the width as needed */
            width: 100%;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 10px); /* Adjusts width to be 100% minus padding */
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        p {
            margin-top: 15px;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <form method="POST" action="signup.php">
        <h2>Sign Up</h2>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required><br>
        <input type="submit" value="Sign Up">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
</body>
</html>
