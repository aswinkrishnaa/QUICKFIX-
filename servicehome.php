<?php
// Start the session (if it's not already started)
session_start();

// Check if the user is logged in (you should have a login check)
if(isset($_SESSION['user_email'])){
    $userEmail = $_SESSION['user_email']; // Store the user's email in a variable

    $servername = "localhost";
    $dbUsername = "root"; // Change this to your database username
    $dbPassword = "rootroot"; // Change this to your database password
    $dbname = "quickfix";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT name, id FROM service_providers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($currentname, $currentid);
        $stmt->fetch();
        
        // Store the service provider ID in a session variable
        $_SESSION['service_provider_id'] = $currentid; // Corrected variable name
    }
} else {
    // User is not logged in, you can redirect them to the login page or show a message
    header("Location: login.php"); // Redirect to the login page
    exit(); // Terminate script execution
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickFix</title>
    <link rel="icon" href="fixlogo.png" type="image/png">
    <!-- Add Bootstrap CSS and JavaScript links -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Navbar Styles */
        .navbar {
            background-color: azure; /* Navbar background color */

        }
        .navbar-brand {
            color: navy;
            
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
            background-color:aliceblue;
            /* Dropdown background color */
        }

        .dropdown-item {
            color: black; /* Dropdown text color */
        }

        .dropdown-item:hover {
            color: blueviolet;
        }

        /* Carousel Styles */
        .carousel-inner {
            overflow: hidden;
            height: 500px;
        }

        .carousel-caption {
            color: #fff; /* Carousel caption text color */
            top: 350px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
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

        .navbar,
        .carousel-inner,
        .carousel-caption {
            animation: fadeIn 1s ease;
        }
        
         .footer {
  position: relative;
  width: 100%;
  background: #3586ff;
  min-height: 100px;
  padding: 20px 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}

.social-icon,
.menu {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 10px 0;
  flex-wrap: wrap;
}

.social-icon__item,
.menu__item {
  list-style: none;
}

.social-icon__link {
  font-size: 2rem;
  color: #fff;
  margin: 0 10px;
  display: inline-block;
  transition: 0.5s;
}
.social-icon__link:hover {
  transform: translateY(-10px);
}

.menu__link {
  font-size: 1.2rem;
  color: #fff;
  margin: 0 10px;
  display: inline-block;
  transition: 0.5s;
  text-decoration: none;
  opacity: 0.75;
  font-weight: 300;
}

.menu__link:hover {
  opacity: 1;
}

.footer p {
  color: #fff;
  margin: 15px 0 10px 0;
  font-size: 1rem;
  font-weight: 300;
}

.wave {
  position: absolute;
  top: -100px;
  left: 0;
  width: 100%;
  height: 100px;
  background: url("https://i.ibb.co/wQZVxxk/wave.png");
  background-size: 1000px 100px;
}

.wave#wave1 {
  z-index: 1000;
  opacity: 1;
  bottom: 0;
  animation: animateWaves 4s linear infinite;
}

.wave#wave2 {
  z-index: 999;
  opacity: 0.5;
  bottom: 10px;
  animation: animate 4s linear infinite !important;
}

.wave#wave3 {
  z-index: 1000;
  opacity: 0.2;
  bottom: 15px;
  animation: animateWaves 3s linear infinite;
}

.wave#wave4 {
  z-index: 999;
  opacity: 0.7;
  bottom: 20px;
  animation: animate 3s linear infinite;
}

@keyframes animateWaves {
  0% {
    background-position-x: 1000px;
  }
  100% {
    background-positon-x: 0px;
  }
}

@keyframes animate {
  0% {
    background-position-x: -1000px;
  }
  100% {
    background-positon-x: 0px;
  }
}



Resources
        .container2 {
            display: flex; /* Use Flexbox to create a flexible layout */
            align-items: center; /* Vertically center the content */
    
    
    
        }

        /* Style for the image */
        .container2 img {
            max-width: 30%; /* Adjust the image width as needed */
/*            margin-right: 20px;  Add space to the right of the image */
        }

        /* Style for the paragraph */
        .container2 p {
            flex-grow: 1; /* Allow the paragraph to grow and take available space */
            width: 800px;
            text-align: justify;
            margin-left: 0;
            margin-right: 30px;
            margin-top: 40px;
        }  
          /* Define the styles for the box */
    .stats-box {
    background-color: #f9f9f9;
    border: 2px solid #ccc;
    border-radius: 5px;
    text-align: center;
    padding: 20px;
    margin: 20px;
    width: 200px;
        
    transition: background-color 0.3s, border 0.3s, color 0.3s;
    cursor: pointer;
        display: inline-block;
        
        
}

.stats-box:hover {
    background-color: #333;
    border: 2px solid #f9f9f9;
    color: #f9f9f9;
}

.stats-box span {
    display: block;
    font-size: 36px;
    font-weight: bold;
}

