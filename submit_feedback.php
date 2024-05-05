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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $complaintId = $_POST['complaint_id'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];
    $serviceProviderId = $_SESSION['servicepro_id'];

    // Validate input data
    $errors = [];

    if (empty($rating) || !is_numeric($rating) || $rating < 1 || $rating > 5) {
        $errors[] = "Rating must be a number between 1 and 5.";
    }

    if (empty($feedback)) {
        $errors[] = "Feedback cannot be empty.";
    }

    if (!empty($errors)) {
        // Display validation errors
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        // Insert feedback and ratings into the database
        $insertFeedbackQuery = "INSERT INTO feedback_ratings (user_id, service_provider_id, complaint_id, feedback, rating)
                            VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertFeedbackQuery);
        $stmt->bind_param("iiisi", $_SESSION['user_id'], $serviceProviderId, $complaintId, $feedback, $rating);

        if ($stmt->execute()) {
            
           

            // Redirect to a success page or any other page you prefer
            echo '<script>alert("Feedback submitted !!!")</script>';
            header("Location: userdetailedcomplaint.php?complaint_id=$complaintId");
            exit();
        } else {
            // Handle the error, e.g., display an error message
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>
