<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['user_email'];

$servername = "localhost";
$dbUsername = "root"; // Change this to your database username
$dbPassword = "rootroot"; // Change this to your database password
$dbname = "quickfix";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM service_providers WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    // Display profile details fetched from the database
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile</title>
        <!-- Add Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            /* Custom styles */
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
            }

            .navbar {
                background-color: azure;
            }

            .navbar-brand {
                color: #2980b9;
                font-size: 20px;
            }

            .navbar-brand img {
                margin-right: 1px;
            }

            .navbar-nav .nav-link {
                color: #000;
                font-weight: 500;
            }

            .container {
                margin-top: 20px;
                padding: 20px;
                background-color: #fff;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            h1 {
                margin-bottom: 20px;
            }

            /* Add your other custom styles here */
        </style>
    </head>
    <body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="index.php">
            <img src="fixlogo.png" width="25px" height="25px">
            QuickFix
        </a>
        <!-- Add a responsive button for small screens -->
        <!-- Add your navbar links here -->
        <!-- ... -->
    </nav>

    <div class="container">
        <h1>Profile</h1>
        <p><strong>Name:</strong> <?php echo $row['name']; ?></p>
        <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
        <p><strong>ID Proof:</strong> <?php echo $row['id_proof']; ?></p>
        <p><strong>Expertise Certificate:</strong> <?php echo $row['expertise_certificate']; ?></p>
        <!-- Display other profile details as needed -->
    </div>

    <!-- Include Bootstrap JS and jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
} else {
    echo "No user found!";
}
$conn->close();
?>
