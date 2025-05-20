<?php
session_start();

$servername = "";
$dbUsername="";
$dbPassword = "";
$dbName = "";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    // Connect to database
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
    if ($conn->connect_error) {
        $error = "❌ Database connection failed.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($storedPassword);
            $stmt->fetch();

            // Plain text comparison (use hashing in production)
            if ($password === $storedPassword) {
                $_SESSION["user_name"] = $username;
                header("Location: upload.php");
                exit();
            } else {
                $error = "❌ Incorrect password.";
            }
        } else {
            $error = "❌ Username not found.";
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Verification - Student-Sphere</title>
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
      width: 100%;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h2 {
      color: #007acc;
      margin-bottom: 25px;
      font-size: 1.8em;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 18px;
      margin-top: 10px;
    }

    label {
      text-align: left;
      font-weight: 600;
      color: #333;
    }

    input[type="text"],
    input[type="password"] {
      padding: 14px;
      border: 1px solid #ccddee;
      border-radius: 8px;
      font-size: 16px;
      width: 100%;
      box-sizing: border-box;
    }

    input:focus {
      outline: none;
      border-color: #007acc;
      box-shadow: 0 0 5px rgba(0, 122, 204, 0.2);
    }

    button {
      padding: 12px;
      background-color: #00aaff;
      border: none;
      color: white;
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
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 15px;
      display: <?php echo empty($error) ? 'none' : 'block'; ?>;
    }

    .back-home {
      display: inline-block;
      margin-top: 20px;
      color: #00aaff;
      text-decoration: none;
    }

    .back-home:hover {
      text-decoration: underline;
    }

    @media screen and (max-width: 600px) {
      .container {
        padding: 30px;
        border-radius: 10px;
      }

      h2 {
        font-size: 1.5em;
      }

      input, button {
        font-size: 15px;
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
    <h2>User Verification</h2>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <form method="POST" action="verification.php">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required />

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required />

      <button type="submit">Verify & Continue</button>
    </form>
    <a href="home.php" class="back-home">← Back to Home</a>
  </div>
</body>
</html>
