<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="icon" href="fixlogo.png" type="image/png">
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
            transform: translateY(-15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-container {
        animation: fadeIn 1.5s ease;
/*
        position: absolute;
        top: 150px;
*/
    }
         
        
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h1>Login</h1><br>
            <form method="post" action="">
               
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
           
        </div>
         <div class="sitename">
            <h1 style=" position: absolute; left:20px; top: 10px; font-size: 20px;"><a href="index.php" style="text-decoration: none; color: #2980b9;">QuickFix</a></h1>
            </div>
        <video autoplay="true" class="homepage-hero__background homepage-hero__image-mobile" loop="true" muted="true" playsinline="true" preload="" src="pexels-adrien-jacta-6630025%20(Original).mp4" style="position: absolute;  z-index: -1000; width: 100%; left: 0; top: 0; opacity: 90%;">
     </video>
    </div>
</body>
</html>


<?php
// Start a PHP session
session_start();

// Check if the form is submitted
if (isset($_POST["login"])) { 
    // Get form data
    
    $userInputUsername = $_POST["username"];
    $userInputPassword = $_POST["password"];

    // Validate and sanitize data (you can use your existing validation functions)
    $username = validateInput($userInputUsername);
    $password = validateInput($userInputPassword);

    // Database connection code 
    $servername = "localhost";
    $dbUsername = "root"; 
    $dbPassword = "rootroot";
    $dbname = "quickfix";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
// Check credentials based on the account type
$sql = "SELECT username, password, role FROM login WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($dbUsername, $dbPassword, $userRole);
    $stmt->fetch();

    // Verify the submitted password against the hashed password from the database
    if (password_verify($password, $dbPassword)) {
        // Successful login

        // Store the user's username and role in session variables
        $_SESSION['user_email'] = $username;

        // Redirect based on the user's role
        if ($userRole === "user") {
            header("Location: userhome.php"); // Redirect to userhome.php
        } elseif ($userRole === "service_provider") {
            // Use a new statement object for service provider authentication
            $sqlServiceProvider = "SELECT status FROM service_providers WHERE email = ?";
            $stmtServiceProvider = $conn->prepare($sqlServiceProvider);
            $stmtServiceProvider->bind_param("s", $username);
            $stmtServiceProvider->execute();
            $stmtServiceProvider->store_result();

            if ($stmtServiceProvider->num_rows === 1) {
                $stmtServiceProvider->bind_result($providerStatus);
                $stmtServiceProvider->fetch();

                // Check if the service provider is approved by the admin
                if ($providerStatus === "approved") {
                    header("Location: servicehome.php"); // Redirect to servicehome.php
                    exit(); // Terminate script execution
                } 
            } elseif ($providerStatus === "pending") {
                // Service provider not approved by admin
                echo '<script>alert("Service Provider not approved by admin.")</script>';
                
            } elseif ($providerStatus === "blocked") {
                // Service provider not approved by admin
                echo '<script>alert("Your account is blocked")</script>';
                
            }
            else {
                    // Invalid password
                    echo "Invalid username or password.";
                }

            $stmtServiceProvider->close();
        } elseif ($userRole === "admin") {
            // Successful login for an admin
            header("Location: adminhome.php"); // Redirect to adminhome.php
            exit(); // Terminate script execution
        } else {
            // Invalid account type or credentials
            echo '<script>alert("Invalid account type or credentials.")</script>';
            
        }
    } else {
        // Invalid password
        echo '<script>alert("Invalid password.")</script>';
        
    }
} else {
    // Username not found
    echo '<script>alert("Invalid username.")</script>';
    
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