<?php
// Start the session (if it's not already started)
session_start();

// Check if the user is logged in as a service provider or not. Redirect if not logged in.
if (!isset($_SESSION['service_provider_id'])) {
    header("Location: servicehome.php"); // Adjust the login page URL
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

// Query to fetch resolved complaints based on the service provider's profession and preferred location
$sql = "SELECT c.id, c.user_id, c.issue_type, c.description, c.severity, c.submission_date, c.completion_date
        FROM complaints c
        WHERE c.status = 'resolved' 
          AND c.provider_category = (
            SELECT profession FROM service_providers WHERE id = ?
          )
          AND c.location_id = (
            SELECT preferred_location_id FROM service_providers WHERE id = ?
          )
        ORDER BY c.completion_date DESC"; // Display most recent resolved complaints first

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_SESSION['service_provider_id'], $_SESSION['service_provider_id']);
$stmt->execute();
$result = $stmt->get_result();

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolved Complaints</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Add custom styles here */
        body {
            background-color: #f0f4f8;
        }
        h1 {
            text-align: center;
            margin-top: 40px;
        }
        table {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 80%;
            margin: 20px auto;
        }
        table th {
            background-color: #007bff;
            color: #fff;
        }
        table th, table td {
            padding: 10px 15px;
            text-align: center;
        }
        table tbody tr:hover {
            background-color: #f2f2f2;
        }
        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }

        /* Navbar Styles */
        .navbar {
            background-color: azure; /* Navbar background color */
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1); 
        }

        .navbar-brand,
        .navbar-nav .nav-link {
            color: navy; /* Navbar text color */
        }

        .navbar-brand:hover,
        .navbar-nav .nav-link:hover {
            color: #ff5722; /* Navbar text color on hover */
        }

        /* Dropdown Styles */
        .dropdown-menu {
            background-color: aliceblue; /* Dropdown background color */
        }

        .dropdown-item {
            color: black; /* Dropdown text color */
        }

        .dropdown-item:hover {
            color: blueviolet;
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

        .table td {
            animation: fadeIn 1s ease;
        }
    </style>
</head>
<body>
    <!-- Your navbar code here -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: azure; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);">
    <a class="navbar-brand" href="index.php" style=" color:  #2980b9; font-size: 20px;"><img src="fixlogo.png" width="25px" height="25px" style="margin-right: 1px;">uickFix</a>
    <!-- Add a responsive button for small screens -->
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
                    <a class="dropdown-item" href="view_resolved_complaints.php">Resolved Complaints</a>
                    <a class="dropdown-item" href="insight.php">Quickfix Insights</a>
                </div>
            </li>
           <li class="nav-item">
            <a class="nav-link" href="service_payments_dashboard.php">Payments</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php 
                    
                    $servername = "localhost";
    $dbUsername = "root"; 
    $dbPassword = "rootroot"; 
    $dbname = "quickfix";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT name, id FROM service_providers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['service_provider_id']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($currentname, $currentid);
        $stmt->fetch();
        
    }
                    echo $currentname; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="#">Edit Profile</a>
                    <a class="dropdown-item" href="#">Change Password</a>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<h1>Resolved Complaints</h1>
<?php if ($result->num_rows === 0): ?>
<center>
    <h4 style="margin-top: 100px; color: maroon;">No resolved complaints available.</h4>
</center>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Issue Type</th>
                <th>Submission Date</th>
                <th>Completion Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <?php
                        $userid = $row['user_id'];
                        $servername = "localhost";
                        $dbUsername = "root";
                        $dbPassword = "rootroot";
                        $dbname = "quickfix";

                        $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        $sqll = "SELECT name FROM users WHERE id = ?";
                        $stmnt = $conn->prepare($sqll);
                        $stmnt->bind_param("i", $userid);
                        $stmnt->execute();
                        $resultt = $stmnt->get_result();
                        if ($roww = $resultt->fetch_assoc()) {
                            echo $roww['name'];
                        }
                        // Close the database connection
                        $stmnt->close();
                        $conn->close();
                        ?>
                    </td>
                    <td><?php echo $row['issue_type']; ?></td>
                    <td><?php echo $row['submission_date']; ?></td>
                    <td><?php echo $row['completion_date']; ?></td>
                    <td>
                        <a href="detailedcomplaint.php?complaint_id=<?php echo $row['id']; ?>" class="btn btn-primary">View</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Include Bootstrap JS and jQuery (if not already included) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
