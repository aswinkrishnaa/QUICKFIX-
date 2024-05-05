<?php
session_start(); // Start the session if not already started

// Check if the user is logged in or not. Redirect if not logged in.
if (!isset($_SESSION['service_provider_id'])) {
    header("Location: servicehome.php");
    exit();
}

// Handle the form submission
if (isset($_POST['submit_reply'])) {
    // Database connection code (replace with your credentials)
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "rootroot";
    $dbname = "quickfix";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the reply message and complaint ID from the form
    $replyMessage = $_POST['reply_message'];
    $complaintId = $_POST['complaint_id'];

    // Prepare and execute an SQL UPDATE statement
    $sql = "UPDATE feedback_ratings SET reply_message = ? WHERE complaint_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $replyMessage, $complaintId);

    if ($stmt->execute()) {
        // Update successful
        // You can add a success message or redirect back to the complaint details page
        header("Location: detailedcomplaint.php?complaint_id=$complaintId"); // Redirect to the complaint details page
    } else {
        // Handle the error appropriately, e.g., display an error message
        echo "Error: " . $stmt->error;
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    // Handle the case when the form is not submitted
    header("Location: servicehome.php"); // Redirect to the service provider's home page
    exit();
}
?>
