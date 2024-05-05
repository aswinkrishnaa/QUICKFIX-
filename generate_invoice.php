<?php
require_once('tcpdf/tcpdf.php'); // Include the TCPDF library

// Ensure the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["generateInvoice"])) {
    // Retrieve and sanitize the data from the form
    $comments = $_POST["comments"];
    $itemDescriptions = $_POST["itemDescription"];
    $itemCosts = $_POST["itemCost"];
    $complaintId = $_GET['complaint_id'];
    

    // Calculate the total cost
    $totalCost = array_sum($itemCosts);
    
    $serviceCharge = 150;
$totalCost += $serviceCharge;

    // Define the directory where you want to save the PDF files
    $saveDirectory = 'C:\AppServ\www\Quickfix\uploads\invoice';

    // Check if the directory exists, and if not, create it
    if (!is_dir($saveDirectory)) {
        if (!mkdir($saveDirectory, 0755, true)) {
            die("Error creating the directory for PDF files.");
        }
    }

    // Start a new PDF document
    $pdf = new TCPDF();
    $pdf->SetAuthor('QuickFix');
    $pdf->SetTitle('Invoice');
    $pdf->AddPage();
    $pdf->SetFont('times', '', 12);

    // Add the company logo to the PDF
    $logoPath = 'C:\AppServ\www\Quickfix\images\qflogo.png'; // Adjust the path to your logo
    $pdf->Image($logoPath, 10, 12, 40); // Add the logo to the PDF

    // Company Header
    $companyInfo = "<br><br><br><br><br><br><br>
        <br>
        quickfix arcade, abc block, Ernakulam<br>
        Email: info@quickfix.org<br>
        Phone: 9800000009
    ";

    // Add company header to the PDF
    $pdf->SetXY(80, 10); // Adjust the coordinates for the company info
    $pdf->writeHTML($companyInfo, true, false, true, false, '');

    // Add invoice content to the PDF
    $pdf->Ln(10); // Move down a bit to make space for the content
    $pdf->SetTextColor(0, 0, 128); // Set text color to navy blue
    $pdf->SetFont('times', 'B', 14); // Set font to bold and larger size
    $pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');
    $pdf->SetFont('times', '', 12); // Reset font to normal

    $pdf->Ln(10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    // Create an HTML table for the invoice content with no borders
    $table = '<table cellpadding="4">
        <tr>
            <td align="left"><b>Service Provider:</b></td>
            <td align="left">' . $_POST['providerName'] . '</td>
        </tr>
        <tr>
            <td align="left"><b>Submission Date:</b></td>
            <td align="left">' . $_POST['complaintSubmissionDate'] . '</td>
        </tr>
        <tr>
            <td align="left"><b>Completion Date:</b></td>
            <td align="left">' . $_POST['complaintCompletionDate'] . '</td>
        </tr>
        <tr>
            <td align="left"><b>Issue Type:</b></td>
            <td align="left">' . $_POST['complaintIssueType'] . '</td>
        </tr>
        <tr>
            <td align="left"><b>Invoice created:</b></td>
            <td align="left">' . date('Y-m-d H:i:s') . '</td>
        </tr>';

    // Add line items to the table
    foreach ($itemDescriptions as $key => $itemDescription) {
        $table .= '<tr>
            <td align="left"><b>Item ' . ($key + 1) . ':</b></td>
            <td align="left">' . $itemDescription . ' - Rs ' . number_format($itemCosts[$key], 2) . '</td>
        </tr>';
    }
    
    $table .= '<tr>
            <td align="left"><b>Service Charge:</b></td>
            <td align="left">- Rs 150</td>
        </tr>';

    // Add the total cost
    $table .= '<tr>
            <td align="left"><b>Total:</b></td>
            <td align="left">Rs ' . number_format($totalCost, 2) . '</td>
        </tr>';

    $pdf->Ln(10);
    // Add comments to the table
    $table .= '</table>';
    $table .= '<br>'; // Add some space between the table and comments

    $table .= '<b>Comment:</b> ' . $comments;

    // Output the HTML table in the PDF
    $pdf->writeHTML($table, true, false, true, false, '');

    // Generate the PDF file path
    $pdfPath = $saveDirectory . DIRECTORY_SEPARATOR . date('Y-m-d-H-i-s') . '.pdf';
$pdf->Output($pdfPath, 'F');

    
    // Store the invoice path in the "invoice" table
            $servername = "localhost";
            $dbUsername = "root";
            $dbPassword = "rootroot";
            $dbname = "quickfix";

            $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $insertSql = "INSERT INTO invoice (invoice_path, date_of_generation, total_cost) VALUES (?, NOW(), ?)";

            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("si", $pdfPath, $totalCost);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                // Get the invoice ID
                $invoiceId = $stmt->insert_id;

                // Close the statement and the database connection
                $stmt->close();
                $sql = "UPDATE complaints SET invoice_id = ? WHERE id = ?";
            
            $stmtUpdate = $conn->prepare($sql);
            $stmtUpdate->bind_param("ii", $invoiceId, $complaintId);
            $stmtUpdate->execute();

            if ($stmtUpdate->affected_rows > 0) {
                // Close the statement
                $stmtUpdate->close();
            } else {
                echo "Error updating the 'complaints' table with the invoice ID. ";
            }
                
                $conn->close();

                // Output a success message
               echo '<script>alert("Invoice generated successfully");</script>';
    echo '<script>window.location.href = "detailedcomplaint.php?complaint_id=' . $complaintId . '";</script>';
                
            } else {
                echo "Error storing the PDF file path in the database.";
            }
    
    
    
} else {
    // Handle the case where the form wasn't submitted
    
     echo '<script>alert("Form not submitted.");</script>';
    echo '<script>window.location.href = "detailedcomplaint.php?complaint_id=' . $complaint['id'] . '";</script>';
}
?>
