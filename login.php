<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate username and password
    if (empty($username) || empty($password)) {
        echo "All fields are required.";
        exit;
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        echo "Invalid username or password.";
        exit;
    }

    $users = file_get_contents('secret/logs/users.txt');
    $users_array = explode(PHP_EOL, $users);

    foreach ($users_array as $user) {
        $user_details = explode(":", $user);
        if ($user_details[0] === $username && password_verify($password, $user_details[1])) {
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        }
    }
    echo "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Aligns the form to the left */
            min-height: 100vh;
            margin: 0;
            padding-left: 20%; /* Increased padding to move the form to the right */
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
    <form method="POST" action="login.php">
        <h2>Login</h2>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>
        <input type="submit" value="Login">
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </form>
</body>
</html>
