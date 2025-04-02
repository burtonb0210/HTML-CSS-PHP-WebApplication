<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Login</title>
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
            width: 50%;
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
            margin-bottom: 5px;
        }

        input {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #a2d5a2;
            border-radius: 5px;
            display: block;
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

        .error-message {
            color: red;
            font-weight: bold;
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
        <h1>Administrator Login (user: admin1 pass: password1)</h1>
    </header>

    <div class="content-section">
        <form method="POST" action="admin_login.php">
            <label for="txtAdminID">Administrator ID:</label>
            <input type="text" id="txtAdminID" name="txtAdminID" required pattern="[A-Za-z0-9]{3,20}" title="Administrator ID should be between 3 and 20 alphanumeric characters.">

            <label for="txtPassword">Password:</label>
            <input type="password" id="txtPassword" name="txtPassword" required minlength="8" title="Password should be at least 8 characters long.">

            <button type="submit">Login</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            $strAdminID = mysqli_real_escape_string($conn, $_POST['txtAdminID']);
            $strAdminPassword = mysqli_real_escape_string($conn, $_POST['txtPassword']);

            // Validate administrator credentials
            $strAdminQuery = "
                SELECT * 
                FROM TAdministrators 
                WHERE strAccountLogin = '$strAdminID' AND strPassword = '$strAdminPassword';
            ";
            $rstAdmin = mysqli_query($conn, $strAdminQuery);

            if ($rstAdmin && mysqli_num_rows($rstAdmin) > 0) {
                // Redirect to admin dashboard
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "<p class='error-message'>Invalid Administrator ID or Password.</p>";
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
