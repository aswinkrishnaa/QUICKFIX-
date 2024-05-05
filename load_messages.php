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

// Retrieve and display messages
$userId = $_SESSION['user_id']; // Replace with the actual user_id
$serviceId = $_SESSION['service_id']; // Replace with the actual service_id
$complaintId = $_SESSION['cmp_id']; // Replace with the actual complaint_id

$retrieveSql = "SELECT sender_id, message, complaint_id FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) AND complaint_id = ? ORDER BY timestamp ASC";
$stmt = $conn->prepare($retrieveSql);
$stmt->bind_param("iiiii", $userId, $serviceId, $serviceId, $userId, $complaintId);
$stmt->execute();
$result = $stmt->get_result();

$messages = array();

while ($row = $result->fetch_assoc()) {
    $senderId = $row['sender_id'];
    $message = $row['message'];
    $timestamp = $row['timestamp']; // Assuming you have a 'timestamp' column in your database

    // Determine the CSS class and the sender label
    $cssClass = ($senderId == 1) ? 'user-message' : 'service-provider-message';
    $senderLabel = ($senderId == 1) ? 'User' : 'Service Provider';

    // Create a message HTML block with sender, timestamp, and styling
    $messageHtml = '<div class="message ' . $cssClass . '">';
    $messageHtml .= '<div class="message-sender">' . htmlspecialchars($senderLabel) . '</div>';
    $messageHtml .= '<div class="message-text">' . htmlspecialchars($message) . '</div>';
    $messageHtml .= '<div class="message-timestamp">' . htmlspecialchars($timestamp) . '</div>';
    $messageHtml .= '</div>';

    // Add this message block to the messages array
    $messages[] = $messageHtml;
}


$stmt->close();
$conn->close();

// Send the messages as a JSON response
header('Content-Type: application/json');
echo json_encode($messages);
?>
