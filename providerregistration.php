<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $preferredLocation = $_POST["preferred_location"];
    $phone = $_POST["phone"];
    $workfield=$_POST["work_field"];

    // Validate and sanitize data
    $name = validateInput($name);
    $email = validateEmail($email);
    $password = validateInput($password);
    $preferredLocation = validateInput($preferredLocation);
    $phone = validatePhone($phone);
//    $phone = validatePhone($workfield);

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Allowed file formats
    $allowedIdProofFormats = array("jpg", "jpeg", "png", "pdf");
    $allowedExpertiseCertificateFormats = array("jpg", "jpeg", "png", "pdf");
    $allowedProfilePhotoFormats = array("jpg", "jpeg", "png");

    // File uploads
    $idProof = uploadFile($_FILES["id_proof"], "id_proofs", $allowedIdProofFormats);
    $expertiseCertificate = uploadFile($_FILES["expertise_certificate"], "certificates", $allowedExpertiseCertificateFormats);
    $profilePhoto = uploadFile($_FILES["profile_photo"], "profile_photos", $allowedProfilePhotoFormats);

    // Construct the relative path to the profile photo
    $profilePhotoPath = "uploads/profile_photos/" . basename($profilePhoto);

    // Connect to your database 
    $servername = "localhost";
    $username = "root";
    $dbpassword = "rootroot";
    $dbname = "quickfix";

    $conn = new mysqli($servername, $username, $dbpassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statement to insert service provider data into the database
    $stmt = $conn->prepare("INSERT INTO service_providers (name, email, id_proof, expertise_certificate, preferred_location_id, phone, profile_photo, profession) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiss", $name, $email, $idProof, $expertiseCertificate, $preferredLocation, $phone, $profilePhotoPath, $workfield);

    if ($stmt->execute()) {
//        $registrationMessage = "Registration successful! Your account will be activated after admin approval.";
//    } else {
//        $registrationMessage = "Registration failed. Error: " . $stmt->error;
//    }
//
//    $conn->close();
        // Registration successful, insert into login table
    $loginStmt = $conn->prepare("INSERT INTO login (username, password, role) VALUES (?, ?, ?)");
    
    // Set the role as "service_provider"
    $role = "service_provider";
    
    // Bind parameters
    $loginStmt->bind_param("sss", $email, $hashedPassword, $role);
    
    // Execute the login table insert
    if ($loginStmt->execute()) {
        $registrationMessage = "Registration successful! Your account will be activated after admin approval.";
    } else {
        $registrationMessage = "Registration failed. Error inserting into login table: " . $loginStmt->error;
    }
    
    $loginStmt->close(); // Close the login statement
} else {
    $registrationMessage = "Registration failed. Error: " . $stmt->error;
}

$conn->close(); // Close the database connection
}

// Validate and sanitize functions...
// Validate and sanitize functions
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    
    if (empty($data)) {
        return "This field is required.";
    }
    
    return $data;
}

function validateEmail($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }
    return $email;
}

//function validateConfirmPassword($password, $confirmPassword) {
//    if ($password !== $confirmPassword) {
//        return "Passwords do not match.";
//    }
//    return $password; 
//}

function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) !== 10) {
        return "Invalid phone number";
    }
    return $phone;
}

// Function to handle file uploads
function uploadFile($file, $uploadDir, $allowedFormats) {
    $targetDir = "uploads/$uploadDir/";
    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Handle file name conflicts
    $count = 1;
    while (file_exists($targetFile)) {
        $fileNameWithoutExtension = pathinfo($targetFile, PATHINFO_FILENAME);
        $newFileName = $fileNameWithoutExtension . '_' . $count . '.' . $fileType;
        $targetFile = $targetDir . $newFileName;
        $count++;
    }

    // Check file size (you can adjust the size as needed)
    if ($file["size"] > 5000000) {
        die("File is too large.");
        $uploadOk = 0;
    }

    // Allow only certain file formats
    if (!in_array($fileType, $allowedFormats)) {
        die("Invalid file format.");
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        die("File upload failed.");
    } else {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            die("Error uploading file.");
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>QuickFix - Service Provider Registration</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
    
<body>
     <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">QuickFix</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login2.php">Login</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="registerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Register
                    </a>
                    <div class="dropdown-menu" aria-labelledby="registerDropdown">
                        <a class="dropdown-item" href="userregistration.php">User Register</a>
                        <a class="dropdown-item" href="providerregistration.php">Service Provider Register</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact Us</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Service Provider Registration</h2>
         <?php if (isset($registrationMessage)): ?>
            <p><?php echo $registrationMessage; ?></p>
        <?php endif; ?>
        <br>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
<!--
            <div class="form-group">
    <label for="confirmPassword">Confirm Password:</label>
    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
</div>
-->
            
             <div class="form-group">
                <label for="preferred_location">Preferred Location:</label>
                <select class="form-control" id="preferred_location" name="preferred_location">
                    <!-- PHP code to populate the dropdown -->
                    <?php
                    
                    $servername = "localhost";
                    $username = "root";
                    $dbpassword = "rootroot";
                    $dbname = "quickfix";

                    $conn = new mysqli($servername, $username, $dbpassword, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $query = "SELECT id, city FROM locations";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['city'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{10}" placeholder="Enter your mobile phone number" required>
            </div>
            <div class="form-group">
                <label for="id_proof">ID Proof:</label>
                <input type="file" class="form-control-file" id="id_proof" name="id_proof" accept=".jpg, .jpeg, .png, .pdf" required>
            </div>
            
            <div class="form-group">
             <label for="preferred_location">profession:</label>
                <select class="form-control" id="work_field" name="work_field">
                    <option value="plumber">Plumber</option>
                    <option value="electrician">Electrician</option>
                    <option value="painter">Painter</option>
                    <option value="carpenter">Carpenter</option>
                    <option value="interior-designer">Interior designer</option>
                    <option value="Cleaner">Cleaner</option>
                    <option value="house-keeper">House Keeper</option>
                    <option value="Gardener">Gardener</option>
                </select>
            </div>
            <div class="form-group">
                <label for="expertise_certificate">Expertise Certificate:</label>
                <input type="file" class="form-control-file" id="expertise_certificate" name="expertise_certificate" accept=".jpg, .jpeg, .png, .pdf" required>
            </div>
            <div class="form-group">
                <label for="profile_photo">Profile Photo:</label>
                <input type="file" class="form-control-file" id="profile_photo" name="profile_photo" accept=".jpg, .jpeg, .png" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
       
    </div>
   
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
    
</html>
