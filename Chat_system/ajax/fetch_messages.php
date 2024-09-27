<?php
include "../conn.php";
session_start();

$sender_id = $_GET['sender_id']; // Get sender ID from the request
$receiver_id = $_GET['receiver_id']; // Get receiver ID from the request

// Prepare the SQL query with a LEFT JOIN to fetch replies
$query = "SELECT m1.*, m2.message AS reply_to_message 
          FROM messages m1 
          LEFT JOIN messages m2 ON m1.reply_to = m2.id 
          WHERE (m1.sender_id = ? AND m1.receiver_id = ?) 
             OR (m1.sender_id = ? AND m1.receiver_id = ?) 
          ORDER BY m1.timestamp ASC";

$stmt = $conn->prepare($query);

// Bind parameters
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query returned results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Display messages
        if ($row['sender_id'] == $sender_id) {
            echo '<div class="message sent" data-id="' . htmlspecialchars($row['id']) . '">';
        } else {
            echo '<div class="message received" data-id="' . htmlspecialchars($row['id']) . '">';
        }

        echo '<span class="message-text">' . htmlspecialchars($row['message']) . '</span>';

        // Display file if it exists
        if (!empty($row['file_path'])) {
            echo '<br><img src="ajax/' . htmlspecialchars($row['file_path']) . '" class="thumbnail" onclick="openFullScreen(this)" style="cursor: pointer; width: 100px; height: auto;">';
        }

        // Button to reply to the message
        echo '<button class="reply-button">Reply</button>';

        // Display the original message being replied to (if applicable)
        if ($row['reply_to_message']) {
            echo '<div class="reply-message">Replying to: ' . htmlspecialchars($row['reply_to_message']) . '</div>';
        }

        echo '</div>'; // Close message div
    }
} else {
    echo '<div class="no-messages">No messages found.</div>';
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
