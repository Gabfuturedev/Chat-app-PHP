<?php
include "../conn.php"; // Database connection

$sender_id = $_POST['sender_id'];
$recipient_id = $_POST['recipient_id'];
$message = $_POST['message'];
$reply_to_id = isset($_POST['reply_to']) ? $_POST['reply_to'] : null; // Get the reply to message ID if provided

// Handle file upload
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    // Define the upload directory
    $uploadDir = '../uploads/';
    $imageName = basename($_FILES['image']['name']);
    $targetFilePath = $uploadDir . $imageName; // Ensure the file path includes the directory

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        $imagePath = $imageName; // Store just the image name or the path based on your preference
    } else {
        echo "Error uploading image.";
        exit;
    }
}

// Prepare the SQL statement
$sql = "INSERT INTO messages (sender_id, receiver_id, message, file_path, reply_to) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Check if the prepare() was successful
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

// Bind the parameters (note: bind 'i' for integers and 's' for strings)
$stmt->bind_param('iissi', $sender_id, $recipient_id, $message, $imagePath, $reply_to_id);

// Execute the statement
if ($stmt->execute()) {
    echo "Message sent successfully!";
} else {
    echo "Error sending message: " . $stmt->error;
}

// Close the statement
$stmt->close();

// Close the connection
$conn->close();
?>
