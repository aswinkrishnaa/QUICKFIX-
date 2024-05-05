<!DOCTYPE html>
<html>
<head>
    <title>Service Provider Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
     <style>
       /* Navbar Styles */
.navbar {
    background-color: #d1e3ec;
    transition: background-color 0.3s ease; 
}

.navbar-brand,
.navbar-nav .nav-link {
    color: #fff;
    transition: color 0.3s ease; 
}

.navbar-brand:hover,
.navbar-nav .nav-link:hover {
    color: #ff5722;
}

/* Dropdown Styles */
.dropdown-menu {
    background-color: aliceblue;
    transition: background-color 0.3s ease; 
}

.dropdown-item {
    color: black;
    transition: color 0.3s ease; 
}

.dropdown-item:hover {
    color: blueviolet;
   
}

        body{
/*            background-color: #d1e3ec;*/
            background-color: azure;
            
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

    
</style>
</head>
<body>
       <nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="adminhome.php" style="color: #2980b9;">QuickFix</a>
    <!-- Add a responsive button for small screens -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto" style="margin-right: 25px;">
            <li class="nav-item active">
                <a class="nav-link" href="adminhome.php">Home <span class="sr-only">(current)</span></a>
            </li>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="complaintsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Users
                </a>
                <div class="dropdown-menu" aria-labelledby="complaintsDropdown">
                    <a class="dropdown-item" href="#">View Users</a>
                    <a class="dropdown-item" href="#">Users Complaints</a>
                    
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="complaintsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Service Provider
                </a>
                <div class="dropdown-menu" aria-labelledby="complaintsDropdown">
                    <a class="dropdown-item" href="view_New_registration.php">New Registrations</a>
                    <a class="dropdown-item" href="view_serviceprovider.php">View Service Providers</a>
                    
                </div>
            </li>
           
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Admin
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
    <?php
    // Check if the ID parameter is set in the URL
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $provider_id = $_GET['id'];
        
        // Database connection parameters
        $servername = "localhost";
        $username = "root";
        $password = "rootroot";
        $dbname = "quickfix";
        
        // Create a new database connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check if the connection is successful
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Define the SQL query to fetch the service provider's information by ID
        $sql = "SELECT sp.id, sp.name, sp.email, sp.phone, l.city AS preferred_location, sp.profile_photo, sp.id_proof, sp.expertise_certificate, sp.profession, sp.status, sp.availability, sp.registration_date
                FROM service_providers sp
                INNER JOIN locations l ON sp.preferred_location_id = l.id
                WHERE sp.id = ?";
        
        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            die("Error in preparing the statement: " . $conn->error);
        }
        
        // Bind the ID parameter to the SQL statement
        $stmt->bind_param("i", $provider_id);
        
        // Execute the SQL statement
        if ($stmt->execute()) {
            // Bind the result variables
            $stmt->bind_result($sid, $name, $email, $phone, $preferred_location, $profile_photo, $id_proof, $expertise_certificate, $profession, $status, $availability, $joining_datetime);
            
            // Fetch the result
            $stmt->fetch();
            
            // Close the prepared statement
            $stmt->close();
            
            // Display the service provider's information
            ?>
            <div class="container mt-5">
                <center><h2>Service Provider</h2></center>
                <table class="table" style="animation: fadeIn 1.5s ease; margin-top: 40px;">
                    <tr>
                        <tr>
                        <th>Profile Photo</th>
                        <td><img src="<?php echo $profile_photo; ?>" width="100" height="100"></td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td><?php echo $name; ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo $email; ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?php echo $phone; ?></td>
                    </tr>
                    <tr>
                        <th>Preferred Location</th>
                        <td><?php echo $preferred_location; ?></td>
                    </tr>
                    <tr>
                        <th>Profession</th>
                        <td><?php echo $profession; ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?php echo $status; ?></td>
                    </tr>
                    <tr>
                        <th>Availability</th>
                        <td><?php echo $availability; ?></td>
                    </tr>
                    <tr>
                        <th>Joining Date and Time</th>
                        <td><?php echo $joining_datetime; ?></td>
                    </tr>
                    
                    <tr>
                        <th>ID Proof</th>
                        <td><a href="<?php echo $id_proof; ?>" target="_blank">View ID Proof</a></td>
                    </tr>
                    <tr>
                        <th>Expertise Certificate</th>
                        <td><a href="<?php echo $expertise_certificate; ?>" target="_blank">View Expertise Certificate</a></td>
                    </tr>
                <tr>
                     <th>Action</th>
                <td>
                     <a href='delete_provider.php?id=" . $sid . "' class='btn btn-danger'>Delete</a>
                    </td>
                </tr>
                </table>
            </div>
            <?php
        } else {
            // Error executing the SQL statement
            echo "Error fetching service provider information: " . $stmt->error;
        }
        
        // Close the database connection
        $conn->close();
    } else {
        // Invalid ID parameter
        echo "Invalid ID parameter.";
    }
    ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
