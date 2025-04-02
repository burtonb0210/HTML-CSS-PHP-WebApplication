<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Golfers</title>
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
            width: 90%;
            max-width: 1200px;
            padding: 20px;
            background-color: #ffffff;
            border: 2px solid #a2d5a2;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
        }

        th, td {
            border: 1px solid #a2d5a2;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #006400;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        .actions a {
            text-decoration: none;
            color: #006400;
            font-weight: bold;
        }

        .actions a:hover {
            text-decoration: underline;
        }
		
		.button-container {
            margin-top: 20px;
            text-align: center;
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

        footer {
            margin-top: 20px;
            background-color: #333;
            color: white;
            padding: 10px;
        }
    </style>
</head>

<body>
    <header>
        <h1>View Golfers</h1>
    </header>

    <div class="content-section">
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zip Code</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection
                $strServerName = "mc-itddb-12-e-1";
                $strUsername = "bwburton";
                $strPassword = "0733033";
                $strDBName = "01WAPP1400BurtonB";

                $conn = mysqli_connect($strServerName, $strUsername, $strPassword, $strDBName);

                if (!$conn) {
                    die("<p>Connection failed: " . mysqli_connect_error() . "</p>");
                }

                // Query to fetch golfer data
                $strQueryGolfers = "
                    SELECT 
                        TG.intGolferID, 
                        TG.strFirstName, 
                        TG.strLastName, 
                        TG.strAddress, 
                        TG.strCity, 
                        TS.strState, 
                        TG.strZipCode, 
                        TG.strPhoneNumber, 
                        TG.strEmail
                    FROM 
                        TGolfers AS TG
                    JOIN 
                        TStates AS TS ON TG.intStateID = TS.intStateID
                    ORDER BY 
                        TG.intGolferID;
                ";

                $rstGolfers = mysqli_query($conn, $strQueryGolfers);

                if ($rstGolfers && mysqli_num_rows($rstGolfers) > 0) {
                    while ($udtGolfer = mysqli_fetch_assoc($rstGolfers)) {
                        echo "<tr>
                            <td>" . $udtGolfer['strFirstName'] . "</td>
                            <td>" . $udtGolfer['strLastName'] . "</td>
                            <td>" . $udtGolfer['strAddress'] . "</td>
                            <td>" . $udtGolfer['strCity'] . "</td>
                            <td>" . $udtGolfer['strState'] . "</td>
                            <td>" . $udtGolfer['strZipCode'] . "</td>
                            <td>" . $udtGolfer['strPhoneNumber'] . "</td>
                            <td>" . $udtGolfer['strEmail'] . "</td>
                            <td class='actions'><a href='edit_golfer.php?intGolferID=" . $udtGolfer['intGolferID'] . "'>Edit</a></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No golfers found.</td></tr>";
                }

                mysqli_close($conn);
                ?>
            </tbody>
        </table>
		
		<!-- Back to Home Button -->
        <div class="button-container">
            <a href="GolfathonHome.php">
                <button type="button">Back to Home</button>
            </a>
        </div>
    </div>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>

</html>
