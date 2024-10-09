<?php 
include "../conn.php";

// Query to get each user with their latest message
$stmt = "
    SELECT users.id, users.username, messages.message AS message_preview, messages.timestamp
    FROM users
    LEFT JOIN messages ON users.id = messages.sender_id OR users.id = messages.receiver_id
    LEFT JOIN (
        SELECT MAX(id) AS latest_message_id
        FROM messages
        GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)
    ) AS latest_messages ON messages.id = latest_messages.latest_message_id
    WHERE messages.id IS NOT NULL
    ORDER BY messages.timestamp DESC
";
$result = mysqli_query($conn, $stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $username = $row['username'];
    $message_preview = $row['message_preview'] ?: "No messages yet"; // Default message if none
    $timestamp = $row['timestamp'] ? date('g:i a', strtotime($row['timestamp'])) : ""; // Format timestamp

    echo "<div class='user' id=$id data-username='$username'>
        <h5>$username</h5>
        <div class='message-notification'>
            <p class='message-preview'>$message_preview</p>
            <p class='time-stamp'>$timestamp</p>
        </div>
    </div>";
}
?>
