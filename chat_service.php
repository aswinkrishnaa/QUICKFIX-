
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat Interface</title>
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <style>
        /* Your existing styles... */
body {
            font-family: Arial, sans-serif;
            background-color: #fafafa;
            margin: 0;
            padding: 20px;
        }

        .chat-container {
            max-width: 1000px;
            height: 510px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .chat-box {
            height: 400px;
            overflow-y: scroll;
            border-radius: 8px;
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 20px;
            
        }

        .message-form {
            display: flex;
            align-items: center;
        }

        .message-form input[type="text"] {
            flex: 1;
            padding: 12px;
            border-radius: 25px;
            border: 1px solid #ddd;
            margin-right: 10px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .message-form input[type="text"]:focus {
            border-color: #999;
        }

        .message-form button {
            padding: 12px 20px;
            border-radius: 25px;
            border: none;
            background-color: #3897f0;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .message-form button:hover {
            background-color: #2680c2;
        }

        .message-form label {
            display: block;
            margin-right: 10px;
            cursor: pointer;
        }

        .message-form input[type="file"] {
            display: none;
        }

        .message-form i {
            font-size: 24px;
            color: #3897f0;
            cursor: pointer;
        }

    
        .chat-box p {
            margin: 5px 0;
            padding: 10px;
            border-radius: 15px;
/*            word-wrap: break-word;*/
        }

        .chat-message.user {
            background-color: #d6e6ff;
            float: left;
        }

        .chat-message.sender {
            background-color: #e7e7e7;
            float: right;
        }

        .chat-message time {
            display: block;
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }
   
    </style>
</head>
<body>
     <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="index.php" style=" color:  #2980b9; font-size: 20px;"><img src="fixlogo.png" width="25px" height="25px" style="margin-right: 1px;">uickFix</a>
        <!-- Add a responsive button for small screens -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto" style="margin-right: 25px;">
                <li class="nav-item active">
                    <a class="nav-link" href="servicehome.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['service_provider_id'])) {
        echo "something went wromh";//        header("Location: servicehome.php");
        exit();
    }
    
      $complaintId = $_GET['complaint_id'];
$receiverId = $_GET['service_provider_id'];
$senderId = $_GET['user_id'];
    
//  echo  $complaintId;
//echo $receiverId;
//echo $senderId;
    
 
    

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

    // Function to retrieve chat messages
function fetchMessages($conn, $complaintId) {
    $retrieveQuery = "SELECT sender_id, receiver_id, message, timestamp, messagefrom
                      FROM chat_messages
                      WHERE complaint_id = ?
                      ORDER BY timestamp ASC";

    $stmt = $conn->prepare($retrieveQuery);
    $stmt->bind_param("i", $complaintId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}


    
    ?> 
    <audio id="successSound">
    <source src="./sounds/interface-124464.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function playSuccessSound() {
        var audioElement = document.getElementById("successSound");
        if (audioElement !== null) {
            audioElement.play();
        }
    }
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
$receiverId = $_GET['service_provider_id'];
$senderId = $_GET['user_id'];
    $message = $_POST['message'];

    // Insert message into the database
    $insertQuery = "INSERT INTO chat_messages (sender_id, receiver_id, message, timestamp, complaint_id, messagefrom)
                VALUES (?, ?, ?, NOW(), ?, 'sp')";


    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iisi", $senderId, $receiverId, $message, $complaintId);
    
    if ($stmt->execute()) {
        // Message inserted successfully
        echo 'playSuccessSound();';
            echo 'console.log("Message inserted successfully!");';
    } else {
        // Failed to insert message
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
       ?>

    // Other JavaScript code...
});
</script>
    <?php

    // Retrieve and display chat messages
    
    $complaintId = $_GET['complaint_id'];
$receiverId = $_GET['service_provider_id'];


   $messageResult = fetchMessages($conn, $complaintId);



    ?>

    <!-- Chat interface -->
    <div class="chat-container">
        
    <div class="chat-box">
        <?php
        // Display chat messages
      while ($row = $messageResult->fetch_assoc()) {
    // Define classes based on sender
    $messageClass = ($row['messagefrom'] === 'sp') ? 'sender' : 'user';
    $bgColor = ($row['messagefrom'] === 'sp') ? '#d6e6ff' : '#e7e7e7';

    echo '<p class="chat-message ' . $messageClass . '" style="background-color: ' . $bgColor . '">';
    echo htmlspecialchars($row['message']); // Ensure proper HTML escaping
    echo '<time>' . $row['timestamp'] . '</time>';
    echo '</p>';
    echo '<br><br><br><br>';
}
        ?>
    </div>
    <!-- Your message form -->
    <form method="post" class="message-form">
        <input type="text" name="message" placeholder="Type a message...">
        
        <button type="submit">Send</button>
    </form>
        
</div>
    <audio id="successSound">
        <source src="./sounds/interface-124464.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <!-- Your JavaScript for handling real-time updates (if needed) -->
    <script>
document.addEventListener("DOMContentLoaded", function() {
    function fetchNewMessagesAndScroll() {
        const chatBox = document.querySelector(".chat-box");

        // Make an AJAX request to fetch new messages
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // On success, update the chat box with new messages
                    chatBox.innerHTML = xhr.responseText;

                    // Scroll to the bottom of the chat box
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            }
        };

        // Send GET request to a PHP file that retrieves new messages
        xhr.open("GET", "fetch_new_messages.php?complaint_id=<?php echo $complaintId; ?>", true);
        xhr.send();
    }

    // Fetch new messages initially and then every 10 seconds
    fetchNewMessagesAndScroll(); // Fetch immediately on page load
    setInterval(fetchNewMessagesAndScroll, 10000); // 10000 milliseconds = 10 seconds

    // Your existing code for sending messages
    // ...
});
</script>
<!--
    <script>
document.addEventListener("DOMContentLoaded", function() {
    function refreshPage() {
        location.reload(); // Reload the current page
    }

    // Refresh the page every 10 seconds (10000 milliseconds)
    setTimeout(refreshPage, 10000);
});
</script>
-->
 




    <?php
    // Handle sending messages (same as your code)
    // ...

    // Close your database connection
    $conn->close();
    ?>
</body>
</html>


