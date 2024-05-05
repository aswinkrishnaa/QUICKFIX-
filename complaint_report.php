
<?php
session_start();

function getUserNameByEmail($conn, $email) {
    $sql = "SELECT name FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId);

    if ($stmt->fetch()) {
        return $userName;
    } else {
        return null;
    }
    $stmt->close();
}

if (isset($_SESSION['user_email'])) {
    $userEmail = $_SESSION['user_email']; // Store the user's email in a variable

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // The form has been submitted, process the data
        $providerCategory = $_POST["provider_category"];
        $issueType = $_POST["issue_type"];
        $description = $_POST["description"];
        $locationId = $_POST["location"];
        $severity = $_POST["severity"];

        // Validate and sanitize user input (you can use your existing validation functions)
        $providerCategory = validateInput($providerCategory);
        $issueType = validateInput($issueType);
        $description = validateInput($description);
        $locationId = validateInput($locationId);
        $severity = validateInput($severity);

        // Handle the uploaded photo (you can implement this part)
        $photoPath = uploadPhoto();

        // Database connection code (replace with your database credentials)
        $servername = "localhost";
        $dbUsername = "root"; // Change this to your database username
        $dbPassword = "rootroot"; // Change this to your database password
        $dbname = "quickfix";

        $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $userId = getUserIdByEmail($conn, $userEmail);
        $userName = getUserNameByEmail($conn, $userEmail);

        // Insert the complaint into the complaints table
        $sql = "INSERT INTO complaints (provider_category, user_id, issue_type, description, location_id, severity, photo_of_issue, submission_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisssss", $providerCategory, $userId, $issueType, $description, $locationId, $severity, $photoPath);

        
if ($stmt->execute()) {
    // Successfully registered the complaint
    // Insert a notification record for the user
    $notificationMessage = "New complaint received from $userName.";
    $notificationLink = "list_complaints.php"; // Replace with the appropriate link
    $nottype = "complaintreported";
    $usertype = "service_provider";
    $sql = "INSERT INTO notifications (from_id, message, link, created_at, location_id, notification_type, user_type)
          VALUES (?, ?, ?, NOW(),?,?, ?)";
//
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $userId, $notificationMessage, $notificationLink, $locationId, $nottype, $usertype);

    if ($stmt->execute()) {
        // Successfully inserted the notification
    } else {
        // Error in notification insertion
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
//    insertNotificationRecord($conn, $userId, $notificationMessage, $notificationLink);

    echo '<script>alert("Complaint reported")</script>';
    echo '<script>window.location.href = "complaint_report.php";</script>';
    exit(); // Terminate script execution
} else {
    // Error in complaint registration
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Function to insert a notification record
//function insertNotificationRecord($conn, $userId, $message, $link) {
//    $sql = "INSERT INTO notifications (user_id, message, link, created_at)
//            VALUES (?, ?, ?, NOW())";
//
//    $stmt = $conn->prepare($sql);
//    $stmt->bind_param("iss", $userId, $message, $link);
//
//    if ($stmt->execute()) {
//        // Successfully inserted the notification
//    } else {
//        // Error in notification insertion
//        echo "Error: " . $stmt->error;
//    }
//
//    $stmt->close();
//}
        
        
        $conn->close();
    }
} else {
    echo "User is not logged in";
}


// Validate and sanitize functions
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handle file upload (you can implement this part)
function uploadPhoto() {
    // Check if a file was uploaded without errors
    if (isset($_FILES["photo_of_issue"]) && $_FILES["photo_of_issue"]["error"] == 0) {
        $targetDir = "uploads/complaints_photo"; // Specify the directory where you want to store the uploaded files
        $targetFile = $targetDir . basename($_FILES["photo_of_issue"]["name"]);
        
        // Check if the file already exists (you can choose to overwrite or generate a new name)
        if (file_exists($targetFile)) {
            // Handle the case where the file already exists
            // You can choose to rename the file or generate a unique name
            // For example: $targetFile = $targetDir . uniqid() . "_" . basename($_FILES["photo_of_issue"]["name"]);
        }
        
        // Check the file size (adjust this value to your needs)
        if ($_FILES["photo_of_issue"]["size"] > 500000) {
            // Handle the case where the file size is too large
            return ""; // Return an empty string to indicate an error
        }
        
        // Allow only specific file types (you can modify this list)
        $allowedTypes = array("jpg", "jpeg", "png", "gif");
        $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedTypes)) {
            // Handle the case where the file type is not allowed
            return ""; // Return an empty string to indicate an error
        }
        
        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES["photo_of_issue"]["tmp_name"], $targetFile)) {
            return $targetFile; // Return the path to the uploaded file
        } else {
            // Handle the case where the file could not be moved
            return ""; // Return an empty string to indicate an error
        }
    } else {
        // Handle the case where no file was uploaded or an error occurred during upload
        return ""; // Return an empty string to indicate an error
    }
}

