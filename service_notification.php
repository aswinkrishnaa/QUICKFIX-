<?php
session_start();

function getUserIdByEmail($conn, $userEmail) {
    
    $sql = "SELECT id FROM service_providers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->bind_result($userId);

    if ($stmt->fetch()) {
        
        return $userId;
        
    } else {
        return null;
    }
    $stmt->close();
}

if (isset($_SESSION['user_email'])) {
    // Database connection code
    $servername = "localhost";
    $dbUsername = "root"; // Change this to your database username
    $dbPassword = "rootroot"; // Change this to your database password
    $dbname = "quickfix";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the user's ID based on their email
    $userEmail = $_SESSION['user_email'];
    $userId = getUserIdByEmail($conn, $userEmail);
    

    // Query to retrieve notifications
    $sql = "SELECT message, link, created_at, notification_type, location_id, destination_id FROM notifications WHERE destination_id = ? AND is_read = 0 ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    

    // Fetch the service provider's preferred location_id
    $preferredLocationId = null;
    $locationQuery = "SELECT preferred_location_id FROM service_providers WHERE id = ?";
    $locationStmt = $conn->prepare($locationQuery);
    $locationStmt->bind_param("i", $userId); // Replace with the actual service provider ID
    $locationStmt->execute();
    $locationStmt->bind_result($preferredLocationId);
    $locationStmt->fetch();
    $locationStmt->close();
    echo $preferredLocationId;

    

    // HTML code for the notifications page
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Notifications</title>
        <!-- Add your CSS styles here if needed -->
        <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
        }

        h1 {
            text-align: center;
        }

        .notifications-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .notification {
            border: 1px solid #ccc;
            margin: 10px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .notification a {
            color: #3498db;
            text-decoration: none;
            margin-top: 5px;
            display: inline-block;
        }
        
        </style>
    </head>
    <body>
        <h1>Notifications</h1>

        <div class="notifications-container">
            <?php
            // Display notifications
            if ($result->num_rows > 0) {
                echo "true";
                while ($row = $result->fetch_assoc()) {
                    $message = $row['message'];
                    $link = $row['link'];
                    $created_at = $row['created_at'];
                    $notification_type = $row['notification_type'];
                    $location_id = $row['location_id'];
                    $destination_id = $row['destination_id'];
                    

                    // Check notification type
                    if ($notification_type === 'complaintreported') {
                        // Check location_id against preferred_location_id
                       
    
                        
                        if ($location_id === $preferredLocationId) {
                            // Display the notification
                            echo "<div class='notification'>";
                            echo "<p>$message</p>";
                            echo "<a href='$link'>View Details</a>";
                            echo "<p>Created at: $created_at</p>";
                            echo "</div>";
                        }
                        }
                        
                     else {
                        // For other notification types
                        // Display the notification as needed
                        echo "<div class='notification'>";
                        echo "<p>$message</p>";
                        echo "<a href='$link'>View Details</a>";
                        echo "<p>Created at: $created_at</p>";
                        echo "</div>";
                    }
                }
            } else {
                echo "<p>No unread notifications.</p>";
            }
            ?>
        </div>

        <!-- Add your additional HTML content here -->

        <!-- Include any necessary JavaScript or CSS files here if needed -->

    </body>
    </html>
    <?php
    $stmt->close();
    $conn->close();
} else {
    echo "User is not logged in";
}
?>
