<?php
session_start();

$banned_words = array('admin', 'root', 'password', 'owner', 'mod', 'spam', 'admin');

function contains_banned_words($username, $banned_words) {
    foreach ($banned_words as $word) {
        if (stripos($username, $word) !== false) {
            return true;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

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
    if (contains_banned_words($username, $banned_words)) {
        echo "Username contains banned words.";
        exit;
    }

    if (empty($password)) {
        echo "Password is required.";
        exit;
    }
    if (strlen($password) < 6) {
        echo "Password must be at least 6 characters long.";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    $users = file_get_contents('secret/logs/users.txt');
    $users_array = explode(PHP_EOL, $users);

    foreach ($users_array as $user) {
        $user_details = explode(":", $user);
        if ($user_details[0] === $username) {
            echo "Username already exists.";
            exit;
        }
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    file_put_contents('secret/logs/users.txt', $username . ':' . $hashed_password . PHP_EOL, FILE_APPEND);
    $_SESSION['username'] = $username;
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['login_username']);
    $password = trim($_POST['login_password']);

    if (empty($username) || empty($password)) {
        echo "Username and password required.";
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
    <title>Create acc</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #2a3032;
        }

        .container {
            display: flex;
            justify-content: space-between;
            gap: 50px;
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
    <div class="container">
        <form method="POST" action="">
            <h2>Sign Up</h2>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required><br>
            <input type="submit" name="signup" value="Sign Up">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>

        <form method="POST" action="">
            <h2>The Place.</h2>
            <p>The Place is an online chatroom dedicated to online expression, making friends etc. The main inspiration/theme for The Place comes from shedtwt, a selfharm and eating disorder twitter space that shares info images etc.</p>
            <p>Please treat everyone here with respect, and reason. Do that and you should love it here! (I hope)</p>
            <h3>Why did I want to make this?</h3>
            <p>I want this website to be known as a safe space for anyone, yet not restricting users with too many rules.</p>
            <p><a href="privacy.php">Privacy Policy</a></p>
        </form>
    </div>
</body>
</html>
