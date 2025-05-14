
<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header("Location: index.php");
    exit();
}

$userName = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Welcome to Student-Sphere!</title>
    <style>
        /* Reset and Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom, #a78bfa, #e0f2f7); /* Different background gradient */
            color: #333; /* Dark text on light background */
            min-height: 100vh;
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .main-title {
            font-size: 3em; /* Make it larger */
            color: #ff6f00; /* Attractive orange color */
            background-color: #000; /* Black title background */
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .tagline {
            font-size: 1.3em;
            color: #555; /* Darker tagline text */
            margin-top: 30px;
            font-style: italic;
            opacity: 0; /* Start with 0 opacity for fade-in */
            animation: fadeIn 1s ease-out 1s forwards; /* Fade in animation with delay */
            transition: margin-bottom 0.3s ease; /* Smooth transition for margin */
        }

        @keyframes fadeIn {
            to {
                opacity: 0.8; /* Fade to the desired opacity */
            }
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white container */
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); /* Darker shadow for contrast */
            text-align: center;
            max-width: 480px;
            width: 100%;
            color: #333; /* Dark text inside container */
            order: 2; /* Default order */
        }

        h2 {
            font-size: 2.2rem; /* Slightly larger */
            color: #00aaff; /* Accent color */
            margin-bottom: 30px;
            font-weight: bold;
        }

        .button-container {
            display: flex;
            flex-direction: column; /* Stack buttons on smaller screens */
            gap: 15px; /* Space between buttons */
            width: 100%;
            max-width: 300px; /* Slightly wider button area */
            margin: 20px auto; /* Center button container */
        }

        .button {
            background-color: #00aaff;
            color: #ffffff;
            padding: 16px; /* Slightly larger padding */
            border: none;
            border-radius: 8px;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .button:hover {
            background-color: #007acc;
            transform: scale(1.05); /* Slightly more pronounced hover effect */
        }

        /* Responsive Design */
        @media (min-width: 600px) {
            .button-container {
                flex-direction: row; /* Buttons in a row on larger screens */
                justify-content: center; /* Center buttons horizontally */
                max-width: none; /* Remove max-width for row layout */
            }

            .button {
                width: auto;
                min-width: 150px; /* Minimum width for buttons in a row */
                margin: 0 10px;
            }

            h2 {
                font-size: 2.5rem;
            }

            .container {
                padding: 50px 40px;
                max-width: 600px; /* Wider container on larger screens */
            }

            body {
                justify-content: center; /* Center vertically on larger screens */
            }

            .tagline {
                margin-bottom: 40px; /* Add space below tagline on PC */
                opacity: 0.8; /* Ensure it's visible on PC */
                animation: none; /* Disable the initial fade-in on PC if it's distracting */
            }
        }

        @media (max-width: 768px) {
            body {
                justify-content: space-around; /* Distribute space vertically on smaller screens */
                background: linear-gradient(to bottom, #a78bfa, #e0f2f7); /* Same background for mobile */
            }

            .container {
                order: 1; /* Container first on mobile */
                margin-bottom: 30px;
            }

            .main-title {
                font-size: 2.6em;
                margin-bottom: 15px;
                order: 0; /* Title above container */
                padding: 8px 16px; /* Adjust padding for mobile title */
            }

            .tagline {
                font-size: 1.2em;
                margin-top: 25px;
                order: 3; /* Tagline below container */
                color: #777; /* Slightly lighter tagline on mobile */
                opacity: 0; /* Start with 0 opacity for fade-in */
                animation: fadeIn 1s ease-out 1s forwards; /* Fade in animation */
                margin-bottom: 20px; /* Add a little space below tagline on mobile */
            }
        }

        @media (max-width: 480px) {
            .main-title {
                font-size: 2.4em;
                padding: 6px 12px;
                border-radius: 6px;
            }

            .tagline {
                font-size: 1.1em;
                margin-top: 20px;
            }

            .container {
                padding: 30px 20px;
                border-radius: 12px;
                margin-bottom: 20px;
            }

            h2 {
                font-size: 2rem;
                margin-bottom: 25px;
            }

            .button {
                font-size: 15px;
                padding: 14px;
                border-radius: 6px;
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
        <h2> Welcome, <?php echo htmlspecialchars($userName); ?>!</h2>
        <div class="button-container">
            <a href="get_pdfs.php" class="button"> Get Resources</a>
            <a href="verification.php" class="button">Upload Resource</a>
        </div>
    </div>

</body>
</html>
