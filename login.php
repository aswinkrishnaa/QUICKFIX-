<?php
// Start a PHP session
session_start();

// Check if the form is submitted
if (isset($_POST["login"])) { // Use a named submit button
    // Get form data
    $accountType = $_POST["account_type"];
    $userInputUsername = $_POST["username"];
    $userInputPassword = $_POST["password"];

    // Validate and sanitize data (you can use your existing validation functions)
    $username = validateInput($userInputUsername);
    $password = validateInput($userInputPassword);

    // Database connection code (replace with your database credentials)
    $servername = "localhost";
    $dbUsername = "root"; // Change this to your database username
    $dbPassword = "rootroot"; // Change this to your database password
    $dbname = "quickfix";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check credentials based on the account type
    if ($accountType === "user") {
        // Use the users table for user authentication
        $sql = "SELECT email, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($dbUsername, $dbPassword);
            $stmt->fetch();

            // Verify the submitted password against the hashed password from the database
            if (password_verify($password, $dbPassword)) {
                // Successful login for a user

                // Store the user's email in a session variable
                $_SESSION['user_email'] = $username;

                header("Location: userhome.php"); // Redirect to userhome.php
                exit(); // Terminate script execution
            } else {
                // Invalid password
                echo "Invalid username or password.";
            }
        } else {
            // Username not found
            echo "Invalid username or password.";
        }

        $stmt->close();
    } elseif ($accountType === "service_provider") {
        // Use the service_providers table for service provider authentication
        $sql = "SELECT email, password, status FROM service_providers WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($dbUsername, $dbPassword, $providerStatus);
            $stmt->fetch();

            // Check if the service provider is approved by the admin
            if ($providerStatus === "approved") {
                
                // Verify the submitted password against the hashed password from the database
                if (password_verify($password, $dbPassword)) {
                    // Successful login for an approved service provider

                    // Store the user's email in a session variable
                    $_SESSION['user_email'] = $username;
                    header("Location: servicehome.php"); // Redirect to servicehome.php
                    exit(); // Terminate script execution
                } else {
                    // Invalid password
                    echo "Invalid username or password.";
                }
            } else {
                // Service provider not approved by admin
                echo "Service Provider not approved by admin.";
            }
        } else {
            // Username not found
            echo "Invalid username or password.";
        }

        $stmt->close();
    } elseif ($accountType === "admin" && $username === "admin" && $password === "admin1234") {
        // Successful login for an admin
        header("Location: adminhome.php"); // Redirect to admindashboard.php
        exit(); // Terminate script execution
    } else {
        // Invalid account type or credentials
        echo "Invalid account type or credentials.";
    }

    $conn->close();
}

// Validate and sanitize functions
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    if (empty($data)) {
        return "This field is required.";
    }

    return $data;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: #f0f4f8;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .container {
        max-width: 400px;
        width: 100%;
        text-align: center;
    }

    .login-container {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .login-container h1 {
        margin-bottom: 20px;
        color: #333333;
    }

    .login-container form input {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: none;
        background-color: #f0f4f8;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .login-container form button {
        width: 100%;
        padding: 10px;
        background-color: #3498db;
        border: none;
        border-radius: 5px;
        color: #ffffff;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .login-container form button:hover {
        background-color: #2980b9;
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

    .login-container {
        animation: fadeIn 1s ease;
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h1>Login</h1><br>
            <form method="post" action="">
                <select name="account_type" style="margin-bottom: 18px;">
                    <option value="user">User</option>
                    <option value="service_provider">Service Provider</option>
                    <option value="admin">Admin</option>
                </select>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
