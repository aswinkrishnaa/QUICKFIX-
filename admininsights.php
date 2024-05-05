 <?php
   

   

    // Database connection settings
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "rootroot";
    $dbname = "quickfix";

    // Create a connection to the database
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

// Fetch data from the complaints table related to the logged-in service provider
    $complaintsData = [];
    $sqlComplaints = "SELECT 
        DATE_FORMAT(submission_date, '%Y-%m') AS month_year,
        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) AS resolved_count,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN status = 'inprogress' THEN 1 ELSE 0 END) AS inprogress_count,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count
        FROM complaints
        GROUP BY month_year";

    $stmtComplaints = $conn->prepare($sqlComplaints);
    if (!$stmtComplaints) {
        die("Error in the SQL query: " . $conn->error);
    }

    
    $stmtComplaints->execute();
    $resultComplaints = $stmtComplaints->get_result();

    if ($resultComplaints->num_rows > 0) {
        while ($row = $resultComplaints->fetch_assoc()) {
            $monthYear = date("F Y", strtotime($row["month_year"]));
            $complaintsData[] = [
                "month_year" => $monthYear,
                "resolved_count" => $row["resolved_count"],
                "pending_count" => $row["pending_count"],
                "inprogress_count" => $row["inprogress_count"],
                "rejected_count" => $row["rejected_count"]
            ];
//            echo "<pre>";
////print_r($complaintsData);
//echo "</pre>";
        }
    } else {
        echo "No data found for complaints overview.";
    }

    // Fetch data from the ratings_feedback table related to the logged-in service provider
    $ratingsFeedbackData = [];
    $sqlRatingsFeedback = "SELECT
        DATE_FORMAT(timestamp, '%Y-%m') AS month_year,
        AVG(rating) AS average_rating,
        SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS rating_5,
        SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS rating_4,
        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS rating_3,
        SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS rating_2,
        SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS rating_1,
        COUNT(*) AS feedback_count
        FROM feedback_ratings
        GROUP BY month_year";

    $stmtRatingsFeedback = $conn->prepare($sqlRatingsFeedback);
    
    $stmtRatingsFeedback->execute();
    $resultRatingsFeedback = $stmtRatingsFeedback->get_result();

    if ($resultRatingsFeedback->num_rows > 0) {
        while ($row = $resultRatingsFeedback->fetch_assoc()) {
            $monthYear = date("F Y", strtotime($row["month_year"]));
            $ratingsFeedbackData[] = [
                "month_year" => $monthYear,
                "average_rating" => round($row["average_rating"], 2),
                "rating_5" => $row["rating_5"],
                "rating_4" => $row["rating_4"],
                "rating_3" => $row["rating_3"],
                "rating_2" => $row["rating_2"],
                "rating_1" => $row["rating_1"],
                "feedback_count" => $row["feedback_count"]
            ];
        }
    }


// Fetch data for resolution time analysis
$resolutionChartData = [];
$resolutionTimeCounts = [];

foreach ($resolutionTimeCounts as $interval => $count) {
    $resolutionChartData[] = ['interval' => $interval, 'count' => $count];
}

// Encode $resolutionChartData to JSON
$resolutionDataJSON = json_encode($resolutionChartData);
$sqlResolutionTime = "SELECT TIMESTAMPDIFF(HOUR, submission_date, completion_date) AS resolution_hours 
FROM complaints WHERE status = 'resolved'";

$stmtResolutionTime = $conn->prepare($sqlResolutionTime);

$stmtResolutionTime->execute();
$resultResolutionTime = $stmtResolutionTime->get_result();




// Fetch data for resolution time analysis

$resolutionTimeData = []; // Initialize the array for storing resolution time intervals

while ($row = $resultResolutionTime->fetch_assoc()) {
    $resolution_hours = $row['resolution_hours'];
    // Categorize resolution time into intervals (modify as per your preferred intervals)
    if ($resolution_hours <= 24) {
        $interval = '0-24 hours';
    } elseif ($resolution_hours <= 72) {
        $interval = '1-3 days';
    } elseif ($resolution_hours <= 168) {
        $interval = '3-7 days';
    } else {
        $interval = '7+ days';
    }
    // Store the resolution time interval for each resolved complaint
    $resolutionTimeData[] = $interval;
}

// Count the occurrences of each resolution time interval
$resolutionTimeCounts = array_count_values($resolutionTimeData);

