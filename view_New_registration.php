<?php
// Include your database connection code here
$servername = "localhost";
$dbUsername = "root"; // Change this to your database username
$dbPassword = "rootroot"; // Change this to your database password
$dbname = "quickfix";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming you have a database connection, fetch new service provider registrations
$sql = "SELECT * FROM service_providers WHERE status = 'pending'";
$result = $conn->query($sql);

// Check if there are new registrations
if ($result->num_rows > 0) {
    // Display the registrations in a table
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Service Provider Registrations</title>
        <!-- Include your CSS styles here -->
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f0f4f8;
                margin: 0;
                padding: 0;
            }

            h1 {
                background-color: #333;
                color: #fff;
                text-align: center;
                padding: 34px;
                margin-top: 0;
            }

            table {
                width: 80%;
                margin: 20px auto;
                border-collapse: collapse;
                background-color: #fff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            th, td {
                padding: 12px 15px;
                text-align: left;
            }

            th {
                background-color: #444;
                color: #fff;
            }

            tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            td form {
                display: flex;
                justify-content: space-between;
            }

            button {
                padding: 5px 10px;
                border: none;
                cursor: pointer;
            }

            button[name="accept"] {
                background-color: #4CAF50;
                color: #fff;
            }
            
            button[name="accept"]:hover {
                background-color: green;
                color: #fff;
            }

            button[name="reject"] {
                background-color: #f44336;
                color: #fff;
            }
            
            button[name="reject"]:hover {
                background-color: red;
                color: #fff;
            }

            img {
                max-width: 100px;
                max-height: 100px;
            }
            
            .navbar {
    background-color: #333;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
}

.logo {
    font-size: 24px;
    font-weight: bold;
}

.menu {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
}

.menu li {
    margin-right: 20px;
}

.menu li:last-child {
    margin-right: 0;
}

.menu a {
    text-decoration: none;
    color: #fff;
    transition: color 0.3s;
}

.menu a:hover {
    color: #ff5722; /* Change the color on hover */
}

/* Dropdown styles */
.dropdown {
    position: relative;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #333;
    min-width: 160px;
    z-index: 1;
}

.dropdown-content a {
    padding: 10px;
    display: block;
    color: #444;
    text-decoration: none;
    transition: background-color 0.3s;
}

.dropdown:hover .dropdown-content {
    display: block; /* Show dropdown on hover */
}

