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

// Query to fetch complaints reported by the logged-in user
$sql = "SELECT c.id, c.issue_type, c.description, c.severity, c.submission_date, c.status, 
       c.service_provider_id, sp.name AS service_provider_name
FROM complaints c
LEFT JOIN service_providers sp ON c.service_provider_id = sp.id
WHERE c.user_id = ? AND c.status = 'resolved'
ORDER BY c.submission_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
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
    <title>View Complaints</title>
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
            color: #fff; /* Navbar text color */
        }

        .navbar-brand:hover,
        .navbar-nav .nav-link:hover {
            color: #ff5722;
            /* Navbar text color on hover */
        }

        /* Dropdown Styles */
        .dropdown-menu {
            background-color: aliceblue;
            /* Dropdown background color */
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
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="index.php" style=" color:  #2980b9; font-size: 20px;"><img src="fixlogo.png" width="25px" height="25px" style="margin-right: 1px;">uickFix</a>
        <!-- Add a responsive button for small screens -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto" style="margin-right: 25px;">
                <li class="nav-item active">
                    <a class="nav-link" href="userhome.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <h1>View Complaints</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Issue Type</th>
                <th>Description</th>
                <th>Severity</th>
                <th>Submission Date</th>
                <th>Status</th>
                <th>Service Provider</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['issue_type']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['severity']; ?></td>
                    <td><?php echo $row['submission_date']; ?></td>
                    <td style="color: <?php
    switch ($row['status']) {
        case 'pending':
            echo 'yellow';
            break;
        case 'accepted':
        case 'inprogress':
            echo 'green'; // You can change this color as needed
            break;
        case 'resolved':
            echo 'blue'; // You can change this color as needed
            break;
        default:
            echo 'red'; // Default color for other statuses
    }
?>;">
    <?php echo $row['status']; ?>
</td>

                    <td><?php echo ($row['status'] === 'accepted' || $row['status'] === 'inprogress' || $row['status'] === 'resolved') ? $row['service_provider_name'] : '-'; ?></td>

                    <td>
                        <a href="userdetailedcomplaint.php?complaint_id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                        
    

                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Include Bootstrap JS and jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
