<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];

    // Validate and sanitize data
    $name = validateInput($name);
    $email = validateEmail($email);
    $password = validateInput($password);
    $address = validateInput($address);
    $phone = validatePhone($phone);

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Connect to your database (replace with your database credentials)
    $servername = "localhost";
    $username = "root";
    $dbpassword = "rootroot";
    $dbname = "quickfix";

    $conn = new mysqli($servername, $username, $dbpassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the email is already registered
    $emailExists = false;
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $stmtCheckEmail = $conn->prepare($checkEmailQuery);
    $stmtCheckEmail->bind_param("s", $email);
    $stmtCheckEmail->execute();
    $stmtCheckEmail->store_result();
    if ($stmtCheckEmail->num_rows > 0) {
        $emailExists = true;
        $registrationMessage = "Email is already registered.";
    }
    $stmtCheckEmail->close();

    if (!$emailExists) {
        // Use prepared statement to insert user data into the database
        $stmt = $conn->prepare("INSERT INTO users (name, email, address, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $address, $phone);

        if ($stmt->execute()) {
//            $registrationMessage = "Registration successful! You can now log in.";
//        } else {
//            $registrationMessage = "Registration failed. Please try again.";
//        }
//    }
//
//    $conn->close();
            $loginStmt = $conn->prepare("INSERT INTO login (username, password, role) VALUES (?, ?, ?)");
    
    // Set the role as "service_provider"
    $role = "user";
    
    // Bind parameters
    $loginStmt->bind_param("sss", $email, $hashedPassword, $role);
    
    // Execute the login table insert
    if ($loginStmt->execute()) {
        $registrationMessage = "Registration successful!.";
    } else {
        $registrationMessage = "Registration failed. Please try again." . $loginStmt->error;
    }
    
    $loginStmt->close(); // Close the login statement
} else {
    $registrationMessage = "Registration failed. Error: " . $stmt->error;
}

$conn->close(); // Close the database connection
}
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

function validateEmail($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }
    return $email;
}

function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) !== 10) {
        return "Invalid phone number";
    }
    return $phone;
}
?>






<!DOCTYPE html>
<html>
<head>
    <title>QuickFix - User Registration</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include your custom CSS if needed -->
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">QuickFix</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login2.php">Login</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="registerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Register
                    </a>
                    <div class="dropdown-menu" aria-labelledby="registerDropdown">
                        <a class="dropdown-item" href="userregistration.php">User Register</a>
                        <a class="dropdown-item" href="providerregistration.php">Service Provider Register</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact Us</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- User Registration Form -->
    <div class="container mt-5">
        <h2>User Registration</h2>
    <?php if (isset($registrationMessage)): ?>
        <p><?php echo $registrationMessage; ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <!-- Your form fields here -->
         <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            
            <div class="form-group">
    <label for="phone">Phone:</label>
    <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{10}" placeholder="Enter your mobile phone number">
</div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    </div>
    
     <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>

