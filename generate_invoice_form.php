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

    // SQL query to retrieve complaint details and service provider name
    $sql = "SELECT
                c.submission_date,
                c.completion_date,
                c.issue_type,
                c.status,
                sp.name AS service_provider_name
            FROM complaints c
            LEFT JOIN service_providers sp ON c.service_provider_id = sp.id
            WHERE c.id = ?";
    
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

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Form</title>
    <!-- Add any necessary CSS styles here -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        h1 {
            text-align: center;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .company-info p {
            margin: 5px 0;
        }
        .company-logo img {
            max-width: 100px;
            height: auto;
        }
        .form-section {
            margin-top: 20px;
        }
        .form-section h2 {
            background-color: #007BFF;
            color: #fff;
            padding: 10px;
            margin-top: 0;
            border-radius: 5px 5px 0 0;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        textarea {
            height: 100px;
        }
        .line-item {
            display: flex;
            justify-content: space-between;
        }
        .line-item input {
            width: 45%;
        }
        .line-item button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .line-item button:hover {
            background-color: #c82333;
        }
        button.add-item {
            background-color: #28a745;
        }
        button.add-item:hover {
            background-color: #218838;
        }
        button.generate-invoice {
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            margin-top: 20px;
            cursor: pointer;
        }
        button.generate-invoice:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    
    
    
    <div class="container">
        <form action="generate_invoice.php?complaint_id=<?php $complaintId = $_GET['complaint_id']; echo $complaintId; ?>" method="post">
        
        <div class="company-info">
            <div class="company-logo">
                <img src="./images/qflogo.png" alt="QuickFix Logo">
            </div>
            <p><strong>QuickFix</strong> </p>
            <p>quickfix arcade, abc block, Ernakulam</p>
            <p><strong>Email:</strong> info@quickfix.org</p>
            <p><strong>Phone:</strong> 9800000009</p>
            
        </div>
            <h1>Invoice Form</h1>
        <div class="form-section">
            <h2>Service Provider Information</h2>
            <label for="providerName">Service Provider Name:</label>
            <input type="text" id="providerName" name="providerName" value="<?php echo $complaint['service_provider_name']; ?>" readonly>
        </div>
        <div class="form-section">
            <h2>Complaint Details</h2>
            <label for="complaintSubmissionDate">Submission Date:</label>
            <input type="text" id="complaintSubmissionDate" name="complaintSubmissionDate" value="<?php echo $complaint['submission_date']; ?>" readonly>
            <label for="complaintCompletionDate">Completion Date:</label>
            <input type="text" id="complaintCompletionDate" name="complaintCompletionDate" value="<?php echo $complaint['completion_date']; ?>" readonly>
            <label for="complaintIssueType">Issue Type:</label>
            <input type="text" id="complaintIssueType" name="complaintIssueType" value="<?php echo $complaint['issue_type']; ?>" readonly>
            
        </div>
        <div class="form-section">
            <h2>Line Items</h2>
            <div id="lineItems">
                <!-- JavaScript will add dynamic line items here -->
            </div>
            <button class="add-item" type="button" onclick="addLineItem()">Add Another Item</button>
        </div>
        <div class="form-section">
            <h2>Comments</h2>
            <textarea id="comments" name="comments"></textarea>
        </div>
        <button class="generate-invoice" type="submit" name="generateInvoice">Generate Invoice</button>
        </form>
    </div>

    <!-- JavaScript for adding dynamic line items -->
    <script>
        function addLineItem() {
            const lineItems = document.getElementById("lineItems");
            const newItem = document.createElement("div");
            newItem.className = "line-item";
            newItem.innerHTML = `
                <input type="text" name="itemDescription[]" placeholder="Item Description" required>
                <input type="number" name="itemCost[]" placeholder="Item Cost" required>
                <button type="button" onclick="removeLineItem(this)">Remove</button>
            `;
            lineItems.appendChild(newItem);
        }

        function removeLineItem(button) {
            const lineItems = document.getElementById("lineItems");
            lineItems.removeChild(button.parentNode);
        }
    </script>
</body>
</html>



