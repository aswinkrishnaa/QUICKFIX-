<?php
// Start the session (if it's not already started)
session_start();

// Check if the user is logged in or not. Redirect if not logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: userhome.php"); // Redirect to your login page
    exit();
}else {
//    echo $_SESSION['user_id'];
//    echo $_SESSION['service_id'];
//    echo $_GET['complaint_id'];
    $_SESSION['cmp_id'] = $_GET['complaint_id'];
    $serviceProviderId = $_GET['service_provider_id']; // Retrieve the service provider ID from the URL
    $_SESSION['service_provider_id'] = $serviceProviderId;
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickFix Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            text-align: center;
            margin: 20px 0;
        }

        #container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
            color: #007bff;
            cursor: pointer;
        }

        a:hover {
            text-decoration: underline;
        }

        #chatbox {
            border: 1px solid #ccc;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
            margin-bottom: 20px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
        
        
    </style>
</head>
<body>
    <h1 style=" position: absolute; left:20px; top: 10px; font-size: 20px;"><a href="userhome.php" style="text-decoration: none; color: #2980b9;">QuickFix</a></h1>
    <h1>FAQ</h1>

    <div id="container">
        <h2>Frequently Asked Questions</h2>
        <ul>
            <li><a href="#" onclick="askQuestion('How to report a complaint?')">How to report a complaint?</a></li>
            <li><a href="#" onclick="askQuestion('How can I track my complaint status?')">How can I track my complaint status?</a></li>
            <li><a href="#" onclick="askQuestion('What are the payment options?')">What are the payment options?</a></li>
        </ul>

        <h2>Chatbot</h2>
        <div id="chatbox">
            <p>Chatbot: Hello! How can I assist you today?</p>
        </div>

        <h2>Chat with Service Provider</h2>
        <button onclick="connectToServiceProvider()">Chat with Service Provider</button>
    </div>

    <script>
        function askQuestion(question) {
            addToChatbox('me: ' + question);
            let response = getChatbotResponse(question);
            addToChatbox('Chatbot: ' + response);
        }

        function addToChatbox(message) {
            let chatbox = document.getElementById('chatbox');
            chatbox.innerHTML += '<p>' + message + '</p>';
            chatbox.scrollTop = chatbox.scrollHeight;
        }

        function getChatbotResponse(question) {
            if (question.includes('complaint')) {
                return 'You can report a complaint by going to the "Complaints" section and clicking on the "Report a Complaint" button.';
            } else if (question.includes('status')) {
                return 'To track your complaint status, visit the "My Complaints" section and select your complaint for details.';
            } else if (question.includes('payment')) {
                return 'We accept payments through credit/debit cards and online wallets or by cash. You can make payments when your complaint is resolved.';
            } else {
                return 'I\'m sorry, I couldn\'t understand your question. Please ask another question or use our "Chat with Service Provider" option.';
            }
        }

        function connectToServiceProvider() {
    // Assuming you have the URL parameters senderId and receiverId defined here
     let serviceProviderId = <?php echo $_SESSION['service_id']; ?>;
    let complaintId = <?php echo $_SESSION['cmp_id']; ?>;
    let userid = <?php echo $_SESSION['user_id']; ?>;
    
    // Redirect to chat.php with senderId, receiverId, and complaintId as URL parameters
    window.location.href = `chat_user.php?senderId=${userid}&receiverId=${serviceProviderId}&complaintId=${complaintId}`;
}

    </script>
    <script>
        function connectToServiceProvider() {
            // Retrieve necessary values
            let serviceProviderId = <?php echo $_SESSION['service_provider_id']; ?>;
            let complaintId = <?php echo $_SESSION['cmp_id']; ?>;
            let userId = <?php echo $_SESSION['user_id']; ?>;
            
            // Redirect to chat_user.php with senderId, receiverId, and complaintId as URL parameters
            window.location.href = `chat_user.php?senderId=${userId}&receiverId=${serviceProviderId}&complaintId=${complaintId}`;
        }
    </script>
</body>
</html>
