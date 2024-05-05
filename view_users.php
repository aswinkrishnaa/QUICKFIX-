<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
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
    <div class="container mt-5">
        <center><h2>Users</h2></center>

        <?php
        // Database connection code (similar to your existing code)
        $servername = "localhost";
        $username = "root";
        $password = "rootroot";
        $dbname = "quickfix"; 

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the "Delete" button is clicked for a user
        if (isset($_POST['delete_user'])) {
            $user_id_to_delete = $_POST['user_id_to_delete'];

            // SQL query to delete the selected user
            $delete_sql = "DELETE FROM users WHERE id = $user_id_to_delete";

            if ($conn->query($delete_sql) === TRUE) {
                echo "<div class='alert alert-success'>User deleted successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error deleting user: " . $conn->error . "</div>";
            }
        }

        // SQL query to fetch all users
        $sql = "SELECT id, name, email, address, phone, registration_date FROM users";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Registration Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["address"] . "</td>";
                        echo "<td>" . $row["phone"] . "</td>";
                        echo "<td>" . $row["registration_date"] . "</td>";
                        echo "<td>
                                <form method='POST'>
                                    <input type='hidden' name='user_id_to_delete' value='" . $row["id"] . "'>
                                    <button type='submit' name='delete_user' class='btn btn-danger btn-sm'>Delete</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php
        } else {
            echo "No users found.";
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
