<?php
// Start the session (if it's not already started)
session_start();

// Check if the user is logged in or not. Redirect if not logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: userhome.php"); // Redirect to your login page
    exit();
}

// Database connection code (replace with your credentials)
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "rootroot";
$dbname = "quickfix";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the complaint_id parameter is provided in the URL
if (isset($_GET['complaint_id'])) {
    $complaintId = $_GET['complaint_id'];

    // Query to check if the complaint status is 'pending'
    $checkStatusSql = "SELECT status FROM complaints WHERE id = ?";
    $stmt = $conn->prepare($checkStatusSql);
    $stmt->bind_param("i", $complaintId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($status);
        $stmt->fetch();

        if ($status === 'pending') {
            // If the complaint status is 'pending', you can proceed with the removal
            $deleteSql = "DELETE FROM complaints WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("i", $complaintId);
            $deleteStmt->execute();

            // Redirect to the view_complaints.php page after removal
            header("Location: view_complaints.php");
            exit();
        } else {
            // If the complaint status is not 'pending', redirect back to view_complaints.php
            header("Location: view_complaints.php");
            exit();
        }
    }
}

// Close the database connection
$conn->close();
?>
