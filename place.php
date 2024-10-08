<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$banned_users = file_exists('secret/logs/banned_users.txt') ? file('secret/logs/banned_users.txt', FILE_IGNORE_NEW_LINES) : [];
$is_banned = in_array($username, $banned_users);

function update_user_count() {
    $session_file = 'secret/logs/session_users.txt';
    $session_expiration = 300;

    $sessions = @file_get_contents($session_file);
    $sessions = $sessions !== false ? @unserialize($sessions) : [];

    if ($sessions === false) {
        $sessions = [];
    }

    $sessions[session_id()] = time();

    foreach ($sessions as $session_id => $last_active) {
        if ($last_active < time() - $session_expiration) {
            unset($sessions[$session_id]);
        }
    }

    file_put_contents($session_file, serialize($sessions));

    return count($sessions);
}

$online_users = update_user_count();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$is_banned) {
    $botUsername = 'Mr.Rock';
    $timestamp = date('g:i A');

    if (isset($_POST['message']) && !empty(trim($_POST['message']))) {
        $message = trim($_POST['message']);
        
        if (strpos($message, '!feedback') === 0) {
            $feedback = trim(str_replace('!feedback', '', $message));
            if (!empty($feedback)) {
                $feedback_entry = "$timestamp - $username: $feedback" . PHP_EOL;
                file_put_contents('secret/logs/feedback.txt', $feedback_entry, FILE_APPEND);
            }
        } elseif ($message === '!clear' && $username === 'admin') {
            $chatLogsFile = 'secret/logs/chatlogs.txt';
            $uploadsDir = 'secret/logs/uploads/';
            
            if (file_exists($chatLogsFile)) {
                file_put_contents($chatLogsFile, '');
            }

            foreach (glob($uploadsDir . '*') as $file) {
                unlink($file);
            }

            $log_entry = "$botUsername: Chat cleared by $username" . PHP_EOL;
            file_put_contents($chatLogsFile, $log_entry, FILE_APPEND);
        } elseif (strpos($message, '!botchat') === 0 && $username === 'admin') {
            $botMessage = trim(str_replace('!botchat', '', $message));
            
            if (!empty($botMessage)) {
                $log_entry = "$timestamp - $botUsername: $botMessage" . PHP_EOL;
                file_put_contents('secret/logs/chatlogs.txt', $log_entry, FILE_APPEND);
            }
        } elseif ($message === '!bot') {
            $botResponse = "$timestamp - $botUsername: Hi, I'm $botUsername, the website bot!" . PHP_EOL;
            file_put_contents('secret/logs/chatlogs.txt', $botResponse, FILE_APPEND);
        } else {
            $log_entry = "$timestamp - $username: $message" . PHP_EOL;
            file_put_contents('secret/logs/chatlogs.txt', $log_entry, FILE_APPEND);
        }
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'secret/logs/uploads/';
        $upload_file = $upload_dir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if ($_FILES['image']['size'] <= 5000000) {
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imageFileType, $allowed_extensions)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                        $log_entry = "$timestamp - $username: <img src='$upload_file' alt='Image' style='width:auto; max-height:100px;'>" . PHP_EOL;
                        file_put_contents('secret/logs/chatlogs.txt', $log_entry, FILE_APPEND);
                    } else {
                        echo "Unknown Error.";
                    }
                } else {
                    echo "JPG, JPEG, PNG & GIF files are allowed.";
                }
            } else {
                echo "Large file.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    header('Location: place.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>The Place</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            messageInput.focus();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === '/') {
                event.preventDefault();
                messageInput.focus();
            }
        });

        function checkBanStatus() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'check_ban_status.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText === 'banned') {
                        window.location.reload();
                    }
                }
            };
            xhr.send();
        }

        setInterval(checkBanStatus, 5000);
    </script>
    <style>
        body {
            background-color: #2a3032;
            font-family: Arial, sans-serif;
        }
        .chat-box {
            width: 70%;
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 40px;
            background-color: #f7f3c4;
            color: #b49c6a;
            border: 1px solid #4f2f1f;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .chat-log {
            height: 300px;
            overflow-y: scroll;
            border: 1px solid #ccc;
            border-radius: 15px;
            padding: 10px;
            background-color: #1c2021;
            margin-bottom: 10px;
        }
        .chat-log p {
            margin: 0 0 10px;
        }
        .chat-input {
            display: flex;
            flex-direction: column;
            <?php if ($is_banned) echo 'display: none;'; ?>
        }
        .chat-input input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .chat-input input[type="file"] {
            margin-bottom: 10px;
        }
        .chat-input button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .user-count {
            position: fixed;
            bottom: 90%;
            right: 15%;
            background-color: #000;
            color: #fff;
            padding: 5px 10px;
            border-radius: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="chat-box">
        <h2>Welcome to The Place, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <a href="logout.php">Logout</a>

        <div class="chat-log" id="chatLog">
        </div>

        <?php if ($is_banned): ?>
            <p>You have been banned from sending messages.</p>
        <?php else: ?>
            <form class="chat-input" method="POST" action="place.php" enctype="multipart/form-data">
                <input type="file" name="image" accept="image/*">
                <input type="text" name="message" id="messageInput" placeholder="Type your message..." autocomplete="off">
            </form>
        <?php endif; ?>
    </div>

    <div class="user-count">
        <?php echo "Users online: " . $online_users; ?>
    </div>

    <script>
        var userIsScrolling = false;
        var chatLog = document.getElementById('chatLog');

        function loadChatLog() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'load_place.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    chatLog.innerHTML = xhr.responseText;

                    if (!userIsScrolling) {
                        chatLog.scrollTop = chatLog.scrollHeight;
                    }
                }
            };
            xhr.send();
        }

        chatLog.addEventListener('scroll', function() {
            var scrollTop = chatLog.scrollTop;
            var scrollHeight = chatLog.scrollHeight;
            var clientHeight = chatLog.clientHeight;

            if (scrollTop + clientHeight < scrollHeight) {
                userIsScrolling = true;
            } else {
                userIsScrolling = false;
            }
        });

        setInterval(loadChatLog, 100);
    </script>
</body>
</html>
