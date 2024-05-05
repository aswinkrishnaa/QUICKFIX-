<?php
// Start the session (if it's not already started)
session_start();

// Check if the user is logged in as a service provider or not. Redirect if not logged in.
if (!isset($_SESSION['service_provider_id'])) {
    header("Location: servicehome.php"); // Adjust the login page URL
    exit();
}

// Function to get the user ID of the complaint owner
function getComplaintOwnerId($complaintId, $conn) {
    // Replace 'complaints' with your actual table name, and 'user_id' with your actual column name for the user ID.
    $sql = "SELECT user_id FROM complaints WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaintId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['user_id'];
    } else {
        return null; // Handle the case where the complaint is not found or other errors
    }
}

// Create a database connection
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "rootroot";
$dbname = "quickfix";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['accept'])) {
    // Handle Accept Button Click
    $complaintId = $_POST['complaint_id'];
    $servid = $_SESSION['service_provider_id'];

    $complaintOwnerId = getComplaintOwnerId($complaintId, $conn);

    // Update the status to 'Accepted' in the database
    $updateSql = "UPDATE complaints SET status = 'accepted', service_provider_id = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ii", $servid, $complaintId);
    $stmt->execute();
    $stmt->close();

    // Create a notification with the message containing the service provider's name
    if ($complaintOwnerId !== null) {
        // You should replace 'service_providers' and 'name' with your actual table and column names for service providers and their names.
        $spNameSql = "SELECT name FROM service_providers WHERE id = ?";
        $spNameStmt = $conn->prepare($spNameSql);
        $spNameStmt->bind_param("i", $servid);
        $spNameStmt->execute();
        $spNameResult = $spNameStmt->get_result();
        $spNameRow = $spNameResult->fetch_assoc();
        $serviceProviderName = $spNameRow['name'];

        $notificationMessage = "Your complaint with id $complaintId has been accepted by $serviceProviderName.";
        $link = 'userdetailedcomplaint.php?complaint_id= '.$complaintId;
        // Insert the notification message into your notifications table along with the necessary data.
        // Replace 'notifications' with your actual table name and map data to the corresponding columns.
        $insertNotificationSql = "INSERT INTO notifications (message, destination_id, from_id, notification_type, user_type, link) VALUES (?, ?, ?, ?, ?, ?)";
        $insertNotificationStmt = $conn->prepare($insertNotificationSql);
    
        // You may need to define destination_id and from_id based on your table structure.
        // For example, if destination_id is the user ID who should receive the notification and from_id is the service provider's ID, use appropriate values.
        $destinationId = $complaintOwnerId; // The user who should receive the notification
        $fromId = $servid; // The service provider's ID
        $notificationType = "complaint_accepted"; // Define your notification type
        $userType = "service_provider"; // Define the user type

        $insertNotificationStmt->bind_param("siisss", $notificationMessage, $destinationId, $fromId, $notificationType, $userType, $link);
        $insertNotificationStmt->execute();
    }

    // Redirect back to the detailedcomplaint.php page
    header("Location: detailedcomplaint.php?complaint_id=$complaintId");
    exit();
}

if (isset($_POST['submit_rejection'])) {
    // Handle submission of the rejection reason
    $complaintId = $_POST['complaint_id'];
    $reason = $_POST['rejection_reason'];

    $complaintOwnerId = getComplaintOwnerId($complaintId, $conn);

    // Update the status to 'Rejected' and store the rejection reason in the database
    $updateSql = "UPDATE complaints SET status = 'rejected', rejection_reason = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $reason, $complaintId);
    $stmt->execute();
    $stmt->close();
    
    // Create a notification with the message containing the service provider's name
    if ($complaintOwnerId !== null) {
        // You should replace 'service_providers' and 'name' with your actual table and column names for service providers and their names.
        $spNameSql = "SELECT name FROM service_providers WHERE id = ?";
        $spNameStmt = $conn->prepare($spNameSql);
        $spNameStmt->bind_param("i", $servid);
        $spNameStmt->execute();
        $spNameResult = $spNameStmt->get_result();
        $spNameRow = $spNameResult->fetch_assoc();
        $serviceProviderName = $spNameRow['name'];

        $notificationMessage = "Your complaint with id $complaintId has been rejected by $serviceProviderName.";

        // Insert the notification message into your notifications table along with the necessary data.
        // Replace 'notifications' with your actual table name and map data to the corresponding columns.
        $insertNotificationSql = "INSERT INTO notifications (message, destination_id, from_id, notification_type, user_type, link) VALUES (?, ?, ?, ?, ?, ?)";
        $insertNotificationStmt = $conn->prepare($insertNotificationSql);
    
        // You may need to define destination_id and from_id based on your table structure.
        // For example, if destination_id is the user ID who should receive the notification and from_id is the service provider's ID, use appropriate values.
        $destinationId = $complaintOwnerId; // The user who should receive the notification
        $fromId = $servid; // The service provider's ID
        $notificationType = "complaint_rejected"; // Define your notification type
        $userType = "service_provider"; // Define the user type

        $insertNotificationStmt->bind_param("siiss", $notificationMessage, $destinationId, $fromId, $notificationType, $userType, $link);
        $insertNotificationStmt->execute();
    }

    // Redirect back to the detailedcomplaint.php page
    header("Location: detailedcomplaint.php?complaint_id=$complaintId");
    exit();
}

// Close the database connection
$conn->close();
?>
