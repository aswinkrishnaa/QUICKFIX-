<?php

// require 'vendor/autoload.php'; // Adjust the path based on your project structure

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
 

// Database connection code (replace with your credentials)
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "rootroot";
$dbname = "quickfix";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['update_status'])) {
    $complaintId = $_POST['complaint_id'];
    $newStatus = $_POST['new_status'];

    // Fetch the user email associated with the complaint
    $userEmailQuery = "SELECT u.email FROM users u
                       JOIN complaints c ON u.id = c.user_id
                       WHERE c.id = ?";
    
    $stmtUserEmail = $conn->prepare($userEmailQuery);

    if (!$stmtUserEmail) {
        // Handle the case where the query preparation fails
        die("User email query preparation failed: " . $conn->error);
    }

    $stmtUserEmail->bind_param("i", $complaintId);
    $stmtUserEmail->execute();

    if ($stmtUserEmail->error) {
        // Handle the case where the query execution fails
        die("User email query execution failed: " . $stmtUserEmail->error);
    }

    // Store the result
    $stmtUserEmail->store_result();

    if ($stmtUserEmail->num_rows > 0) {
        // Bind the result
        $stmtUserEmail->bind_result($userEmail);

        // Fetch the result
        $stmtUserEmail->fetch();

        // Check if the new status is "resolved"
        if ($newStatus === 'resolved') {
            // Generate OTP
            $otp = generateOTP();

            // Update the complaint with the generated OTP
            $updateOTPSql = "UPDATE complaints SET otp = ? WHERE id = ?";
            $stmtOTP = $conn->prepare($updateOTPSql);
            $stmtOTP->bind_param("si", $otp, $complaintId);
            $stmtOTP->execute();
            $stmtOTP->close();

            // Send OTP to the user via email using PHPMailer
            sendOTPByEmailPHPMailer($otp, $complaintId, $userEmail); // Use the retrieved user email

            // Redirect to a page where the service provider can enter the OTP
            header("Location: enter_otp.php?complaint_id=$complaintId");
            exit();
        } else {
            // Update the status of the complaint in the database
            $updateSql = "UPDATE complaints SET status = ? WHERE id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("si", $newStatus, $complaintId);

            if ($stmt->execute()) {
                // Status updated successfully
                header("Location: detailedcomplaint.php?complaint_id=$complaintId");
                exit();
            } else {
                // Handle the case where the status update fails
                echo "Status update failed.";
            }

            $stmt->close();
        }
    } else {
        // Handle the case where there is no matching record
        echo "No matching record found for complaint ID: $complaintId";
    }

    $stmtUserEmail->close();
    $conn->close();
}

// Function to generate a 6-digit OTP
function generateOTP() {
    return rand(100000, 999999);
}

// Function to send OTP by email using PHPMailer
function sendOTPByEmailPHPMailer($otp, $complaintId, $userEmail) {
    $mail = new PHPMailer(true);

    

    try {
        // Configure your SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'team.quickfix.com@gmail.com';
        $mail->Password = 'dzul nirh birw tmax';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Set From and To addresses
        $mail->setFrom('team.quickfix.com@gmail.com', 'QuickFix');
        $mail->addAddress($userEmail);

        $mail->isHTML(true);
        // Set email subject and body
        $mail->Subject = "Verification Code";
        $mail->Body = "Your OTP for complaint #$complaintId is: $otp";

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>
