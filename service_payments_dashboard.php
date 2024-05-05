<?php
session_start();

// Check if the service provider is logged in or not. Redirect if not logged in.
if (!isset($_SESSION['service_provider_id'])) {
    header("Location: login.php"); // Redirect to your login page
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

// Get the current logged service provider's ID
$serviceProviderId = $_SESSION['service_provider_id'];

// Query to fetch total received amount
$totalAmountSql = "SELECT SUM(service_provider_share) AS total_received, count(*) As transaction_count FROM payments WHERE service_provider_id = ?";
$stmtTotalAmount = $conn->prepare($totalAmountSql);
$stmtTotalAmount->bind_param("i", $serviceProviderId);
$stmtTotalAmount->execute();
$resultTotalAmount = $stmtTotalAmount->get_result();
$totalAmountRow = $resultTotalAmount->fetch_assoc();
$totalReceivedAmount = $totalAmountRow['total_received'];
$transaction_count = $totalAmountRow['transaction_count'];

// Query to fetch all transactions
$transactionsSql = "SELECT * FROM payments WHERE service_provider_id = ?";
$stmtTransactions = $conn->prepare($transactionsSql);
$stmtTransactions->bind_param("i", $serviceProviderId);
$stmtTransactions->execute();
$resultTransactions = $stmtTransactions->get_result();

// Search functionality
$searchResult = false;
$searchTerm = '';

if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];

    // Query to search transactions
    $searchSql = "SELECT * FROM payments WHERE service_provider_id = ? AND (payment_id = ? OR DATE(payment_date) = ?)";
    $stmtSearch = $conn->prepare($searchSql);
    $stmtSearch->bind_param("iss", $serviceProviderId, $searchTerm, $searchTerm);
    $stmtSearch->execute();
    $resultSearch = $stmtSearch->get_result();

    // Check if any matching transactions found
    if ($resultSearch->num_rows > 0) {
        $searchResult = true;
    }
}


// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <title>Service Payments Dashboard</title>
    <!-- Add your CSS styles here -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            margin: 20px;
            padding: 20px;
            margin-left: 10%;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .total-amount-container {
            text-align: center;
            margin-bottom: 20px;
            background: linear-gradient(to right, rgba(167, 207, 223, 1) 0%, rgba(35, 83, 138, 1) 100%);
            box-shadow: 0 4px 8px 0 rgba(0, 0, 1, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            color: black;
            width: 300px;
            height: 150px;
            border-radius: 20px;
/*            transition: opacity 0.5s ease-in-out;*/
            animation: fadeIn 1.9s ease;
            
        }
        

        .transactions-list {
            list-style: none;
            padding: 0;
        }

        .transaction-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        .top-container {
            width: 100%;
            background: linear-gradient(90deg, rgba(49,148,148,1) 1%, rgba(171,228,238,1) 100%);
            height: 300px;
            border-bottom-left-radius: 600px;
            border-bottom-right-radius: 600px;
            animation: fadeIn 1.5s ease;
            
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .transaction-header {
            background-color: #334d36;
            color: white;
        }

        .transaction-row {
            background-color: #f9f9f9;
        }

        .transaction-item {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .transaction-item:hover {
            background-color: #e0f7fa;
        }

        .no-results {
            text-align: center;
            color: red;
            margin-top: 10px;
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
        <a class="navbar-brand" href="index.php" style="color: #2980b9; font-size: 20px;"><img src="fixlogo.png" width="25px" height="25px" style="margin-right: 1px;">uickFix</a>
        <!-- Add a responsive button for small screens -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto" style="margin-right: 25px;">
                <li class="nav-item active">
                    <a class="nav-link" href="servicehome.php">Home <span class="sr-only">(current)</span></a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="service_payments_dashboard.php">Payments</a>
                </li>
                
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="top-container">
            <center><h1 style="color: white; padding-top: 40px;">Service Payments Dashboard</h1></center>
        </div>

        <div class="total-out" style="display: flex; margin-top: 10px; position: relative; top: -100px;">
            <!-- Display Total Received Amount -->
            <div class="total-amount-container" style="margin-right: 10px; margin-left: 35%;">
                <h4 style="position:relative; top: 18px; padding-left: 5px; padding-right: 5px;">Total Received Amount</h4>
                <p style="color: white; position:relative; left: 70px; top: 60px;"><?php echo 'Rs ' . number_format($totalReceivedAmount, 2); ?></p>
            </div>
        </div>

        <!-- Display Table of Transactions -->
        <h2>Transactions</h2>

        <!-- Add search box -->
        <form method="get" action="">
            <label for="search">Search Transaction:</label>
            <input type="text" name="search" id="search" value="<?php echo $searchTerm; ?>">
            <input type="submit" value="Search">
        </form>

        <!-- Display Table of Transactions with Search Highlighting -->
        <table class="transactions-table">
            <tr class="transaction-header">
                <th>Payment ID</th>
                <th>Total Amount</th>
                <th>Admin Share</th>
                <th>Service Provider Share</th>
                <th>Payment Date</th>
            </tr>

            <?php
            if ($resultTransactions->num_rows > 0) {
    while ($transaction = $resultTransactions->fetch_assoc()) :
        // Check if the current transaction matches the search term
        $matchFound = false;

        if ($searchResult) {
            $paymentIdMatch = ($transaction['payment_id'] == $searchTerm);
            $paymentDateMatch = (date('Y-m-d', strtotime($transaction['payment_date'])) == $searchTerm);

            if ($paymentIdMatch || $paymentDateMatch) {
                $matchFound = true;
            }
        }

        // Display only the matching row
        if ($searchResult && $matchFound) :
            ?>
            <tr class="transaction-row">
                <td class="transaction-item"><?php echo $transaction['payment_id']; ?></td>
                <td class="transaction-item"><?php echo 'Rs ' . number_format($transaction['total_amount'], 2); ?></td>
                <td class="transaction-item"><?php echo 'Rs ' . number_format($transaction['admin_share'], 2); ?></td>
                <td class="transaction-item"><?php echo 'Rs ' . number_format($transaction['service_provider_share'], 2); ?></td>
                <td class="transaction-item"><?php echo $transaction['payment_date']; ?></td>
            </tr>
        <?php
        elseif (!$searchResult) :
            // Display all transactions if no search is performed
            ?>
            <tr class="transaction-row">
                <td class="transaction-item"><?php echo $transaction['payment_id']; ?></td>
                <td class="transaction-item"><?php echo 'Rs ' . number_format($transaction['total_amount'], 2); ?></td>
                <td class="transaction-item"><?php echo 'Rs ' . number_format($transaction['admin_share'], 2); ?></td>
                <td class="transaction-item"><?php echo 'Rs ' . number_format($transaction['service_provider_share'], 2); ?></td>
                <td class="transaction-item"><?php echo $transaction['payment_date']; ?></td>
            </tr>
        <?php
        endif;
    endwhile;
} else {
    echo '<tr><td colspan="5" class="no-results">No transactions found</td></tr>';
}
            ?>
        </table>

        <?php
        // Display button to view all transactions
        if ($searchResult) {
            ?>
            <form method="get" action="">
                <input type="submit" value="View All Transactions">
            </form>
            <?php
        }
        ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
     <script>
        // Adding class to trigger the transition after a delay
        document.addEventListener("DOMContentLoaded", function() {
            const totalAmountContainer = document.querySelector('.total-amount-container');
            setTimeout(() => {
                totalAmountContainer.classList.add('show');
            }, 500); // Delay in milliseconds before the transition starts (e.g., 500ms)
        });
    </script>
</body>
</html>
