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

// Retrieve the complaint ID from the URL
if (isset($_GET['complaint_id'])) {
    $complaintId = $_GET['complaint_id'];

    // Query to fetch complaint details
//    $sql = "SELECT * FROM complaints WHERE id = ?";
    $sql = "SELECT c.id, c.user_id, c.issue_type, c.description, c.severity, c.submission_date, c.status, c.photo_of_issue, c.completion_date, c.invoice_id, u.name AS user_name, fr.rating, fr.feedback, fr.reply_message
FROM complaints c
INNER JOIN users u ON c.user_id = u.id
LEFT JOIN feedback_ratings fr ON c.id = fr.complaint_id
WHERE c.id = ?;
";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaintId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $complaint = $result->fetch_assoc();
        // Close the database connection
        $stmt->close();
        $conn->close();
    } else {
        // Handle the case where the complaint ID is not found
        echo "Complaint not found.";
        $stmt->close();
        $conn->close();
        exit();
    }
} else {
    // Handle the case where the complaint ID is not provided in the URL
    echo "Complaint ID not provided.";
    exit();
}
function displayStars($rating) {
    $stars = "";
    for ($i = 1; $i <= 5; $i++) {
        $class = ($i <= $rating) ? "fa fa-star" : "fa fa-star-o"; // Use Font Awesome classes
        $stars .= '<i class="' . $class . '"></i>';
    }
    return $stars;
}

