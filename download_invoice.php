<?php
// Check if a complaint ID is provided in the URL
if (isset($_GET['invoice_id'])) {
    $invoiceId = $_GET['invoice_id'];

    // Database connection code (replace with your credentials)
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "rootroot";
    $dbname = "quickfix";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to fetch the invoice file path
    $sql = "SELECT invoice_path FROM invoice WHERE invoice_id = $invoiceId";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $invoicePath = $row['invoice_path'];

            // Check if the invoice file exists
            if (file_exists($invoicePath)) {
                // Set the appropriate content type for a PDF
                header('Content-Type: application/pdf');

                // Set the Content-Disposition header to prompt a download
                header('Content-Disposition: attachment; filename="' . basename($invoicePath) . '"');

                // Read and output the PDF file
                readfile($invoicePath);

                // Exit to prevent any additional output
                exit();
            } else {
                echo "Invoice not found.";
            }
        } else {
            echo "Complaint not found.";
        }
    } else {
        echo "Error in the SQL query: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    echo "Complaint ID not provided.";
}
?>