.dropdown-content a:hover {
    background-color: azure; /* Change the background color on hover */
}


        </style>
    </head>
    <body>
    <nav class="navbar">
        <div class="logo">QuickFix</div>
        <ul class="menu">
            <li><a href="index.php">Home</a></li>
            <li class="dropdown">
                <a href="#">Users</a>
                <div class="dropdown-content">
                    <a href="#">View Users</a>
                    <a href="#">Users Complaints</a>
                    
                </div>
            </li>
            <li class="dropdown">
                <a href="#">Service Provider</a>
                <div class="dropdown-content">
                    <a href="view_New_registration.php">New Registrations</a>
                    <a href="#">View Service Providers</a>
                    
                </div>
            </li>
            <li class="dropdown">
                <a href="#">Admin</a>
                <div class="dropdown-content">
                    <a href="view_New_registration.php">Edit Profile</a>
                    <a href="logout.php">Logout</a>
                    
                </div>
            </li>
        </ul>
    </nav>
        <h1 style>New Service Provider Registrations</h1>
        <table>
            <tr>
                <th>Profile Photo</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>profession</th>
                <th>Certificate</th>
                <th>Id proof</th>
                <th>Action</th>
            </tr>';

   while ($row = $result->fetch_assoc()) {
    // Display each registration in a table row
    echo '<tr>';
    echo '<td><img src="' . $row['profile_photo'] . '" alt="Profile Photo"></td>';
    echo '<td>' . $row['name'] . '</td>';
    echo '<td>' . $row['email'] . '</td>';
    echo '<td>' . $row['phone'] . '</td>';
       echo '<td>' . $row['profession'] . '</td>';
    echo '<td><a href="' . $row['expertise_certificate'] . '" target="_blank">View Certificate</a></td>';
    echo '<td><a href="' . $row['id_proof'] . '" target="_blank">View ID Proof</a></td>';
    echo '<td>
            <form method="post" action="">
                <input type="hidden" name="provider_id" value="' . $row['id'] . '">
                <button type="submit" name="accept">Accept</button>
                <button type="submit" name="reject">Reject</button>
            </form>
          </td>';
    echo '</tr>';
}

    echo '</table></body></html>';
} else {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Service Provider Registrations</title>
       
        <style>
        *{
        margin: 0;
        }
        .navbar {
    background-color: #333;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
    padding-right:90px;
}

.logo {
    font-size: 24px;
    font-weight: bold;
}

.menu {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
}

.menu li {
    margin-right: 20px;
}

.menu li:last-child {
    margin-right: 0;
}

.menu a {
    text-decoration: none;
    color: #fff;
    transition: color 0.3s;
}

.menu a:hover {
    color: #ff5722; /* Change the color on hover */
}

/* Dropdown styles */
.dropdown {
    position: relative;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #333;
    min-width: 160px;
    z-index: 1;
}

.dropdown-content a {
    padding: 10px;
    display: block;
    color: #444;
    text-decoration: none;
    transition: background-color 0.3s;
}

.dropdown:hover .dropdown-content {
    display: block; /* Show dropdown on hover */
}

.dropdown-content a:hover {
    background-color: azure; /* Change the background color on hover */
}


        </style>
    </head>
    <body>
    <nav class="navbar">
        <div class="logo">QuickFix</div>
        <ul class="menu">
            <li><a href="adminhome.php">Home</a></li>
            <li class="dropdown">
                <a href="#">Users</a>
                <div class="dropdown-content">
                    <a href="view_users.php">View Users</a>
                    <a href="#">Users Complaints</a>
                    
                </div>
            </li>
            <li class="dropdown">
                <a href="#">Service Provider</a>
                <div class="dropdown-content">
                    <a href="view_New_registration.php">New Registrations</a>
                    <a href="#">View Service Providers</a>
                    
                </div>
            </li>
            <li class="dropdown">
                <a href="#">Admin</a>
                <div class="dropdown-content">
                    <a href="view_New_registration.php">Edit Profile</a>
                    <a href="logout.php">Logout</a>
                    
                </div>
            </li>
        </ul>
    </nav>
        <center><h1 style="background: #333; padding-bottom: 34px; padding-top: 10px; color: white; ">New Service Provider Registrations</h1>
        <h1 style= "margin-top: 100px;">No New Service Provider Registrations</h1></center>
    </body>
    </html>';
}
// Close your database connection
$conn->close();
?>

<?php

// Include your database connection code here
$servername = "localhost";
$dbUsername = "root"; // Change this to your database username
$dbPassword = "rootroot"; // Change this to your database password
$dbname = "quickfix";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["accept"])) {
        // Accept button was clicked
        $providerId = $_POST["provider_id"];

        // Update the service provider's status to "accepted" in the database
        $updateSql = "UPDATE service_providers SET status = 'approved' WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("i", $providerId);

        if ($stmt->execute()) {
    // Redirect after successful update
//    header("Location: view_New_registration.php");
            echo '<script>window.location.href = "view_New_registration.php";</script>';
    exit();
} else {
    echo "Error accepting service provider: " . $stmt->error;
    echo '<script>window.location.href = "view_New_registration.php";</script>';
    exit();
}

        $stmt->close();
    } elseif (isset($_POST["reject"])) {
        // Reject button was clicked
        $providerId = $_POST["provider_id"];

        // Update the service provider's status to "rejected" in the database
        $updateSql = "UPDATE service_providers SET status = 'rejected' WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("i", $providerId);

        if ($stmt->execute()) {
            // Redirect after successful update
            header("Location: view_New_registration.php");
            exit();
        } else {
            echo "Error rejecting service provider: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid request.";
    }
}

// Close your database connection
$conn->close();
?>


