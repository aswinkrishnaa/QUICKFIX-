<?php
session_start();

// Check if the user is logged in as a service provider
if (!isset($_SESSION['service_provider_id'])) {
    header("Location: servicehome.php"); // Redirect if not logged in
    exit();
}

$currentServiceProviderId = $_SESSION['service_provider_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection here
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "rootroot";
    $dbname = "quickfix";

    // Create a connection to the database
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Retrieve email from service_providers table using the service_provider_id
    $getEmailQuery = "SELECT email FROM service_providers WHERE id = ?";
    $getEmailStmt = $conn->prepare($getEmailQuery);

    if (!$getEmailStmt) {
        die("Error in fetching email: " . $conn->error);
    }

    $getEmailStmt->bind_param("i", $currentServiceProviderId);
    $getEmailStmt->execute();
    $emailResult = $getEmailStmt->get_result();

    if ($emailResult->num_rows > 0) {
        $emailRow = $emailResult->fetch_assoc();
        $serviceProviderEmail = $emailRow['email'];

        // Retrieve the hashed password for the current service provider using their email
        $getPasswordQuery = "SELECT password FROM login WHERE username = ? AND role = 'service_provider'";
        $getPasswordStmt = $conn->prepare($getPasswordQuery);

        if (!$getPasswordStmt) {
            die("Error in the SQL query: " . $conn->error);
        }

        $getPasswordStmt->bind_param("s", $serviceProviderEmail);
        $getPasswordStmt->execute();
        $result = $getPasswordStmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];

            // Verify the entered current password with the stored hashed password
            if (password_verify($currentPassword, $hashedPassword)) {
                // Check if the new password and confirm password match
                if ($newPassword === $confirmPassword) {
                    // Hash the new password before storing it
                    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update the password for the logged-in service provider
                    $updateSql = "UPDATE login SET password = ? WHERE username = ? AND role = 'service_provider'";
                    $updateStmt = $conn->prepare($updateSql);

                    if (!$updateStmt) {
                        die("Error in the SQL query: " . $conn->error);
                    }

                    $updateStmt->bind_param("ss", $hashedNewPassword, $serviceProviderEmail);

                    if ($updateStmt->execute()) {
                        $successMessage = "Password updated successfully!";
                    } else {
                        $errorMessage = "Error updating password: " . $conn->error;
                    }

                    $updateStmt->close();
                } else {
                    $errorMessage = "New password and confirm password do not match.";
                }
            } else {
                $errorMessage = "Incorrect current password.";
            }
        } else {
            $errorMessage = "User not found or not a service provider.";
        }

        $getPasswordStmt->close();
    } else {
        // Handle case when email is not found for the service provider
        echo "Service provider email not found.";
    }

    $getEmailStmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <!-- Add your CSS links or styles here -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-top: 40px;
        }

        form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
        }

        input[type="password"] {
            width: calc(100% - 12px);
            padding: 6px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #ff5722;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #f44336;
        }

        .success-message {
            color: #4caf50;
            text-align: center;
            margin-top: 10px;
        }

        .error-message {
            color: #f44336;
            text-align: center;
            margin-top: 10px;
        }
        .navbar {
            margin-bottom: 20px;
            background: linear-gradient(to right, #EFF8FF,#C9CBFF);
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1); 
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="index.php" style=" color:  #2980b9; font-size: 20px;"><img src="fixlogo.png" width="25px" height="25px" style="margin-right: 1px;">uickFix</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto" style="margin-right: 25px;">
            <li class="nav-item active">
                <a class="nav-link" href="servicehome.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="complaintsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Complaints
                </a>
                <div class="dropdown-menu" aria-labelledby="complaintsDropdown">
                    <a class="dropdown-item" href="list_complaints.php">New Complaints</a>
                    <a class="dropdown-item" href="view_accepted_complaints.php">Accepted Complaints</a>
                    <a class="dropdown-item" href="view_resolved_complaints.php">Resolved Complaints History</a>
                    <a class="dropdown-item" href="insight.php">QuickFix Insights</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="service_notification.php">Notifications</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Feeds</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                    <?php echo "profile"; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="profileDropdown" style="left:-52px;">
                    <a class="dropdown-item" href="#" >Profile</a>
                    <a class="dropdown-item" href="changepasswordsp.php">Change Password</a>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
    <h2>Change Password</h2>
    
    <?php if (isset($successMessage)) : ?>
        <div class="success-message"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if (isset($errorMessage)) : ?>
        <div class="error-message"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <input type="submit" value="Change Password">
    </form>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>