<?php
session_start();

// Database connection details (using your provided credentials)
$servername = "//";
$username = "//";
$password = "//";
$dbname = "//";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create the 'students' table if it doesn't exist
// Adjusted to use your table and column names
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    studentname VARCHAR(20) NOT NULL,
    date DATE NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    // Table created successfully (or already existed)
} else {
    echo "Error creating table: " . $conn->error; // Don't die() here
}


if (isset($_POST['name_submit'])) {
    if (!empty($_POST['name'])) {
        $name = $_POST['name'];
        $_SESSION['user_name'] = $name; // Store name in session

        // Get the current date
        $date = date("Y-m-d");

        // Prepare and execute the SQL query to insert user data
        $stmt = $conn->prepare("INSERT INTO students (studentname, date) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $date); // "ss" indicates two string parameters

        if ($stmt->execute()) {
            header("Location: home.php"); // Redirect after successful insertion
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();

    } else {
        $error_message = "Please enter your name.";
    }
}


$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome - Student-Sphere</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to bottom, #a78bfa, #e0f2f7);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.95);
      max-width: 500px;
      width: 90%;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h1 {
      color: #ff6f00;
      font-size: 2.8em;
      margin-bottom: 15px;
      background-color: black;
      padding: 10px 20px;
      border-radius: 15px;
      display: inline-block;
    }

    h2 {
      color: #007acc;
      margin-bottom: 25px;
      font-size: 1.5em;
    }

    input[type="text"] {
      padding: 14px;
      width: 100%;
      border: 1px solid #ccddee;
      border-radius: 8px;
      font-size: 16px;
      margin-bottom: 20px;
      box-sizing: border-box;
    }

    input[type="text"]:focus {
      border-color: #007acc;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 122, 204, 0.2);
    }

    button {
      padding: 12px;
      width: 100%;
      background-color: #00aaff;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #007acc;
    }

    .error-message {
      color: #dc3545;
      background-color: #f8d7da;
      border-left: 5px solid #dc3545;
      padding: 10px;
      border-radius: 8px;
      margin-top: 10px;
    }

    .tagline {
      margin-top: 25px;
      color: #007acc;
      font-style: italic;
      font-size: 1.1em;
      opacity: 0;
      animation: fadeIn 1s ease-in 0.5s forwards;
    }

    @keyframes fadeIn {
      to { opacity: 1; }
    }

    @media (max-width: 600px) {
      .container {
        padding: 30px;
        border-radius: 10px;
      }

      h1 {
        font-size: 2.2em;
      }

      h2 {
        font-size: 1.3em;
      }

      .tagline {
        font-size: 1em;
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
    <h1>Student-Sphere</h1>
    <h2>Let's Begin</h2>
    <form method="POST">
      <input type="text" name="name" placeholder="Enter your name" />
      <button type="submit" name="name_submit">Continue</button>
      <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>
    </form>
    <div class="tagline">No more time waste.</div>
  </div>
</body>
</html>
