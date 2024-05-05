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

// Check if the complaint ID is provided in the URL
if (isset($_GET['complaint_id'])) {
    $complaintId = $_GET['complaint_id'];

    // Query to fetch detailed complaint information
    $sql = "SELECT c.*, l.city AS location, u.name AS user_name, 
       u.email AS user_email, 
       sp.name AS service_provider_name, 
       sp.email AS service_provider_email,
       sp.profile_photo AS service_provider_photo,
       sp.id AS service_provider_id,
       fr.feedback, fr.rating, fr.reply_message
FROM complaints c
LEFT JOIN locations l ON c.location_id = l.id
LEFT JOIN users u ON c.user_id = u.id
LEFT JOIN service_providers sp ON c.service_provider_id = sp.id
LEFT JOIN feedback_ratings fr ON c.id = fr.complaint_id
WHERE c.id = ?;
";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $complaintId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Complaint not found
            header("Location: userhome.php"); // Redirect to user's home page
            exit();
        }

        $complaint = $result->fetch_assoc();
        $_SESSION['servicepro_id'] = $complaint['service_provider_id'];
        $stmt->close();
    } else {
        // Handle the error appropriately, e.g., display an error message
    }

    // Close the database connection
    $conn->close();
} else {
    // Complaint ID not provided in the URL
    header("Location: userhome.php"); // Redirect to the user's home page
    exit();
}

// Function to display star ratings
function displayStars($rating) {
    $stars = "";
    for ($i = 1; $i <= 5; $i++) {
        $class = ($i <= $rating) ? "fa-star" : "fa-star-o"; // Use Font Awesome classes
        $stars .= '<i class="fa ' . $class . '"></i>';
    }
    return $stars;
}

// Function to calculate the progress width based on the complaint status
function getProgressWidth($status) {
    switch ($status) {
        case 'pending':
            return '25%';
        case 'accepted':
            return '50%';
        case 'inprogress':
            return '75%';
        case 'resolved':
            return '100%';
        default:
            return '0%';
    }
}

