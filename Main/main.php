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

// Fech received messages from the database
$received_messages_query = "SELECT sender, message, sent_at FROM messages WHERE recipient = ?";
$stmt_received = $conn->prepare($received_messages_query);
$stmt_received->bind_param("s", $email); // Use $email instead of $name
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
.inbox-container {
    display: flex;
}

.inbox {
    flex: 1; /* Take up remaining space */
    margin-right: 20px; /* Add some space between the inbox and full message container */
}


.full-message-container {
    border: 1px solid #ccc;
    padding: 10px;
    margin-top: 20px;
    flex: 1;
}

.full-message {
    margin-bottom: 10px;
    border-radius: 20px;
    background-color: blue;
    color: white;
    display: inline-block;
    padding: 10px;
    max-width: none; /* Allow the message container to expand */
   
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
    width: 20%;
}

/* CSS for unread messages */
.message:not(.read) {
    background-color: #ffffff; /* Example background color for unread messages */
    font-weight: bold; /* Example style for unread messages */
    width: 20%;
}.conbody{
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    height: auto;
}.container {
    display: flex;
}

.inbox-container {
    flex: 1; /* Take up 50% of the container */
    width: 100%;
    margin-left: 0;
}

.message-container {
    flex: 1; /* Take up 50% of the container */
    margin-left: 20px; /* Add space between the inbox and message display */
}




    </style>
</head>
<body>
<nav class="navbar bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand"><?php echo ucwords($name)?></a>
        <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
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
</nav>

<div class="container">
<div class="inbox-container">
    <div class="inbox">
        <h2>Inbox</h2>
        <div class="messages">
            <?php
            // Display received messages
            while ($row = $result_received->fetch_assoc()) {
                echo "<div class='message unread' style='background-color:white;' >";
                echo "<button class='message-button' onclick='markAsRead(this)' data-message='" . $row["message"] . "' data-recipient='" . $row["sender"] . "'>";
                echo "<p>" . $row["sender"] . "</p>";
                echo "<p>" . $row["sent_at"] . "</p>";
                echo "</button>";
                echo "</div>";
            }
            ?>
        </div>  
    </div>
</div>

<?php 
while ($row_sent = $result_sent->fetch_assoc()) {
    $recipient = htmlspecialchars($row_sent['recipient']);
    $message = htmlspecialchars($row_sent['message']);
    $sent_at = htmlspecialchars($row_sent['sent_at']);

     

}
?> 



    <div class="full-message-container" style="width:80%;" >
    <H2 class="sender-name"></H2> 
    <div class="full-message"></div>
    <div class="message-reply"></div>
    <form action="" method="post">
        <div class="mb-3">
            <label for="recipient" class="form-label">Recipient:</label>
            <input type="text" class="form-control" id="recipient" name="recipient" placeholder="Recipient's Email"  >
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message:</label>
            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Type your message here"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
    



</div>

    



<script>
    /// Function to load the first message when the page loads
function loadFirstMessage() {
    const firstMessageButton = document.querySelector('.message-button');
    if (firstMessageButton) {
        const message = firstMessageButton.getAttribute('data-message');
        const recipient = firstMessageButton.getAttribute('data-recipient');
        showFullMessage(message);
        showFullMessage(recipient);
    }
}


// Function to show full message and reply form
function showFullMessage(message, recipient, messageReply) {
    const fullMessageElement = document.querySelector('.full-message');
    const fullSenderElement = document.querySelector('.sender-name');
    // const fullMessageReplyElement = document.querySelector('.message-reply');
    fullMessageElement.innerHTML = message;
    fullSenderElement.innerHTML = recipient;
    // fullMessageReplyElemen.innerHTML =messageReply;

     

    // Show full message container
    const fullMessageContainer = document.querySelector('.full-message-container');
    fullMessageContainer.style.display = 'block';

   
}

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


</script>


    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
  
</body>
</html>