.stats-box p {
    font-size: 18px;
    margin-top: 10px;
    color: #666;
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
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="complaintsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Complaints
                </a>
                <div class="dropdown-menu" aria-labelledby="complaintsDropdown">
                    <a class="dropdown-item" href="list_complaints.php">New Complaints</a>
                    <a class="dropdown-item" href="view_accepted_complaints.php">accepted complaints</a>
                    <a class="dropdown-item" href="view_resolved_complaints.php">resolved complaints History</a>
                    <a class="dropdown-item" href="insight.php">Quickfix Insights</a>
                </div>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="service_payments_dashboard.php">Payments</a>
            </li>
            
<!--
            <li class="nav-item">
                <a class="nav-link" href="#">Feeds</a>
            </li>
-->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo $currentname; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="profileDropdown" style="left:-100px;">
                    
                    <a class="dropdown-item" href="changepasswordsp.php">Change Password</a>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
            
        </ul>
    </div>
</nav>

<!-- Carousel -->
<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img class="d-block w-100" src="images/16608460_2103.i511.017_repair_home_flat.jpg" alt="First slide">
            <div class="carousel-caption d-none d-md-block shadow">
                <h3>Welcome to QuickFix</h3><br> <h5><?php echo $currentname;?></h5>
            </div>
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="images/muscular-car-service-worker-repairing-vehicle.jpg" alt="Second slide">
            <div class="carousel-caption d-none d-md-block">
                <h3>Service Excellence with QuickFix</h3>
            </div>
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="images/woman-replaces-fine-saw-blade.jpg" alt="Third slide">
            <div class="carousel-caption d-none d-md-block"  style="top: 150px;">
                <h3>QuickFix: Where Problems Find Solutions</h3>
                <p align ="justify">We believe in simplifying your life by addressing the challenges that can arise in managing a household.</p>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>
    
     <!-- Stats-->
    <?php
// Database configuration
$servername = "localhost";
$username = "root";
$dbpassword = "rootroot";
$dbname = "quickfix";

// Create a database connection
$conn = new mysqli($servername, $username, $dbpassword, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get the count of service providers
$sqlServiceProviders = "SELECT COUNT(*) as serviceProviderCount FROM service_providers";
$resultServiceProviders = $conn->query($sqlServiceProviders);

// Query to get the count of users
$sqlUsers = "SELECT COUNT(*) as userCount FROM users";
$resultUsers = $conn->query($sqlUsers);

// Query to get the count of locations
$sqlLocations = "SELECT COUNT(*) as locationCount FROM locations";
$resultLocations = $conn->query($sqlLocations);

// Query to get the count of complaints
$sqlComplaints = "SELECT COUNT(*) as complaintCount FROM complaints";
$resultComplaints = $conn->query($sqlComplaints);

// Initialize variables to store counts
$serviceProviderCount = 0;
$userCount = 0;
$locationCount = 0;
$complaintCount = 0;

// Check if queries were successful and fetch the counts
if ($resultServiceProviders && $resultUsers && $resultLocations && $resultComplaints) {
    $rowServiceProviders = $resultServiceProviders->fetch_assoc();
    $rowUsers = $resultUsers->fetch_assoc();
    $rowLocations = $resultLocations->fetch_assoc();
    $rowComplaints = $resultComplaints->fetch_assoc();

    // Store the counts in variables
    $serviceProviderCount = $rowServiceProviders['serviceProviderCount'];
    $userCount = $rowUsers['userCount'];
    $locationCount = $rowLocations['locationCount'];
    $complaintCount = $rowComplaints['complaintCount'];
    
    
}

// Close the database connection
$conn->close();
  
?>
    <center>
   <div class="stat-content" style="margin-bottom: 100px;">
    <div class="stats-box" id="user-stats">
        <span id="user-count"><?php echo $userCount; ?></span>
        <p>Total Users</p>
    </div>
    <div class="stats-box" id="provider-stats">
        <span id="provider-count"><?php echo $serviceProviderCount; ?></span>
        <p>Certified Service Providers</p>
    </div>
    <div class="stats-box" id="location-stats">
        <p>services in</p>
        <span id="location-count"><?php echo $locationCount; ?></span>
        <p>Locations</p>
    </div>
    <div class="stats-box" id="complaint-stats">
        <span id="complaint-count"><?php echo $complaintCount; ?></span>
        <p>Complaints Resolved</p>
    </div>
</div>

        
        </center>



<div class="container2">
        <img src="./images/b931b397ac9d4a23d935d9d4944ee8bd.jpg" >
        <p style="float: right;">Resolution with is not just a promise; it's our commitment to delivering lasting solutions to your household challenges. We understand that problems can disrupt your daily life, and that's why our skilled professionals are dedicated to resolving issues swiftly and effectively. Whether it's fixing that persistent plumbing problem, repairing faulty wiring, or addressing any other concern, we strive for excellence in every job. Our resolution isn't a temporary fix; it's a guarantee of long-term satisfaction and peace of mind. Trust us to provide the resolution you deserve and make your home a harmonious and functional space once again.</p>
    </div>
    
    <!-- Footer -->
<footer class="footer" style="margin-top: 400px;">
    <div class="waves">
      <div class="wave" id="wave1"></div>
      <div class="wave" id="wave2"></div>
      <div class="wave" id="wave3"></div>
      <div class="wave" id="wave4"></div>
    </div>
    <ul class="social-icon">
      <li class="social-icon__item"><a class="social-icon__link" href="#">
          <ion-icon name="logo-facebook"></ion-icon>
        </a></li>
      <li class="social-icon__item"><a class="social-icon__link" href="#">
          <ion-icon name="logo-twitter"></ion-icon>
        </a></li>
      <li class="social-icon__item"><a class="social-icon__link" href="#">
          <ion-icon name="logo-linkedin"></ion-icon>
        </a></li>
      <li class="social-icon__item"><a class="social-icon__link" href="#">
          <ion-icon name="logo-instagram"></ion-icon>
        </a></li>
    </ul>
    <ul class="menu">
      <li class="menu__item"><a class="menu__link" href="index.php">Home</a></li>
      <li class="menu__item"><a class="menu__link" href="#">About</a></li>
      <li class="menu__item"><a class="menu__link" href="#">Services</a></li>
      <li class="menu__item"><a class="menu__link" href="#">Team</a></li>
      <li class="menu__item"><a class="menu__link" href="#">Contact</a></li>

    </ul>
    <p>&copy;2023 QuickFix | All Rights Reserved</p>
  </footer>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<!-- Footer -->

<!-- Include Bootstrap JS and jQuery (if not already included) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