$complaintData = [
    'serviceProviderName' => $complaint['serviceProviderName'],
    'submission_date' => $complaint['submission_date'],
    'completion_date' => $complaint['completion_date'],
    'issue_type' => $complaint['issue_type'],
    // Add more data here as needed
];




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed Complaint</title>
    <link rel="icon" href="fixlogo.png" type="image/png">
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* Add custom styles here */
        body {
            background-color: #f0f4f8;
        }
        h1 {
            text-align: center;
            margin-top: 40px;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(to right, #EFF8FF,#C9CBFF);
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
        .dropdown-item:hover {
            color: blueviolet;
        }
        .dropdown-menu {
            opacity: 80%;
        }

        /* Additional Styles */
        .container {
            margin-top: 20px;
            margin-bottom: 40px;
            
        }
        .imageofissue {
            height: 200px;
            width: auto;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .imageofissue:hover {
            box-shadow: 0px 0px 10px rgba(0,0,236,0.1);
        }
        h1 {
            margin-bottom: 30px;
        }
        .inner {
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
            padding-left: 50px;
            padding-top: 50px;
            border-radius: 10px;
            background-color: rgba(175, 219, 245, 0.7);
        }
        body {
            background: linear-gradient(#F9F9F9,#CDF0EA);
            
        }
        .chat {
            
            top: 240px;
            right: 250px;
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

        .inner {
            animation: fadeIn 1s ease;
        }
        .btns {
            display: inline;
            top: 250px;
            left: 70%;
            position: absolute;
        }
        
        .pulsating-button {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 5px 0 rgba(75, 207, 250, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 20px 10px rgba(75, 207, 250, 0.7);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 5px 0 rgba(75, 207, 250, 0.7);
    }
}
 
        .acceptorreject {
            
            display: flex;
            position: absolute;
            top: 230px;
            right: 300px;
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
                    <a class="dropdown-item" href="#">Quickfix Insights</a>
                </div>
            </li>
             <li class="nav-item">
            <a class="nav-link" href="service_payments_dashboard.php">Payments</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Profile
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

<div class="container">
    <h1>Detailed Complaint</h1>
    <div class="inner">
        <p><strong>Complaint ID:</strong> <?php echo $complaint['id']; ?></p>
<!--        <p><strong>User ID:</strong> <?php echo $complaint['user_id']; ?></p>-->
        <p><strong>User Name:</strong> <?php echo $complaint['user_name']; ?></p>
        <p><strong>Issue Type:</strong> <?php echo $complaint['issue_type']; ?></p>
        <p><strong>Description:</strong> <?php echo $complaint['description']; ?></p>
        <p><strong>Severity:</strong> <?php echo $complaint['severity']; ?></p>
        <p><strong>Submission Date & time:</strong> <?php echo $complaint['submission_date']; ?></p>
        <p><strong>Status:</strong> <?php echo $complaint['status']; ?></p>
        <div class="chat">
                    <?php if ($complaint['status'] === 'accepted' || $complaint['status'] === 'inprogress') { ?>
                       <a href="chat_service.php?complaint_id=<?php echo $complaint['id']; ?>&service_provider_id=<?php echo $_SESSION['service_provider_id']; ?>&user_id=<?php echo  $complaint['user_id'];?>" class="btn btn-primary <?php echo ($complaint['status'] === 'resolved') ? 'disabled' : ''; ?>">Chat</a>

                    <?php } ?>
                </div>
        <?php if ($complaint['status'] === 'resolved'): ?>
    <p><strong>Completion Date:</strong> <?php echo $complaint['completion_date']; ?></p>
<?php endif; ?>
        <!-- Edit Status Dropdown -->
<?php if ($complaint['status'] === 'accepted' || $complaint['status'] === 'inprogress'): ?>
    <p><strong>Edit Status:</strong>
    <form action="modify_complaint_status.php" method="post">
        <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
        <select name="new_status" class="form-control" style="width: 150px;">
            <option value="accepted">Accept</option>
            <option value="inprogress">In Progress</option>
            <option value="resolved">Resolved</option>
        </select>
        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
    </form>
    </p>
<?php endif; ?>

<!-- Accept Button Form -->
<!--
<?php if ($complaint['status'] === 'pending'): ?>
    <form action="update_complaint_status.php" method="post">
        <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
        <button type="submit" name="accept" class="btn btn-success">Accept</button>
    </form>
<?php endif; ?>
-->

   
    <?php if ($complaint['status'] === 'resolved' && $complaint['invoice_id'] === NULL): ?>
    <form action="generate_invoice_form.php?complaint_id=<?php echo $complaint['id']; ?>" method="post">
        <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
        <button type="submit" name="generate_invoice" class="btn btn-success pulsating-button" style="margin-bottom:10px;">Generate Invoice</button>
    </form>
<?php endif; ?>


    <?php if ($complaint['invoice_id'] !== NULL && $complaint['status'] === 'resolved'): ?>
    <a href="download_invoice.php?invoice_id=<?php echo $complaint['invoice_id']; ?>" class="btn btn-success" style="margin-bottom: 10px;">Download Invoice</a>
    <?php
                $servername = "localhost";
$dbUsername = "root";
$dbPassword = "rootroot";
$dbname = "quickfix";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
    // Check if there is a record with the invoice ID in the payments table
    $paymentCheckSql = "SELECT * FROM payments WHERE invoice_id = ?";
    $stmtPaymentCheck = $conn->prepare($paymentCheckSql);
    $stmtPaymentCheck->bind_param("i", $complaint['invoice_id']);
    $stmtPaymentCheck->execute();
    $paymentResult = $stmtPaymentCheck->get_result();
    
     if ($paymentResult->num_rows === 0) {
        // No payment record found, allow payment
    ?>
        
         <p><strong>Payment:</strong> Pending</p>
    <?php
    } else {
        // Payment record found, disable payment button
    ?>
                <p><strong>Payment:</strong> Payment already made for this invoice.</p>
<!--        <p>Payment already made for this invoice.</p>-->
    <?php
    }
    ?>
    
<?php endif; ?>
    
        <!-- Display the photo -->
        <?php if (!empty($complaint['photo_of_issue'])): ?>
            <p><strong>Photo:</strong></p>
            <a href="<?php echo $complaint['photo_of_issue']; ?>" target="_blank">
                <img src="<?php echo $complaint['photo_of_issue']; ?>" alt="Complaint Photo" class="imageofissue">
            </a>
        <?php endif; 
    
    
    ?>
    
    <div class="col-md-6">
    <div class="card" style="margin-right:25px;">
        
  <?php if ($complaint['status'] === 'resolved'): ?>
        <h3>Feedback</h3>
    <div class="card" style="margin-right: 25px;">
        <div class="card-body">
            <p><strong>Rating:</strong> <?php echo displayStars($complaint['rating']); ?></p>
            <p><strong>Feedback:</strong> <?php echo $complaint['feedback']; ?></p>
        </div>
    </div>

    <?php if (empty($complaint['reply_message'])): ?>
        <form method="post" action="save_reply.php">
            <div class="form-group">
                <label for="reply_message">Reply to Feedback:</label>
                <textarea name="reply_message" id="reply_message" rows="4" class="form-control" required></textarea>
            </div>
            <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
            <button type="submit" name="submit_reply" class="btn btn-primary">Submit Reply</button>
        </form>
    <?php endif; ?>
<?php endif; ?>

</div>

    </div>

       <!-- Accept Button Form -->
    <div class="acceptorreject">
<?php if ($complaint['status'] === 'pending'): ?>
    <form action="update_complaint_status.php" method="post" style="margin-right: 5px;">
        <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
        <button type="submit" name="accept" class="btn btn-success">Accept</button>
    </form>
<?php endif; ?>

<!-- Reject Button Form -->
<?php if ($complaint['status'] === 'pending'): ?>
    <button type="button" name="reject" class="btn btn-danger" onclick="showRejectionReason()">Reject</button></div>
    <div id="rejection_reason" style="display: none; margin-top: 15px; position: absolute; top: 260px; right: 200px;">
        <form action="update_complaint_status.php" method="post">
            <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
            <input type="text" name="rejection_reason" class="form-control" placeholder="Reason for Rejection" required style="width: 250px;">
            <button type="submit" name="submit_rejection" class="btn btn-danger mt-2">Submit Rejection</button>
        </form>
    </div>
<?php endif; ?>
        

    </div>


<!-- Include Bootstrap JS and jQuery (if not already included) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function showRejectionReason() {
        var rejectionReasonDiv = document.getElementById("rejection_reason");
        rejectionReasonDiv.style.display = "block";
        
        
        
    }
</script>
</body>
</html>
