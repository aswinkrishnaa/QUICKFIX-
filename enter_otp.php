<?php
// Check if the complaint_id is set in the URL
if (isset($_GET['complaint_id'])) {
    $complaintId = $_GET['complaint_id'];

    $errorMessage = ''; // Initialize the error message variable

    if (isset($_POST['submit'])) {
        $enteredOTP = $_POST['otp'];

        // Database connection code (replace with your credentials)
        $servername = "localhost";
        $dbUsername = "root";
        $dbPassword = "rootroot";
        $dbname = "quickfix";

        $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Validate the entered OTP
        if (validateOTP($enteredOTP, $complaintId, $conn)) {
            // OTP is valid, update the status to resolved
            updateStatusToResolved($complaintId, $conn);
        } else {
            // Invalid OTP, set the error message
            $errorMessage = "Invalid OTP. Please try again.";
        }
    }
} else {
    // Complaint ID not set, set an error message
    $errorMessage = "Invalid request. Please provide a valid complaint ID.";
}

function validateOTP($enteredOTP, $complaintId, $conn) {
    // Implement your logic to validate the entered OTP
    // For simplicity, you can compare it with the stored OTP in the database
    // You might want to hash the OTP and store it securely

    // Fetch the stored OTP from the database based on complaint_id
    $storedOTPSql = "SELECT otp FROM complaints WHERE id = ?";
    $stmtStoredOTP = $conn->prepare($storedOTPSql);
    $stmtStoredOTP->bind_param("i", $complaintId);
    $stmtStoredOTP->execute();
    $stmtStoredOTP->bind_result($storedOTP);

    if ($stmtStoredOTP->fetch()) {
        // Compare the entered OTP with the stored OTP
        if ($enteredOTP === $storedOTP) {
            return true;
        }
    }

    return false;
}

function updateStatusToResolved($complaintId, $conn) {
    // Update the status to resolved in the database
    $updateStatusSql = "UPDATE complaints SET status = 'resolved', completion_date = CURRENT_TIMESTAMP WHERE id = ?";
$stmtUpdateStatus = $conn->prepare($updateStatusSql);
$stmtUpdateStatus->bind_param("i", $complaintId);
    if ($stmtUpdateStatus->execute()) {
        // Status updated successfully, you can redirect to a success page
        header("Location: detailedcomplaint.php?complaint_id=$complaintId");
        exit();
    } else {
        // Handle the case where the status update fails
        $errorMessage = "Failed to update status to resolved.";
    }

    $stmtUpdateStatus->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #333;
        }

        label {
            display: block;
            margin: 15px 0 8px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        p {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    

    <form method="POST" action="">
        <label for="otp"><h2>OTP Verification</h2><p><?php
    // Display the error message if it's set
    if (!empty($errorMessage)) {
        echo "<p style='color: red;'>$errorMessage</p>";
    }
    ?></p></label>
        <input type="text" name="otp" placeholder="Enter otp" required>
        <button type="submit" name="submit">Verify</button>
    </form>
</body>
</html>
