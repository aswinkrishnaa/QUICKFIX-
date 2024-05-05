<!DOCTYPE html>
<html>
<head>
    <title>Admin Complaints</title>
    <style>
        body {
            background: #f0f0f0;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        th {
            background-color: #4caf50;
            color: white;
        }

        select {
            padding: 10px;
        }

        .filter-section {
            text-align: right;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Admin Complaints</h1>

    <form method="POST" action="">
        <label for="status">Sort by Status:</label>
        <select name="status" id="status">
            <option value="pending">Pending</option>
            <option value="accepted">Accepted</option>
            <option value="rejected">Rejected</option>
            <option value="inprogress">In Progress</option>
        </select>

        <label for="location">Filter by Location:</label>
        <select name="location" id="location">
            <?php
            // Database connection settings
            $servername = "your_server_name";
            $dbUsername = "your_username";
            $dbPassword = "your_password";
            $dbname = "your_database_name";

            // Create a connection to the database
            $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Query to fetch location names from the 'locations' table
            $locationQuery = "SELECT id, city FROM locations";
            $locationResult = $conn->query($locationQuery);

            if ($locationResult->num_rows > 0) {
                while ($row = $locationResult->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['city'] . "</option>";
                }
            } else {
                echo "<option value='0'>No locations found</option>";
            }

            // Close the database connection
            $conn->close();
            ?>
        </select>

        <input type="submit" name="submit" value="Submit">
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $status = $_POST['status'];
        $location = $_POST['location'];

        // Database connection settings (use your own credentials)
        $servername = "your_server_name";
        $dbUsername = "your_username";
        $dbPassword = "your_password";
        $dbname = "your_database_name";

        // Create a connection to the database
        $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM complaints WHERE status = ? AND location_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $location);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>ID</th>
                        <th>Provider Category</th>
                        <th>User ID</th>
                        <th>Issue Type</th>
                        <th>Description</th>
                        <th>Location ID</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Submission Date</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['provider_category'] . "</td>";
                echo "<td>" . $row['user_id'] . "</td>";
                echo "<td>" . $row['issue_type'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['location_id'] . "</td>";
                echo "<td>" . $row['severity'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>" . $row['submission_date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No complaints found.";
        }

        // Close the database connection
        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