// Prepare data for the resolution time chart
$resolutionChartData = [];
foreach ($resolutionTimeCounts as $interval => $count) {
    $resolutionChartData[] = ['interval' => $interval, 'count' => $count];
}

// Encode $resolutionChartData to JSON
$resolutionDataJSON = json_encode($resolutionChartData);



$revenueData = [];
$sqlRevenue = "SELECT DATE(payment_date) AS date, SUM(total_amount) AS total_revenue 
               FROM payments 
               GROUP BY DATE(payment_date)";

$resultRevenue = $conn->query($sqlRevenue);

if ($resultRevenue->num_rows > 0) {
    while ($row = $resultRevenue->fetch_assoc()) {
        $revenueData[] = [
            "date" => $row["date"],
            "total_revenue" => $row["total_revenue"]
        ];
    }
}
// Close the database connection
$stmtComplaints->close();
$stmtRatingsFeedback->close();
$stmtResolutionTime->close();




$conn->close();

// Prepare data for JSON response
$data = [
    "complaints" => $complaintsData,
    "ratingsFeedback" => $ratingsFeedbackData,
    "resolutionData" => $resolutionTimeData,
];

$revenueChartData = [
    "revenueData" => $revenueData
];



    ?>
<!DOCTYPE html>
<html>
<head>
    <title>Service Provider Insights</title>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <style>
       
        
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
             
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

        #complaints-chart,
        #ratings-feedback-chart,
        #resolution-time-chart,
        #revenue-trends-chart {
            margin: 20px auto;
            max-width: 900px;
            background-color: aliceblue;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        #complaints-chart, 
        .navbar,
        h2 {
            animation: fadeIn 1s ease;
        }

        .highcharts-container {
            width: 100%;
            height: 300px; /* Adjust the chart height as needed */
            margin: 0 auto;
        }
        
         .navbar {
            background: linear-gradient(to right, #EFF8FF,#C9CBFF);
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1); 
            margin-bottom: 50px;
        }
        .navbar-brand {
            color: navy;
        }
        .navbar-brand,
        .navbar-nav .nav-link {
            color: #fff; /* Navbar text color 
        }
        .navbar-brand:hover,
        .navbar-nav .nav-link:hover {
            color: #ff5722; /* Navbar text color on hover */
        }
        .dropdown-menu {
            background-color: aliceblue; /* Dropdown background color */
            padding: 10px;
        }
        .dropdown-item {
            color: black; /* Dropdown text color */
        }
        .dropdown-item hover {
            color: blueviolet;
        }
        
        .chart-container {
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.chart-container.chart-visible {
  opacity: 1;
}
        
table {
        border-collapse: collapse;
        width: 80%;
    margin-bottom: 30px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f2f2f2;
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
            <li class="nav-item active">
                <a class="nav-link" href="admininsights.php">insights </a>
            </li>
           
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Admin
                </a>
                <div class="dropdown-menu" aria-labelledby="profileDropdown">
                    
                    <a class="dropdown-item" href="#">Change Password</a>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
            
        </ul>
    </div>
</nav>
    
    <h2 style=" padding-bottom: 25px; padding-left:20px; color: darkblue; text-align: left; margin-top: -20px;">QuickFix Insights</h2>

    <div class="chart-container scroll-animation" id="complaints-chart"></div>
    <div class="chart-container scroll-animation" id="ratings-feedback-chart"></div>
    <div class="chart-container scroll-animation" id="resolution-time-chart"></div>
    <div class="chart-container scroll-animation" id="revenue-trends-chart"></div>

   

    <script>
        
                // Function to handle intersection observer
        const observer = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('chart-visible');
              observer.unobserve(entry.target);
            }
          });
        }, { threshold: 0.2 });

        // Target the elements and observe them
        const chartContainers = document.querySelectorAll('.scroll-animation');
        chartContainers.forEach(container => {
          observer.observe(container);
        });
        
        // JavaScript section to work with the data fetched from PHP
        const data = <?php echo json_encode($data); ?>;
        const revenueData = <?php echo json_encode($revenueChartData); ?>;
         
    const resolutionData = <?php echo $resolutionDataJSON; ?>;

        // Create a chart for complaints data
      Highcharts.chart('complaints-chart', {
          
    title: {
        text: 'Complaints Overview'
    },
    xAxis: {
        categories: data.complaints.map(item => item.month_year)
    },
    yAxis: {
        title: {
            text: 'Count'
        }
    },
    series: [
        {
            name: 'Resolved',
            data: data.complaints.map(item => parseInt(item.resolved_count)),
            type: 'column'
        },
        {
            name: 'Pending',
            data: data.complaints.map(item => parseInt(item.pending_count)),
            type: 'column'
        },
        {
            name: 'In Progress',
            data: data.complaints.map(item => parseInt(item.inprogress_count)),
            type: 'column'
        },
        {
            name: 'Rejected',
            data: data.complaints.map(item => parseInt(item.rejected_count)),
            type: 'column'
        }
    ]
});



        // Create a chart for ratings and feedback data
        Highcharts.chart('ratings-feedback-chart', {
            
            title: {
                text: 'Ratings & Feedback Overview'
            },
            series: [
                {
                    name: 'Average Rating',
                    data: data.ratingsFeedback.map(item => item.average_rating),
                    type: 'line'
                },
                {
                    name: 'Feedback Count',
                    data: data.ratingsFeedback.map(item => item.feedback_count),
                    type: 'column'
                },
            ],
            xAxis: {
                categories: data.ratingsFeedback.map(item => item.month_year)
            }
        });
        
        
        // Create a new chart for resolution time analysis
 Highcharts.chart('resolution-time-chart', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Resolution Time Analysis'
    },
    xAxis: {
        categories: resolutionData.map(item => item.interval)
    },
    yAxis: {
        title: {
            text: 'Number of Complaints Resolved'
        }
    },
    series: [{
        name: 'Resolved',
        data: resolutionData.map(item => item.count),
        colorByPoint: true // This might help in displaying individual bars
    }]
});
        
        // Create a chart for revenue trends over time
    Highcharts.chart('revenue-trends-chart', {
        title: {
            text: 'Revenue Trends Over Time'
        },
        xAxis: {
            type: 'datetime',
            title: {
                text: 'Date'
            }
        },
        yAxis: {
            title: {
                text: 'Total Revenue'
            }
        },
        series: [{
            name: 'Total Revenue',
            data: revenueData.revenueData.map(item => [Date.parse(item.date), parseFloat(item.total_revenue)])
        }]
    });
    </script>
    
     <?php
   

   

    // Database connection settings
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "rootroot";
    $dbname = "quickfix";

    // Create a connection to the database
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Fetch data for service provider ranking based on resolved complaints
$serviceProviderRankingData = [];
$sqlServiceProviderRanking = "SELECT sp.id, sp.name, sp.email, sp.profile_photo, COUNT(*) AS resolved_complaints 
                              FROM complaints c
                              JOIN service_providers sp ON c.service_provider_id = sp.id
                              WHERE c.status = 'resolved' 
                              GROUP BY c.service_provider_id 
                              ORDER BY resolved_complaints DESC";

