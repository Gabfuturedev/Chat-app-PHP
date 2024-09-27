<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
} 
include "conn.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat System</title>
    <link rel="stylesheet" href="style.css">
   
</head>
<body>
    <div class="container">
        <div class="listofusers">
            <!-- User list will be displayed here via AJAX -->
        </div>

        <div class="chat-container">
            <div class="chat-header">
                <h2> <span class="receiver-username">Select a user to chat</span></h2>
            </div>
            <div class="chat-messages">
                <!-- Chat messages will display here -->
            </div>
            
            <!-- Reply Indicator -->
            <div class="reply-indicator" id="reply-indicator">
                <strong>Replying to:</strong> <span id="reply-message-display"></span>
                <button id="dismiss-reply" style="cursor: pointer;">X</button>
            </div>

            <div class="chat-form">
                <textarea id="message-input" placeholder="Type your message..."></textarea>
                <input type="file" id="file-input">
                <button id="send-button">Send</button>
            </div>
        </div>
    </div>
    <a href="logout.php"><input type="button" value="Logout"></a>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
       $(document).ready(function() {
    var sender_id = <?php echo $_SESSION['user_id']; ?>; // Logged-in user's ID
    var receiver_id; // To store the ID of the selected receiver
    var replyToMessageId = null; // Default no reply

    // Initially hide the reply indicator
    $('#reply-indicator').hide();

    // Load users on page load
    function loadUsers() {
        $.ajax({
            url: "ajax/fetch_users.php",
            method: "GET",
            success: function(data) {
                $(".listofusers").html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error loading users:", error);
            }
        });
    }

    loadUsers(); // Call to load users

    // Handle user selection
    $(document).on('click', '.user-item', function() {
        receiver_id = $(this).data("id"); // Get the receiver's ID
        $(".receiver-username").text($(this).text()); // Update the chat header with the receiver's name
        loadMessages(); // Load the messages for the selected user
    });

    // Load messages function
    function loadMessages() {
        if (receiver_id) {
            $.ajax({
                url: "ajax/fetch_messages.php",
                method: "GET",
                data: { sender_id: sender_id, receiver_id: receiver_id },
                success: function(data) {
                    $(".chat-messages").html(data); // Display the messages
                },
                error: function(xhr, status, error) {
                    console.error("Error loading messages:", error);
                }
            });
        }
    }

    // Send message function
    $('#send-button').click(function() {
        var message = $('#message-input').val().trim(); // Trim any extra whitespace
        var formData = new FormData();

        // Check if message is not empty or if there's a file
        if (message.length > 0 || $('#file-input')[0].files.length > 0) {
            formData.append('message', message);
            formData.append('sender_id', sender_id);
            formData.append('receiver_id', receiver_id);
            formData.append('file', $('#file-input')[0].files[0]);
            if (replyToMessageId) {
                formData.append('reply_to', replyToMessageId); // Add reply_to if replying
            }

            $.ajax({
                url: 'ajax/add_message.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#message-input').val(''); // Clear input
                    $('#file-input').val(''); // Clear file input
                    replyToMessageId = null; // Clear reply ID after sending
                    $('#reply-indicator').hide(); // Hide reply indicator
                    loadMessages(); // Refresh messages after sending
                },
                error: function(xhr, status, error) {
                    console.error("Error sending message:", error);
                }
            });
        } else {
            alert("Please enter a message or select a file to send."); // Alert if nothing is entered
        }
    });

    // Handle reply button click
    $(document).on('click', '.reply-button', function() {
        var messageId = $(this).closest('.message').data('id'); // Get the ID of the message being replied to
        var messageText = $(this).closest('.message').find('.message-text').text(); // Get the text of the message

        replyToMessageId = messageId; // Store the reply ID
        $('#reply-message-display').text(messageText); // Show the message text in the reply indicator
        $('#reply-indicator').show(); // Show the reply indicator
        $('#message-input').focus(); // Focus the message input for quick replying
    });

    // Dismiss reply indicator
    $('#dismiss-reply').click(function() {
        $('#reply-indicator').hide(); // Hide the reply indicator
        replyToMessageId = null; // Clear reply ID
    });
});
function openFullScreen(image) {
            var fullScreenDiv = document.createElement('div');
            fullScreenDiv.style.position = 'fixed';
            fullScreenDiv.style.top = 0;
            fullScreenDiv.style.left = 0;
            fullScreenDiv.style.width = '100%';
            fullScreenDiv.style.height = '100%';
            fullScreenDiv.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
            fullScreenDiv.style.display = 'flex';
            fullScreenDiv.style.alignItems = 'center';
            fullScreenDiv.style.justifyContent = 'center';
            fullScreenDiv.style.zIndex = 1000;

            var fullScreenImage = document.createElement('img');
            fullScreenImage.src = image.src; // Set the source to the clicked image
            fullScreenImage.style.maxWidth = '90%'; // Limit max width
            fullScreenImage.style.maxHeight = '90%'; // Limit max height

            fullScreenDiv.onclick = function() {
                document.body.removeChild(fullScreenDiv);
            };

            fullScreenDiv.appendChild(fullScreenImage);
            document.body.appendChild(fullScreenDiv);
        }
    </script>
</body>
</html>
