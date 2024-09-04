<?php
if (file_exists('secret/logs/chatlogs.txt')) {
    // Read the chat logs file into an array, ignoring empty lines
    $chat_logs = file('secret/logs/chatlogs.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($chat_logs as $log) {
        // Output each log entry as HTML
        echo "<p>" . $log . "</p>";
    }
} else {
    echo "<p>Start a Conversation!</p>";
}
?>
