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
if (isset($_POST['send'])) {
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
        exit; // Exit to prevent further execution
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Retrieve and display messages
$retrieveSql = "SELECT sender_id, message FROM messages WHERE receiver_id = ? ORDER BY timestamp ASC";
$stmt = $conn->prepare($retrieveSql);
$stmt->bind_param("i", $receiverId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $senderId = $row['sender_id'];
    $message = $row['message'];

    // You can customize the message display based on the sender (e.g., different styling)
    if ($senderId == 1) {
        echo '<div class="message user-message">' . htmlspecialchars($message) . '</div>';
    } else {
        echo '<div class="message service-provider-message">' . htmlspecialchars($message) . '</div>';
    }
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Service Provider</title>
    <style>
        /* Your CSS styles here (same as before) */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .chat-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            padding: 20px;
        }

        .chat-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .chat-messages {
            max-height: 400px;
            overflow-y: scroll;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .message {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }

        .user-message {
            background-color: #3498db;
            color: #fff;
            text-align: right;
        }

        .service-provider-message {
            background-color: #e1e1e1;
            color: #333;
            text-align: left;
        }

        .chat-input {
            margin-top: 20px;
        }

        .chat-input form {
            display: flex;
        }

        .chat-input input[type="text"] {
            flex-grow: 2;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .chat-input button {
            padding: 10px 15px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .chat-input button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat with Service Provider</h2>
        </div>
        <div class="chat-messages" id="chat-messages">
            <!-- Messages will be displayed here -->
        </div>
        <div class="chat-input">
            <form id="message-form" onsubmit="return sendMessage();">
                <input type="text" name="message" id="message" placeholder="Type your message..." required>
                <button type="submit" name="send">&#9658;</button>
            </form>
        </div>
    </div>

    <script>
        function sendMessage() {
            // Get the message input field value
            var message = document.getElementById("message").value;

            // Check if the message is not empty
            if (message.trim() !== "") {
                // Send the message using AJAX
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "send_message.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Handle the response (e.g., display success message)
                        document.getElementById("message").value = ""; // Clear the input field
                        loadMessages(); // Reload messages to display the sent message
                    }
                };

                xhr.send("message=" + encodeURIComponent(message));

                return false; // Prevent the form from submitting via standard HTML form submission
            }

            // If the message is empty, prevent the form from submitting
            return false;
        }

        // Function to load and display messages
        function loadMessages() {
    var chatMessages = document.querySelector(".chat-messages");

    // Send an AJAX request to load messages from the server
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "load_messages.php", true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Parse the JSON response into an array of messages
            var messages = JSON.parse(xhr.responseText);

            // Clear the chatMessages div
            chatMessages.innerHTML = '';

            // Append each message to the chatMessages div
            messages.forEach(function (message) {
                chatMessages.innerHTML += message;
            });

            // Scroll to the bottom to show the latest message
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    };

    xhr.send();
}

// Load messages initially
loadMessages();

// Set up a timer to periodically load messages (e.g., every 5 seconds)
setInterval(loadMessages, 5000); // Adjust the interval as needed
    </script>
</body>
</html>
