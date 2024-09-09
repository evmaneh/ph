<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

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
            justify-content: flex-start;
            min-height: 100vh;
            margin: 0;
            padding-left: 20%;
            font-family: Arial, sans-serif;
            background-color: #2a3032;
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            background-color: #f7f3c4;
            color: #b49c6a;
            padding: 20px;
            border-radius: 15px;
            border: 1px solid #4f2f1f;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            width: 100%;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 10px);
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #b49c6a;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #4f2f1f;
        }

        p {
            margin-top: 15px;
        }

        a {
            color: #968259;
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