// Function to calculate the progress value based on the complaint status
function getProgressValue($status) {
    switch ($status) {
        case 'pending':
            return '25';
        case 'accepted':
            return '50';
        case 'inprogress':
            return '75';
        case 'resolved':
            return '100';
        default:
            return '0';
    }
}
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
        .container {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 80%;
            margin: 20px auto;
            padding: 20px;
        }
        .btn {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #218838;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(to right, #EFF8FF, #C9CBFF); /* Navbar background color */
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

        .chat {
            position: absolute;
            top: 80px;
            left: 120px;
        }

        .rating {
            display: inline-block;
        }

        .rating input {
            display: none;
        }

        .rating label {
            float: right;
        }

        .rating label:before {
            content: "\2605";
            font-size: 2em;
            padding: 0.1em;
            color: #e0e0e0;
            cursor: pointer;
        }

        .rating input:checked ~ label:before {
            color: #ffc107;
        }
        
        /* Animation for the progress bar */
    .progress-bar {
        width: 0;
        background-color: #28a745; /* Change this to your preferred progress bar color */
        transition: width 1s ease-in-out;
    }

    .animate {
        animation: progress 2s ease-in-out;
    }

    @keyframes progress {
        from {
            width: 0;
        }
        to {
            width: 100%;
        }
    }
        
         .pulsating-button {
    animation: pulse 2s infinite;
             margin-top: 20px;
             margin-bottom: 30px;
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

    <div class="container">
        <h1 style="margin-bottom: 25px;">Detailed Complaint</h1>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Issue Type:</strong> <?php echo $complaint['issue_type']; ?></p>
                <p><strong>Description:</strong> <?php echo $complaint['description']; ?></p>
                <p><strong>Location:</strong> <?php echo $complaint['location']; ?></p>
                <p><strong>Severity:</strong> <?php echo $complaint['severity']; ?></p>
                 <p><strong>User:</strong> <?php echo $complaint['user_name']; ?></p>
                <p><strong>Status</strong> 
<!--                    <?php echo $complaint['status']; ?>-->
                </p>
                <div class="progress animate" style="margin-bottom: 10px;">
        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo getProgressWidth($complaint['status']); ?>;" aria-valuenow="<?php echo getProgressValue($complaint['status']); ?>" aria-valuemin="0" aria-valuemax="100">
            <?php echo $complaint['status']; ?>
        </div>
    </div>
           <?php if ($complaint['invoice_id'] !== NULL && $complaint['status'] === 'resolved'): ?>
    <a href="download_invoice.php?invoice_id=<?php echo $complaint['invoice_id']; ?>" class="btn btn-success pulsating-button">Download Invoice</a><br><br>
                
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
        
        <a href="payment.php?complaint_id=<?php echo $complaint['id']; ?>&invoice_id=<?php echo $complaint['invoice_id']; ?>" class="btn btn-warning">Pay Now</a>
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
            </div>
            <div class="col-md-6">
                <?php
                if ($complaint['status'] === 'accepted' || $complaint['status'] === 'inprogress' || $complaint['status'] === 'resolved') {
                    
                    echo '<p><strong>Service Provider Details:</strong></p>';
                    echo '<p><strong>Service Provider:</strong> ' . $complaint['service_provider_name'] . '</p>';
                    echo '<img src="' . $complaint['service_provider_photo'] . '" alt="Service Provider Photo" style="max-width: 100px;">';
                } else {
                    echo '<p><strong>Service Provider:</strong> -</p>';
                }
                ?>

                <div class="chat">
                    <?php if ($complaint['status'] === 'accepted' || $complaint['status'] === 'inprogress') { ?>
                       <a href="userchat.php?complaint_id=<?php echo $complaint['id']; ?>&service_provider_id=<?php echo $complaint['service_provider_id']; ?>" class="btn btn-primary <?php echo ($complaint['status'] === 'resolved') ? 'disabled' : ''; ?>">Chat</a>

                    <?php } ?>
                </div>

            </div>
        </div>
        
        <?php if ($complaint['status'] === 'rejected') { ?>
    <h3>Rejection Reason</h3>
    <p><strong>Yor complaint was rejected due to the reason - </strong> <?php echo $complaint['rejection_reason']; ?></p>
<?php } ?>
        
        <div class="row">
            <div class="col-md-6">
                <?php
                $existingRating = $complaint['rating'];
                $existingFeedback = $complaint['feedback'];
                $existingReplyMessage = $complaint['reply_message'];
                ?>

                <?php if ($existingRating > 0) { ?>
                    <h3>Feedback</h3>
                    <div class="card">
                        <div class="card-body">
                            <p><strong>Rating:</strong> <?php echo displayStars($existingRating); ?></p>
                            <p><strong>Feedback:</strong> <?php echo $existingFeedback; ?></p>
                            
                            <?php if (!empty($existingReplyMessage)): ?>
            <p><strong>Reply:</strong> <?php echo $existingReplyMessage; ?></p>
        <?php endif; ?>
                        </div>
                    </div>
                <?php } else if ($complaint['status'] === 'resolved') { ?>
                    <h2>Feedback</h2>
                    <form method="post" action="submit_feedback.php">
                        <div class="form-group">
                            <label for="rating">Rating:</label>
                            <div class="rating" id="rating">
                                <input type="radio" id="star5" name="rating" value="5" required /><label for="star5" title="5 stars"></label>
                                <input type="radio" id="star4" name="rating" value="4" required /><label for="star4" title="4 stars"></label>
                                <input type="radio" id="star3" name="rating" value="3" required /><label for="star3" title="3 stars"></label>
                                <input type="radio" id="star2" name="rating" value="2" required /><label for="star2" title="2 stars"></label>
                                <input type="radio" id="star1" name="rating" value="1" required /><label for="star1" title="1 star"></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="feedback">Feedback:</label>
                            <textarea name="feedback" id="feedback" rows="4" required></textarea>
                        </div>
                        <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
                        <input type="submit" value="Submit Feedback" class="btn btn-primary">
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>

    

    <!-- Include Bootstrap JS and jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
