<?php
// Start the session (if it's not already started)
session_start();

// Check if the user is logged in or not. Redirect if not logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: userhome.php"); // Redirect to your login page
    exit();
}

// Database configuration (replace with your credentials)
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "rootroot";
$dbName = "quickfix";



// Create a database connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle message submission
if (isset($_POST['message'])) {
    $newMessage = $_POST['message'];

    // Insert the new message into the database
    $sql = "INSERT INTO messages (sender_id, receiver_id, message, timestamp, complaint_id)
            VALUES (?, ?, ?, NOW(), ?)";

    // Replace these with the appropriate sender and receiver IDs
    $senderId = $_SESSION['user_id'];
    $receiverId = $_SESSION['service_id'];
    $complaintid = $_SESSION['cmp_id'];

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisi", $senderId, $receiverId, $newMessage, $complaintid);

    if ($stmt->execute()) {
        // Message inserted successfully
        echo "Message Sent";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