function getUserIdByEmail($conn, $email) {
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId);

    if ($stmt->fetch()) {
        return $userId;
    } else {
        return null;
    }
    $stmt->close();
}



?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Report</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        label {
            font-weight: bold;
        }

        select,
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        select {
            height: 40px;
        }

        button[type="submit"] {
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }
        
        /* Navbar Styles */
        .navbar {
            background-color: Azure; /* Dark background color */
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); /* Shadow effect */
        }

        .navbar-brand {
            color: #fff; /* Text color */
            font-size: 24px; /* Text size */
            font-weight: bold; /* Text boldness */
        }

        .navbar-toggler-icon {
            background-color: #fff; /* Color of the toggle icon bars */
        }

        .navbar-nav {
            margin-right: 25px; /* Right margin for the nav items */
        }

        .nav-item {
            margin-left: 15px; /* Left margin for each nav item */
        }

        .nav-link {
            color: #fff; /* Text color */
            font-size: 18px; /* Text size */
        }

        .nav-link:hover {
            color: #ff5722; /* Text color on hover */
        }

        /* Dropdown styles */
        .dropdown-menu {
            background-color: #343a40; /* Background color for dropdown menu */
        }

        .dropdown-item {
            color: #fff; /* Text color for dropdown items */
        }

        .dropdown-item:hover {
            background-color: #ff5722; /* Background color for dropdown items on hover */
        }
        
        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar,
        .carousel-inner,
        .carousel-caption {
            animation: fadeIn 1s ease;
        }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                <a class="nav-link" href="userhome.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="complaintsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Complaints
                </a>
                <div class="dropdown-menu" aria-labelledby="complaintsDropdown">
                    <a class="dropdown-item" href="complaint_report.php?user_email=<?php echo $userEmail; ?>">Report Complaints</a>
                    <a class="dropdown-item" href="view_complaints.php">Reported Complaints</a>
                    <a class="dropdown-item" href="#">Complaints History</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Feeds</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo $currentname; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="#">Edit Profile</a>
                    <a class="dropdown-item" href="#">Change Password</a>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
            
        </ul>
    </div>
</nav>
    <div class="container">
        <h1>Complaint Report</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="provider_category">Provider Category:</label>
            <select id="provider_category" name="provider_category" required>
                <option value="plumber">Plumber</option>
                <option value="electrician">Electrician</option>
                <option value="painter">Painter</option>
                <option value="carpenter">Carpenter</option>
                
                    <option value="interior-designer">Interior designer</option>
                    <option value="Cleaner">Cleaner</option>
                    <option value="house-keeper">House Keeper</option>
                    <option value="Gardener">Gardener</option>
                <!-- Add more options for provider categories -->
            </select>

            <label for="issue_type">Issue Type:</label>
            <input type="text" id="issue_type" name="issue_type" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="location">Location:</label>
            <select id="location" name="location" required>
                <?php
                // Database connection code (replace with your database credentials)
                $servername = "localhost";
                $dbUsername = "root"; // Change this to your database username
                $dbPassword = "rootroot"; // Change this to your database password
                $dbname = "quickfix";

                $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch locations from the database and populate the dropdown
                $query = "SELECT id, city FROM locations";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['city'] . "</option>";
                    }
                }

                $conn->close();
                ?>
            </select>

            <label for="severity">Severity:</label>
            <select id="severity" name="severity" required>
                <option value="urgent">Urgent</option>
<!--                <option value="emergency">Emergency</option>-->
                <option value="normal">Normal</option>
            </select>

            <label for="photo_of_issue">Photo of Issue:</label>
            <input type="file" id="photo_of_issue" name="photo_of_issue" accept=".jpg, .jpeg, .png" required>

            <button type="submit" style="margin-top: 25px;">Submit Complaint</button>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>


