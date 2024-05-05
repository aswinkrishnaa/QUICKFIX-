<?php
// Replace these variables with your actual database credentials
$servername = "localhost";
$username = "root";
$password = "rootroot";
$dbname = "quickfix";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$complaintId = $_GET['complaint_id'];

// Retrieve new chat messages
$retrieveQuery = "SELECT sender_id, receiver_id, message, timestamp, messagefrom
                  FROM chat_messages
                  WHERE complaint_id = ?
                  ORDER BY timestamp ASC";

$stmt = $conn->prepare($retrieveQuery);
$stmt->bind_param("i", $complaintId);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Construct the HTML response with the new messages
$response = '';
while ($row = $result->fetch_assoc()) {
    $messageClass = ($row['messagefrom'] === 'usr') ? 'user' : 'sender';
    $bgColor = ($row['messagefrom'] === 'usr') ? '#d6e6ff' : '#e7e7e7';

    $response .= '<p class="chat-message ' . $messageClass . '" style="background-color: ' . $bgColor . '">';
    $response .= htmlspecialchars($row['message']);
    $response .= '<time>' . $row['timestamp'] . '</time>';
    $response .= '</p>';
    $response .= '<br><br><br><br>';
}

echo $response;

// Close the database connection
$conn->close();
?>
