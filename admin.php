<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Only allow the 'admin' user
if ($_SESSION['username'] !== 'admin') {
    header('Location: place.php');
    exit;
}

// Handle toggling user bans
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle']) && isset($_POST['toggle_action'])) {
    $username = $_POST['username'];
    $banned_users = file_exists('secret/logs/banned_users.txt') ? file('secret/logs/banned_users.txt', FILE_IGNORE_NEW_LINES) : [];

    if (in_array($username, $banned_users)) {
        // If user is already banned, remove them from the banned list
        $banned_users = array_diff($banned_users, [$username]);
    } else {
        // Otherwise, add them to the banned list
        $banned_users[] = $username;
    }

    file_put_contents('secret/logs/banned_users.txt', implode(PHP_EOL, $banned_users) . PHP_EOL);
}

// Fetch all users from the users.txt file
$users = file('secret/logs/users.txt', FILE_IGNORE_NEW_LINES);
$banned_users = file_exists('secret/logs/banned_users.txt') ? file('secret/logs/banned_users.txt', FILE_IGNORE_NEW_LINES) : [];

// Load feedback entries
$feedback_entries = file_exists('secret/logs/feedback.txt') ? file('secret/logs/feedback.txt', FILE_IGNORE_NEW_LINES) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .admin-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
        }
        h1 {
            color: #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background-color: #f2f2f2;
        }
        .toggle-button {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .toggle-on {
            background-color: #4CAF50;
        }
        .feedback-list {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fff;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <h1>Admin Panel</h1>
    <p>Logged in as <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Toggle Mute</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): 
                    $user_details = explode(':', $user); // Username:HashedPassword
                    $username = $user_details[0];
                    $is_banned = in_array($username, $banned_users);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($username); ?></td>
                    <td><?php echo $is_banned ? 'Muted' : 'Active'; ?></td>
                    <td>
                        <button class="toggle-button <?php echo $is_banned ? '' : 'toggle-on'; ?>" name="toggle" value="Toggle" type="submit">
                            <?php echo $is_banned ? 'Unmute' : 'Mute'; ?>
                        </button>
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                        <input type="hidden" name="toggle_action" value="1">
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>

    <h2>Feedback Entries:</h2>
    <div class="feedback-list">
        <?php if (!empty($feedback_entries)): ?>
            <ul>
                <?php foreach ($feedback_entries as $entry): ?>
                    <li><?php echo htmlspecialchars($entry); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No feedback entries available.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
