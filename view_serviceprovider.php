<!DOCTYPE html>
<html>
<head>
    <title>Service Providers</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
       /* Navbar Styles */
.navbar {
    background-color: #d1e3ec;
    transition: background-color 0.3s ease; /* Add a smooth background color transition */
}

.navbar-brand,
.navbar-nav .nav-link {
    color: #fff;
    transition: color 0.3s ease; /* Add a smooth text color transition */
}

.navbar-brand:hover,
.navbar-nav .nav-link:hover {
    color: #ff5722;
}

/* Dropdown Styles */
.dropdown-menu {
    background-color: aliceblue;
    transition: background-color 0.3s ease; /* Add a smooth background color transition */
}

.dropdown-item {
    color: black;
    transition: color 0.3s ease; /* Add a smooth text color transition */
}

.dropdown-item:hover {
    color: blueviolet;
    /*background-color: blueviolet;*/
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
                    <a class="dropdown-item" href="view_users.php">View Users</a>
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
    <!-- Database connection code (reuse your existing code) -->
    <?php
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

    // Default sorting option
//    $sortOption = 'preferred_location_id';
    $sortOption = '';

if (isset($_GET['sort'])) {
    $sortOption = $_GET['sort'];
}


    // Default SQL query
    $sql = "SELECT sp.id, sp.name, sp.email, sp.phone, l.city AS preferred_location, sp.profile_photo 
            FROM service_providers sp
            INNER JOIN locations l ON sp.preferred_location_id = l.id";

    // Check if a sorting option is selected
    if (isset($_GET['sort'])) {
        $sortOption = $_GET['sort'];
        $sql .= " ORDER BY $sortOption";
    }

    // Check if a city filter is selected
    if (isset($_GET['city']) && $_GET['city'] !== '') {
        $selectedCity = $_GET['city'];
        $sql .= " WHERE l.city = '$selectedCity'";
    }

    // Execute the query and fetch results
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        ?>
        <div class="container mt-5">
            <center><h2 style="margin-bottom: 40px;">Service Providers</h2></center>
            

            <!-- Sorting options form -->
            <form method="get">
                <label for="sort">Sort by:</label>
                <select id="sort" name="sort" onchange="toggleCityFilter(this);">
    <option value="">--- Select ---</option>
    <option value="preferred_location_id" <?php if ($sortOption === 'preferred_location_id') echo 'selected'; ?>>Preferred Location</option>
    <!-- Add more sorting options if needed -->
</select>
            </form>

            <!-- City filter dropdown (initially hidden) -->
            <form method="get" id="cityFilter" style="display: <?php echo ($sortOption === 'preferred_location_id') ? 'block' : 'none'; ?>;">
                <label for="city">Filter by City:</label>
                <select id="city" name="city" onchange="this.form.submit()">
                    <option value="">All Cities</option>
                    <?php
                    // Query to fetch all unique cities from locations table
                    $cityQuery = "SELECT DISTINCT city FROM locations";
                    $cityResult = $conn->query($cityQuery);
                    while ($cityRow = $cityResult->fetch_assoc()) {
                        $cityName = $cityRow["city"];
                        $selected = isset($_GET['city']) && $_GET['city'] === $cityName ? 'selected' : '';
                        echo "<option value='$cityName' $selected>$cityName</option>";
                    }
                    ?>
                </select>
            </form>

            <table class="table" style="animation: fadeIn 1.5s ease; margin-top: 20px;">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Preferred Location</th>
                        <th>Action</th>
                        <!-- Add more columns as needed -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='" . $row["profile_photo"] . "' width='100' height='100'></td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["phone"] . "</td>";
                        echo "<td>" . $row["preferred_location"] . "</td>";
                        echo "<td>
                                <a href='view_profile.php?id=" . $row["id"] . "' class='btn btn-info'>View Profile</a>
                                <a href='delete_provider.php?id=" . $row["id"] . "' class='btn btn-danger'>Delete</a>
                              </td>";
                        // Add more columns as needed
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    } else {
        echo "No service providers found.";
    }

    // Close the database connection
    $conn->close();
    ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Function to toggle the visibility of the city filter dropdown
        function toggleCityFilter(select) {
            var cityFilter = document.getElementById('cityFilter');
            cityFilter.style.display = (select.value === 'preferred_location_id') ? 'block' : 'none';
        }
    </script>
</body>
</html>
