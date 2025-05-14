<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_name"])) {
    header("Location: verification.php"); // Redirect to verification page if not logged in
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "sql211.infinityfree.com";
$username = "if0_38961025";
$password = "SH9gIqKjTa";
$database = "if0_38961025_File_links";

$uploadMessage = "";

function splitLink($link) {
    $partLength = 50;
    $linkParts = [];
    if (strlen($link) > $partLength) {
        $i = 0;
        while ($i < strlen($link)) {
            $linkParts[] = substr($link, $i, $partLength);
            $i += $partLength;
        }
    } else {
        $linkParts[] = $link;
    }
    return $linkParts;
}

function storeFileMetadata($conn, $fileName, $subject, $fileType, $unit, $linkParts, $uploadedBy) {
    $uploadDate = date("Y-m-d");
    $linkPart1 = $linkParts[0] ?? '';
    $linkPart2 = $linkParts[1] ?? '';
    $linkPart3 = $linkParts[2] ?? '';

    $sql = "INSERT INTO File_links (Subject, FileType, Description, UploadDate, LinkPart1, LinkPart2, LinkPart3, UploadedBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        return false;
    }
    $stmt->bind_param("ssssssss", $subject, $fileType, $unit, $uploadDate, $linkPart1, $linkPart2, $linkPart3, $uploadedBy);
    return $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_pdf'])) {
    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] == 0) {
        $fileName = basename($_FILES['pdfFile']['name']);
        $subject = $_POST['subject'];
        $fileType = $_POST['file_type'];
        $unit = $_POST['unit'];
        $uploadedBy = $_SESSION['user_name']; // Get uploaded by from session

        $allowedExtensions = ['pdf'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                if(!mkdir($uploadDir, 0777, true)){
                    error_log("Failed to create directory: " . $uploadDir);
                    $uploadMessage = '<p class="error-message">❌ Failed to create upload directory.</p>';
                }
            }
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['pdfFile']['tmp_name'], $targetPath)) {
                $fileLink = "uploads/" . $fileName;
                $linkParts = splitLink($fileLink);

                $conn = new mysqli($servername, $username, $password, $database);
                if ($conn->connect_error) {
                    error_log("Database connection failed: " . $conn->connect_error);
                    $uploadMessage = '<p class="error-message">❌ Database connection error: ' . $conn->connect_error . '</p>';
                } else {
                    if (storeFileMetadata($conn, $fileName, $subject, $fileType, $unit, $linkParts, $uploadedBy)) {
                        $uploadMessage = '<p class="success-message">✅ File uploaded successfully by ' . htmlspecialchars($uploadedBy) . '.</p>';
                    } else {
                        $uploadMessage = '<p class="error-message">❌ Error storing file metadata.</p>';
                    }
                    $conn->close();
                }
            } else {
                error_log("Failed to upload file. Error code: " . $_FILES['pdfFile']['error']);
                $uploadMessage = '<p class="error-message">❌ Failed to upload file. Error code: ' . $_FILES['pdfFile']['error'] . '</p>';
            }
        } else {
            $uploadMessage = '<p class="error-message">❌ Invalid file type. Only PDF files are allowed.</p>';
        }
    } else {
        $uploadMessage = '<p class="error-message">❌ No file uploaded or error during upload.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Upload PDF - Student-Sphere</title>
    <style>
        /* Reset and Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom, #a78bfa, #e0f2f7); /* Same background gradient as home.php */
            color: #333; /* Dark text on light background */
            min-height: 100vh;
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            padding: 20px;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white container */
            padding: 30px; /* Adjusted padding */
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); /* Darker shadow for contrast */
            max-width: 700px;
            width: 90%; /* Adjust width for better mobile fit */
            color: #333; /* Dark text inside container */
        }

        h2 {
            text-align: center;
            color: #007acc;
            margin-bottom: 25px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        select,
        input[type="file"] {
            padding: 12px;
            border: 1px solid #ccddee;
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        button[type="submit"] {
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .success-message {
            color: #28a745;
            background-color: #d4edda;
            border-left: 5px solid #28a745;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .back-home {
            display: block;
            margin-top: 30px;
            text-align: center;
            color: #00aaff;
            text-decoration: none;
        }

        .back-home:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media screen and (max-width: 600px) {
            .container {
                width: 95%;
                padding: 20px;
            }
        }
    </style>

          <script async src="https://www.googletagmanager.com/gtag/js?id=G-SF3J3SPLS7"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-SF3J3SPLS7');
    </script>
</head>
<body>
    <div class="container">
        <h2>Upload New PDF</h2>
        <?php echo $uploadMessage; ?>
        <form method="post" enctype="multipart/form-data">
            <label for="subject">Subject:</label>
            <select name="subject" id="subject" required>
                <option value="">Select a Subject</option>
                <option value="ATCD">ATCD</option>
                <option value="CV">CV</option>
                <option value="DT">DT</option>
                <option value="ML">ML</option>
                <option value="FIOT">FIOT</option>
            </select>

            <label for="file_type">File Type:</label>
            <select name="file_type" id="file_type" required>
                <option value="">Select File Type</option>
                <option value="Assignment">Assignment</option>
                <option value="Question Bank">Question Bank</option>
                <option value="Lecture Notes">Lecture Notes</option>
            </select>

            <label for="unit">Unit:</label>
            <select name="unit" id="unit" required>
                <option value="">Select Unit</option>
                <option value="Unit 1">Unit 1</option>
                <option value="Unit 2">Unit 2</option>
                <option value="Unit 3">Unit 3</option>
                <option value="Unit 4">Unit 4</option>
                <option value="Unit 5">Unit 5</option>
            </select>

            <label for="pdfFile">Select PDF File:</label>
            <input type="file" name="pdfFile" id="pdfFile" accept=".pdf" required>

            <button type="submit" name="upload_pdf">Upload PDF</button>
        </form>
        <a href="home.php" class="back-home">← Back to Home</a>
    </div>
</body>
</html>