$resultServiceProviderRanking = $conn->query($sqlServiceProviderRanking);

if ($resultServiceProviderRanking->num_rows > 0) {
    while ($row = $resultServiceProviderRanking->fetch_assoc()) {
        $serviceProviderRankingData[] = [
            "id" => $row["id"],
            "name" => $row["name"],
            "email" => $row["email"],
            "profile_photo" => $row["profile_photo"],
            "resolved_complaints" => $row["resolved_complaints"]
        ];
    }
}

$conn->close();
// Display the service provider ranking data in a table
echo "<h3 align='center' style='margin-top:45px; margin-bottom:25px;'>Service Provider Ranking based on Resolved Complaints</h3>";
echo "<table border='1' align='center'>";
echo "<tr><th>Image</th><th>ID</th><th>Name</th><th>Email</th><th>Resolved Complaints</th></tr>";
foreach ($serviceProviderRankingData as $provider) {
    echo "<tr>";
    echo "<td><img src='" . $provider['profile_photo'] . "' width='50' height='50' style = 'display: block;
        margin: auto;
        max-width: 50px;
        max-height: 50px;
        border-radius: 50%;'></td>";
    echo "<td>" . $provider['id'] . "</td>";
    echo "<td>" . $provider['name'] . "</td>";
    echo "<td>" . $provider['email'] . "</td>";
    echo "<td>" . $provider['resolved_complaints'] . "</td>";
    echo "</tr>";
}
echo "</table>";
    ?>
    <!-- Include Bootstrap JS and jQuery (if not already included) -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
