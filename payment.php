<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect if not logged in
    header("Location: userhome.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Retrieve complaint_id and invoice_id from URL parameters
$complaintId = isset($_GET['complaint_id']) ? $_GET['complaint_id'] : null;
$invoiceId = isset($_GET['invoice_id']) ? $_GET['invoice_id'] : null;

// Check if both complaint_id and invoice_id are set
if ($complaintId === null || $invoiceId === null) {
    // Handle the case where either complaint_id or invoice_id is missing
    echo "Error: Complaint ID or Invoice ID is missing.";
    exit();
}

// Database connection code
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "rootroot";
$dbname = "quickfix";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve total_cost from the invoice table
$invoiceSql = "SELECT total_cost FROM invoice WHERE invoice_id = ?";
$stmtInvoice = $conn->prepare($invoiceSql);
$stmtInvoice->bind_param("i", $invoiceId);
$stmtInvoice->execute();
$stmtInvoice->bind_result($totalAmount);

// Fetch the result
if ($stmtInvoice->fetch()) {
    // Successfully fetched total_cost
    $stmtInvoice->close();
} else {
    // Handle the case where total_cost retrieval failed
    echo "Error retrieving total cost.";
    exit();
}

$serviceProviderId = getServiceProviderId($complaintId, $conn);

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["payNow"])) {
    echo "hello";
    echo $userId;
    echo $invoiceId;
    echo $totalAmount;

    // Retrieve service_provider_id from the complaints table
    echo $complaintId;
    echo $serviceProviderId;

    if ($serviceProviderId === false) {
        // Handle error
        echo "Error retrieving service provider information.";
        exit();
    }

    // Calculate shares
    $adminShare = $totalAmount * 0.15;
    $serviceProviderShare = $totalAmount - $adminShare;

    // Insert payment details into the payments table
    $insertSql = "INSERT INTO payments (user_id, service_provider_id, invoice_id, total_amount, admin_share, service_provider_share) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("iiiiii", $userId, $serviceProviderId, $invoiceId, $totalAmount, $adminShare, $serviceProviderShare);

    if ($stmt->execute()) {
        // Payment successful, update relevant tables as needed
        // (e.g., update user and service_provider balances)
        echo '<script>alert("Payment successfull");</script>';
        echo '<script>window.location.href = "userdetailedcomplaint.php?complaint_id=<?php echo $complaintId; ?>";</script>';

        // Redirect to success page
       
        
        exit();
    } else {
        // Handle payment failure
        echo "Error processing payment.";
    }

    $stmt->close();
}

$conn->close();

function getServiceProviderId($complaintId, $conn) {
    // Retrieve service_provider_id from the complaints table
    $serviceProviderIdSql = "SELECT service_provider_id FROM complaints WHERE id = ?";
    $stmtServiceProviderId = $conn->prepare($serviceProviderIdSql);
    $stmtServiceProviderId->bind_param("i", $complaintId);
    $stmtServiceProviderId->execute();
    $stmtServiceProviderId->bind_result($serviceProviderId);

    if ($stmtServiceProviderId->fetch()) {
        $stmtServiceProviderId->close();
        return $serviceProviderId;
    } else {
        $stmtServiceProviderId->close();
        return false;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        .bodydiv {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            padding: 20px;
            box-sizing: border-box;
            position: relative;
            animation: fadeIn 0.5s ease-in-out;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 3px;
            cursor: pointer;
            
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Card Animation */
       .card-container {
    display: flex;
    align-items: center; /* Center vertically */
    justify-content: flex-end; /* Align to the right */
    margin-bottom: 20px;
}

.card {
    width: 50px;
    height: 30px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    position: relative;
}

/* Remove the cardRotate animation */
.card:before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 2px;
    background-color: #333;
}

.visa:before {
    content: 'VISA';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #333;
    font-size: 10px;
}

.mastercard:before {
    
    content: 'MasterCard';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #333;
    font-size: 10px;
    
}
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

/*
        @keyframes cardRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
*/
        
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

    </style>
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script>
        // Dummy Card Type Recognition
        function recognizeCardType(cardNumber) {
            const visaRegex = /^4[0-9]{12}(?:[0-9]{3})?$/;
            const mastercardRegex = /^5[1-5][0-9]{14}$/;

            if (visaRegex.test(cardNumber)) {
                return 'visa';
            } else if (mastercardRegex.test(cardNumber)) {
                return 'mastercard';
            } else {
                return '';
            }
        }

        function updateCardType(cardNumber) {
            const cardContainer = document.getElementById('card-container');
            const cardElement = document.getElementById('card');

            const cardType = recognizeCardType(cardNumber);

            if (cardType === 'visa') {
                cardElement.classList.add('visa');
                cardElement.classList.remove('mastercard');
            } else if (cardType === 'mastercard') {
                cardElement.classList.add('mastercard');
                cardElement.classList.remove('visa');
            } else {
                cardElement.classList.remove('visa', 'mastercard');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const cardNumberInput = document.getElementById('cardNumber');

            cardNumberInput.addEventListener('input', (event) => {
                updateCardType(event.target.value);
            });
        });
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#">QuickFix</a>
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
    
    <div class="bodydiv">
    <form method="POST" action="">
        <div class="card-container" id="card-container">
            <div class="card" id="card"></div>
        </div>

        <label for="complaintId">Complaint ID:</label>
        <input type="text" id="complaintId" name="complaint_id" value="<?php echo $complaintId; ?>" readonly>

        <label for="invoiceId">Invoice ID:</label>
        <input type="text" id="invoiceId" name="invoice_id" value="<?php echo $invoiceId; ?>" readonly>

        <label for="totalAmount">Total Amount:</label>
        <input type="text" id="totalAmount" name="total_amount" value="<?php echo $totalAmount; ?>" readonly>

        <!-- Dummy Card Details -->
        <label for="cardNumber">Card Number:</label>
        <input type="text" id="cardNumber" name="card_number" placeholder="Enter card number" required>

        <label for="expiryDate">Expiry Date:</label>
        <input type="text" id="expiryDate" name="expiry_date" placeholder="MM/YYYY" required>

        <label for="cvv">CVV:</label>
        <input type="text" id="cvv" name="cvv" placeholder="Enter CVV" required style="margin-bottom: 10px;">

        <button type="submit" name="payNow">Pay Now</button>
    </form>
     <!-- Include Bootstrap JS and jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </div>
</body>
</html>

