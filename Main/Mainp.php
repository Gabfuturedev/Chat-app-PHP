<?php
session_start(); // Start the session

// Check if user is logged in
if (isset($_SESSION['email'])) {
    // Include database connection
    include "connect.php"; // Assuming you have a file called connect.php
    
    // Retrieve email from session
    $email = $_SESSION['email'];
    
    // Prepare SQL statement to retrieve user's name
    $stmt = $conn->prepare("SELECT name FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($name);
    
    // Fetch user's name
    if ($stmt->fetch()) {
        // User's name found, store it in a variable $name
        // echo "Welcome, $name!";
    } else {
        // User's name not found
        echo "Welcome!";
    }
    
    // Close statement
    $stmt->close();
} else {
    // Redirect user back to login page if not logged in
    header("Location: index.php");
    exit();
}

// Handle sending messages
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['recipient']) && isset($_POST['message'])) {
        $recipient = $_POST['recipient'];
        $message = $_POST['message'];
        $timestamp = date("Y-m-d H:i:s");
        
        // Prepare SQL statement to insert message into database
        $stmt_insert = $conn->prepare("INSERT INTO messages (sender, recipient, message, sent_at) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("ssss", $name, $recipient, $message, $timestamp);
        $stmt_insert->execute();
        $stmt_insert->close();
        
        // Redirect back to the same page after sending the message
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}
// Retrieve all users from the database
                      
// Fech received messages from the database
$received_messages_query = "SELECT id, sender, message, sent_at, is_read FROM messages WHERE recipient = ?";

$stmt_received = $conn->prepare($received_messages_query);
$stmt_received->bind_param("s", $name); // Use $email instead of $name
$stmt_received->execute();
$result_received = $stmt_received->get_result();

// Fetch sent messages from the database
$sent_messages_query = "SELECT recipient, message, sent_at FROM messages WHERE sender = ?";
$stmt_sent = $conn->prepare($sent_messages_query);
$stmt_sent->bind_param("s", $name);
$stmt_sent->execute();
$result_sent = $stmt_sent->get_result();

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        /* Your custom CSS styles */
        
.full-message-container {
    border: 1px solid #ccc;
    padding: 10px;
    margin-top: 20px;
    max-width: 800px; /* Example value for maximum width */
}

.full-message {
    margin-bottom: 10px;
    border-radius: 20px;
    background-color: blue;
    color: white;
    display: inline-block;
    padding: 10px;
    max-width: 100%; /* Allow the message container to expand up to its parent's width */
    word-wrap: break-word; /* Break long words to prevent overflow */
    overflow-wrap: break-word; /* For wider browser support */
}
.reply-form {
    display: none; /* Hide by default */
}

.reply-form textarea {
    width: 100%;
    margin-bottom: 10px;
}

.reply-form button {
    float: left;
}/* CSS for read messages */
.message.read {
    background-color: #f0f0f0; /* Example background color for read messages */
    width: 50%;
    opacity: 50%;
}

/* CSS for unread messages */
.message:not(.read) {
    background-color: #ffffff; /* Example background color for unread messages */
    font-weight: bold; /* Example style for unread messages */
    width: 50%;
}
#messages-column {
  background-color: #f0f0f0; /* Light gray background for messages */
  padding: 20px; /* Add some padding for spacing */
}

#messages-column h2 {
  color: #333; /* Darker text color for messages heading */
}

#inbox-column {
  background-color: #fff; /* White background for inbox */
  padding: 20px; /* Add some padding for spacing */
}

#inbox-column h2 {
  color: #007bff; /* Blue text color for inbox heading */
}
.message {
    padding: 10px;
    border-bottom: 1px solid #ccc;
}

.message-button {
    background-color: transparent;
    border: none;
    cursor: pointer;
    width: 100%;
    text-align: left;
}

.message-info {
    font-weight: bold;
}

.message-content {
    margin-top: 5px;
    color: #555;
}
.message-button:hover {
    background-color: #f0f0f0; /* Change background color on hover */
}
.message-button:active{
    background-color: green;
}



    </style>
</head>
<body>
<nav class="navbar bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand"><?php echo ucwords($name)?></a>
   

    </div>
    <form method="post">
        <button type="submit" name="logout">Logout</button>
    </form>
    <?php
    if(isset($_POST['logout'])) {
        // Destroy the session
        session_destroy();
    
        // Redirect to the index page
        header("Location: index.php");
        exit;
    }
    ?>
    <!-- Button to trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userListModal">
    Open User List
</button>

</nav>

<div class="container">
  <div class="row">
    <div class="col-lg-3" id="inbox-column">
    <h2>Inbox</h2>
