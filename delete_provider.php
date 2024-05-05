<?php
// Check if the ID parameter is set in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $provider_id = $_GET['id'];
    
    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "rootroot";
    $dbname = "quickfix";
    
    // Create a new database connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check if the connection is successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Define the SQL query to delete the service provider by ID
    $sql = "DELETE FROM service_providers WHERE id = ?";
    
    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error in preparing the statement: " . $conn->error);
    }
    
    // Bind the ID parameter to the SQL statement
    $stmt->bind_param("i", $provider_id);
    
    // Execute the SQL statement
    if ($stmt->execute()) {
        // Delete was successful
//        echo "Service provider with ID $provider_id has been deleted successfully.";
        echo '<script>alert("Service providerhas been Removed successfully.")</script>';
        header("Location: view_serviceprovider.php");
    } else {
        // Delete failed
        echo '<script>alert("Error deleting service provider.")</script>';
        header("Location: view_serviceprovider.php");
    }
    
    // Close the prepared statement and the database connection
    $stmt->close();
    $conn->close();
} else {
    // Invalid ID parameter
    echo "Invalid ID parameter.";
}
?>
