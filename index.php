<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

header('Location: place.php');
exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>
<body>
    <h2>Welcome to The Place, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <a href="place.php">If you aren't redirected, click here.</a>
    <p>You are logged in.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