<!-- Add inbox content here -->
<div class="inbox-container">
    <div class="inbox">
        <div class="messages">
            <form class="d-flex" role="search" method="get">
                <input name="searchTerm" class="form-control me-2" type="search" id="searchUser" placeholder="Search for a user" aria-label="Search">
                <button class="btn btn-outline-success" type="submit" name="btn-search">Search</button>
            </form>
            <?php
            // Display received messages based on search term
            while ($row = $result_received->fetch_assoc()) {
                // Filter messages based on the search term
                if (isset($_GET['searchTerm']) && !empty($_GET['searchTerm'])) {
                    $searchTerm = $_GET['searchTerm'];
                    $sender = $row['sender'];
                    // If the sender's name contains the search term, display the message
                    if (stripos($sender, $searchTerm) !== false) {
                        echo "<div class='message unread' style='background-color:white;'>";
                        echo "<button class='message-button' onclick='showFullMessage(this)' data-sender='" . htmlspecialchars($row["sender"]) . "' data-message='" . htmlspecialchars($row["message"]) . "' data-sent-at='" . htmlspecialchars($row["sent_at"]) . "' data-recipient-name='" . htmlspecialchars($row["sender"]) . "'>";
                        echo "<p>" . htmlspecialchars($row["sender"]) . "</p>";
                        echo "<p style='opacity:50%;'>" . htmlspecialchars($row["message"]) . "</p>";
                        echo "<p style='opacity:50%;'>" . htmlspecialchars($row["sent_at"]) . "</p>";
                        echo "</button>";
                        echo "</div>";
                    }
                } else {
                    // If no search term is provided, display all messages
                    echo "<div class='message unread' style='background-color:white;'>";
                    echo "<button class='message-button' onclick='showFullMessage(this)' data-sender='" . htmlspecialchars($row["sender"]) . "' data-message='" . htmlspecialchars($row["message"]) . "' data-sent-at='" . htmlspecialchars($row["sent_at"]) . "' data-recipient-name='" . htmlspecialchars($row["sender"]) . "'>";
                    echo "<p>" . htmlspecialchars($row["sender"]) . "</p>";
                    echo "<p style='opacity:50%;'>" . htmlspecialchars($row["message"]) . "</p>";
                    echo "<p style='opacity:50%;'>" . htmlspecialchars($row["sent_at"]) . "</p>";
                    echo "</button>";
                    echo "</div>";
                }
            }
            ?>

            
        </div>
    </div>
</div>




    </div>
    <div class="col-lg-9" id="messages-column">
      <h2>Messages</h2>
      <!-- Add message content here -->
      <div class="full-message-container" style="width:80%;" >
    <strong class="sender-name"></strong><br>
    <div class="full-message"></div>
    <div class="message-reply"></div>
    <form action="" method="post">
        <div class="mb-3">
            <!-- <label for="recipient" class="form-label">Recipient:</label> -->
            <input type="text" class="form-control" id="recipient" name="recipient" placeholder="name or message" value=""   readonly style="display: none;">


        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message:</label>
            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Type your message here"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
    </div>
  </div>
</div>





<!-- Modal -->
<div class="modal fade" id="userListModal" tabindex="-1" aria-labelledby="userListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userListModalLabel">Send a Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- User list will be displayed here -->
        
                <?php 
                include "connect.php";
              
                  $query = "SELECT name FROM user";
                        $result_users = $conn->query($query);
                        $search = "SELECT name  FROM user WHERE LIKE %";

                        // Display each user as a message in the modal
                        if ($result_users->num_rows > 0) {
                            while ($row = $result_users->fetch_assoc()) {
                ?>
                                <div class="message unread">
            <button class="message-button" data-bs-dismiss="modal" onclick="showFullMessage(this)" 
                data-sender="<?php echo htmlspecialchars($row["name"]); ?>" 
                data-message="No message" 
                data-sent-at="<?php echo date("Y-m-d H:i:s"); ?>" 
                data-recipient-name="<?php echo htmlspecialchars($row["name"]); ?>">
                <div class="message-info">
                    <p class="sender-name"><?php echo htmlspecialchars($row["name"]); ?></p>
                </div>
                
            </button>
        </div>
                <?php               
                            }
                        } else {
                            echo "<p>No users found.</p>";
                        }
                ?>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
  

<script>
// Function to show full message and reply form
function showFullMessage(button) {
            const message = button.getAttribute('data-message');
            const recipientName = button.getAttribute('data-recipient-name');

            // Populate message details in the message container
            document.querySelector('.full-message').innerHTML = message;
            document.querySelector('.sender-name').innerHTML = recipientName;

            // Set the recipient field to the sender's name
            document.getElementById('recipient').value = recipientName;

            // Show full message container
            const fullMessageContainer = document.querySelector('.full-message-container');
            fullMessageContainer.style.display = 'block';
        }

        // Add event listener to message buttons
        document.querySelectorAll('.message-button').forEach(item => {
            item.addEventListener('click', event => {
                const message = event.currentTarget.getAttribute('data-message');
                const recipientName = event.currentTarget.getAttribute('data-recipient-name');
                showFullMessage(message, recipientName);
            });
        });

// Add event listener to message buttons
document.querySelectorAll('.message-button').forEach(item => {
    item.addEventListener('click', event => {
        const message = event.currentTarget.getAttribute('data-message');
        const recipient = event.currentTarget.getAttribute('data-recipient');
        showFullMessage(message, recipient);
    });
});


// Load the first message when the page loads
window.addEventListener('load', () => {
    loadFirstMessage();
});


function markAsRead(button) {
        // Add a 'read' class to the parent div of the clicked button
        button.parentNode.classList.add('read');
}

function markAsRead(messageId) {
    // Find the message element
    var message = document.getElementById(messageId);
    // Change the appearance to indicate the message has been read
    message.classList.remove('unread');
    message.classList.add('read');
}


</script>

</body>
</html>
