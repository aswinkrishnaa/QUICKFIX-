<?php
session_start();

function getUserIdByEmail($conn, $userEmail) {
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $userEmail);
    $stmt->execute();

    if ($stmt->error) {
        die("Execute failed: " . $stmt->error);
    }

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

    if (!$userId) {
        die("User ID not found");
    }

    // Query to retrieve unread notifications for the user
    $sql = "SELECT message, link, created_at FROM notifications WHERE destination_id = ? AND is_read = 0 ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();

    if ($stmt->error) {
        die("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($stmt->error) {
        die("Get result failed: " . $stmt->error);
    }

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
                max-width: 900px;
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
            
              .navbar {
            background-color: lightsteelblue; /* Navbar background color */

        }
        .navbar-brand {
            color: navy;
            
        }
        .navbar-brand,
        .navbar-nav .nav-link {
            color: #fff; /* Navbar text color */
        }

        .navbar-brand:hover,
        .navbar-nav .nav-link:hover {
            color: #ff5722;
            /* Navbar text color on hover */
        }

        /* Dropdown Styles */
        .dropdown-menu {
            background-color:aliceblue;
            /* Dropdown background color */
        }

        .dropdown-item {
            color: black; /* Dropdown text color */
        }

        .dropdown-item:hover {
            color: blueviolet;
        }

        </style>
    </head>
    <body>
       
        <!-- Navbar -->
   <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="index.php" style=" color:  #2980b9; font-size: 20px;"><img src="fixlogo.png" width="25px" height="25px" style="margin-right: 1px;">uickFix</a>
        <!-- Add a responsive button for small screens -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto" style="margin-right: 25px;">
                <li class="nav-item active">
                    <a class="nav-link" href="userhome.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <h1 style="margin-top: 15px; margin-bottom: 25px;">Notifications</h1>

    <div class="notifications-container">
        <?php
        // Display notifications
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $message = $row['message'];
                $link = $row['link'];
                $created_at = $row['created_at'];

                // Display the notification message, link, and timestamp
                echo "<div class='notification'>";
                echo "<p>$message</p>";
                echo "<a href='$link'>View Details</a>";
                echo "<p>Created at: $created_at</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No unread notifications.</p>";
        }
        ?>
    </div>

    <!-- Add your additional HTML content here -->

    <!-- Include any necessary JavaScript or CSS files here if needed -->
<!-- Include Bootstrap JS and jQuery (if not already included) -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php

    $stmt->close();
    $conn->close();
} else {
    echo "User is not logged in";
}
?>
