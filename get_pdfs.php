<?php
// Database connection details
$servername = "//";
$username = "//";
$password = "//";
$database = "//";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pdfLinks = [];
if (isset($_POST['subject_submit']) && isset($_POST['subject'])) {
    $selectedSubject = $_POST['subject'];
    $selectedType = $_POST['file_type'] ?? 'all';
    $selectedUnit = $_POST['unit'] ?? 'all'; // Get selected unit, default to 'all'

    $sql = "SELECT LinkPart1, LinkPart2, LinkPart3, FileType, Description, UploadedBy, UploadDate FROM File_links WHERE Subject = ?";
    if ($selectedType !== 'all') {
        $sql .= " AND FileType = ?";
    }
    if ($selectedUnit !== 'all') {
        $sql .= " AND Description = ?";
    }
    $stmt = $conn->prepare($sql);

    if ($selectedType !== 'all' && $selectedUnit !== 'all') {
        $stmt->bind_param("sss", $selectedSubject, $selectedType, $selectedUnit);
    } elseif ($selectedType !== 'all') {
        $stmt->bind_param("ss", $selectedSubject, $selectedType);
    } elseif ($selectedUnit !== 'all') {
        $stmt->bind_param("ss", $selectedSubject, $selectedUnit);
    } else {
        $stmt->bind_param("s", $selectedSubject);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $fullLink = $row['LinkPart1'] . $row['LinkPart2'] . $row['LinkPart3'];
            $pdfLinks[] = [
                'type' => $row['FileType'],
                'unit' => $row['Description'],
                'link' => $fullLink,
                'uploaded_by' => $row['UploadedBy'],
                'upload_date' => $row['UploadDate']
            ];
        }
    } else {
        $noResultsMessage = "No PDFs found for the selected criteria.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Get PDFs - Student-Sphere</title>
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

        .main-title {
            display: none; /* Remove the main title */
        }

        .tagline {
            display: none; /* Remove the tagline */
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

        h3 {
            color: #007acc;
            margin-bottom: 15px;
        }

        h4 {
            color: #2c3e50; /* A dark gray/blue for the subject */
            margin-bottom: 8px;
            font-size: 1.1em;
            font-weight: bold;
            text-align: center; /* Center the subject name above the preview */
        }

        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        select {
            padding: 12px;
            border: 1px solid #ccddee;
            border-radius: 8px;
            width: 200px;
        }

        button[type="submit"] {
            padding: 12px 20px;
            background-color: #00aaff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #007acc;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li {
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 25px;
            background-color: #f9fcff;
        }

        .pdf-preview {
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            background: #f5faff;
        }

        .pdf-preview embed {
            width: 100%;
            height: 220px;
        }

        li a.pdf-title {
            font-size: 1.1em;
            font-weight: 600;
            color: #007acc;
            text-decoration: none;
            display: none; /* Hide the PDF title link */
            margin-bottom: 5px;
        }

        li a.pdf-title:hover {
            text-decoration: underline;
        }

        .pdf-info {
            font-size: 0.9em;
            color: #666;
            margin: 4px 0 10px;
        }

        .download-btn {
            display: inline-block;
            padding: 10px 18px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: background-color 0.3s ease;
            text-align: center;
        }

        .download-btn:hover {
            background-color: #218838;
        }

        .no-results {
            text-align: center;
            font-style: italic;
            color: #777;
            margin-top: 20px;
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

            select {
                width: 100%;
            }

            form {
                flex-direction: column;
                align-items: center;
            }

            button[type="submit"] {
                width: 100%;
            }

            .pdf-preview embed {
                height: 150px; /* Further adjust preview height for smaller mobiles */
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
        <h2>Get Available PDFs</h2>

        <form method="post">
            <select name="subject" id="subject-select" required>
                <option value="">Select a Subject</option>
                <option value="ATCD" <?php if (isset($_POST['subject']) && $_POST['subject'] == 'ATCD') echo 'selected'; ?>>ATCD</option>
                <option value="CV" <?php if (isset($_POST['subject']) && $_POST['subject'] == 'CV') echo 'selected'; ?>>CV</option>
                <option value="DT" <?php if (isset($_POST['subject']) && $_POST['subject'] == 'DT') echo 'selected'; ?>>DT</option>
                <option value="ML" <?php if (isset($_POST['subject']) && $_POST['subject'] == 'ML') echo 'selected'; ?>>ML</option>
                <option value="FIOT" <?php if (isset($_POST['subject']) && $_POST['subject'] == 'FIOT') echo 'selected'; ?>>FIOT</option>
            </select>

            <select name="file_type" id="file-type-select">
                <option value="all">All Types</option>
                <option value="Assignment" <?php if (isset($_POST['file_type']) && $_POST['file_type'] == 'Assignment') echo 'selected'; ?>>Assignment</option>
                <option value="Question Bank" <?php if (isset($_POST['file_type']) && $_POST['file_type'] == 'Question Bank') echo 'selected'; ?>>Question Bank</option>
                <option value="Lecture Notes" <?php if (isset($_POST['file_type']) && $_POST['file_type'] == 'Lecture Notes') echo 'selected'; ?>>Lecture Notes</option>
            </select>

            <select name="unit" id="unit-select">
                <option value="all">All Units</option>
                <option value="Unit 1" <?php if (isset($_POST['unit']) && $_POST['unit'] == 'Unit 1') echo 'selected'; ?>>Unit 1</option>
                <option value="Unit 2" <?php if (isset($_POST['unit']) && $_POST['unit'] == 'Unit 2') echo 'selected'; ?>>Unit 2</option>
                <option value="Unit 3" <?php if (isset($_POST['unit']) && $_POST['unit'] == 'Unit 3') echo 'selected'; ?>>Unit 3</option>
                <option value="Unit 4" <?php if (isset($_POST['unit']) && $_POST['unit'] == 'Unit 4') echo 'selected'; ?>>Unit 4</option>
                <option value="Unit 5" <?php if (isset($_POST['unit']) && $_POST['unit'] == 'Unit 5') echo 'selected'; ?>>Unit 5</option>
            </select>

            <button type="submit" name="subject_submit">Get PDFs</button>
        </form>

        <?php if (isset($noResultsMessage)): ?>
            <p class="no-results"><?php echo $noResultsMessage; ?></p>
        <?php elseif (!empty($pdfLinks)): ?>
            <h3>Available PDFs:</h3>
            <ul>
                <?php foreach ($pdfLinks as $pdf): ?>
                    <li>
                        <h4><?php echo htmlspecialchars($pdf['type']); ?> - <?php echo htmlspecialchars($pdf['unit']); ?></h4>
                        <div class="pdf-preview">
                            <embed src="<?php echo htmlspecialchars($pdf['link']); ?>" type="application/pdf">
                        </div>
                        <p class="pdf-info">
                            Uploaded by: <?php echo htmlspecialchars($pdf['uploaded_by']); ?> |
                            Date: <?php echo htmlspecialchars($pdf['upload_date']); ?>
                        </p>
                        <a href="<?php echo htmlspecialchars($pdf['link']); ?>" download class="download-btn"> Download</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <a href="home.php" class="back-home">← Back to Home</a>
    </div>
</body>
</html>
