<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add New Event</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #d9f2d9;
            text-align: center;
        }

        header {
            background-color: #006400;
            color: white;
            padding: 20px;
        }

        .content-section {
            margin: 20px auto;
            width: 40%;
            padding: 20px;
            background-color: #ffffff;
            border: 2px solid #a2d5a2;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: left;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #a2d5a2;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #006400;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #004d00;
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-top: 20px;
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-top: 20px;
        }

        footer {
            margin-top: 20px;
            background-color: #333;
            color: white;
            padding: 10px;
        }

        /* Style for invalid inputs */
        input:required:invalid {
            border-color: red;
        }

        input:valid {
            border-color: green;
        }
    </style>
</head>

<body>
    <header>
        <h1>Add New Event</h1>
    </header>

    <div class="content-section">
        <form method="POST" action="add_event.php">
            <label for="txtEventYear">Event Year:</label>
            <input 
                type="text" 
                id="txtEventYear" 
                name="txtEventYear" 
                maxlength="4" 
                required 
                pattern="^\d{4}$" 
                title="Event Year must be a 4-digit number (e.g., 2024).">

            <button type="submit">Add Event</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Database connection
            $strServerName = "mc-itddb-12-e-1";
            $strUsername = "bwburton";
            $strPassword = "0733033";
            $strDBName = "01WAPP1400BurtonB";

            $conn = mysqli_connect($strServerName, $strUsername, $strPassword, $strDBName);

            if (!$conn) {
                die("<p class='error-message'>Connection failed: " . mysqli_connect_error() . "</p>");
            }

            // Get form data
            $strEventYear = mysqli_real_escape_string($conn, $_POST['txtEventYear']);

            // Insert event into the database
            $strEventQuery = "
                INSERT INTO TEvents (dtmEventYear) 
                VALUES ('$strEventYear');
            ";
            if (mysqli_query($conn, $strEventQuery)) {
                echo "<p class='success-message'>Event added successfully!</p>";
            } else {
                echo "<p class='error-message'>Error adding event: " . mysqli_error($conn) . "</p>";
            }

            mysqli_close($conn);
        }
        ?>
    </div>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>

</html>
