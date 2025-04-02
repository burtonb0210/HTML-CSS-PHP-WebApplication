<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Event Golfers</title>
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
            width: 60%;
            padding: 20px;
            background-color: #ffffff;
            border: 2px solid #a2d5a2;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-weight: bold;
            margin-bottom: 10px;
        }

        select {
            width: 60%;
            max-width: 400px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #a2d5a2;
            border-radius: 5px;
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

        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px; /* Add spacing between buttons */
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        <h1>Manage Event Golfers</h1>
    </header>

    <div class="content-section">
        <form method="POST" action="manage_golfers.php" class="form-container">
            <label for="cmbEventYear">Select Event:</label>
            <select id="cmbEventYear" name="cmbEventYear" required>
                <option value="" disabled selected>Select Year</option>
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

                // Query to populate the dropdown
                $strEventsQuery = "
                    SELECT intEventID, dtmEventYear 
                    FROM TEvents 
                    ORDER BY dtmEventYear ASC;
                ";
                $rstEvents = mysqli_query($conn, $strEventsQuery);

                if (mysqli_num_rows($rstEvents) > 0) {
                    while ($udtEvent = mysqli_fetch_assoc($rstEvents)) {
                        $intEventYear = $udtEvent['dtmEventYear'];
                        $strSelected = (isset($_POST['cmbEventYear']) && $_POST['cmbEventYear'] == $intEventYear) ? 'selected' : '';
                        echo "<option value='$intEventYear' $strSelected>$intEventYear</option>";
                    }
                } else {
                    echo "<option value=''>No events available</option>";
                }

                mysqli_close($conn);
                ?>
            </select>
            <button type="submit">View Golfers</button>
        </form>

        <?php
        if (isset($_POST['cmbEventYear']) && !empty($_POST['cmbEventYear'])) {
            $intEventYear = intval($_POST['cmbEventYear']);

            // Re-establish database connection
            $conn = mysqli_connect($strServerName, $strUsername, $strPassword, $strDBName);

            if (!$conn) {
                die("<p>Connection failed: " . mysqli_connect_error() . "</p>");
            }

            // Query golfers for the selected event year
            $strGolfersQuery = "
                SELECT 
                    TEG.intEventGolferID, 
                    TG.strFirstName, 
                    TG.strLastName, 
                    COALESCE(SUM(TEGS.decPledgePerHole * 9), 0) AS decTotalPledged,
                    COALESCE(SUM(CASE WHEN TEGS.intPaymentStatusID = 1 THEN TEGS.decPledgePerHole * 9 ELSE 0 END), 0) AS decTotalCollected
                FROM 
                    TEventGolfers as TEG
                JOIN 
                    TGolfers as TG ON TEG.intGolferID = TG.intGolferID
                LEFT JOIN 
                    TEventGolferSponsors as TEGS ON TEG.intEventGolferID = TEGS.intEventGolferID
                JOIN 
                    TEvents as TE ON TEG.intEventID = TE.intEventID
                WHERE 
                    TE.dtmEventYear = $intEventYear
                GROUP BY 
                    TEG.intEventGolferID, TG.strFirstName, TG.strLastName;
            ";
            $rstGolfers = mysqli_query($conn, $strGolfersQuery);

            if (mysqli_num_rows($rstGolfers) > 0) {
                echo '<table>
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Total Pledged</th>
                            <th>Total Collected</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>';

                while ($udtGolfer = mysqli_fetch_assoc($rstGolfers)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($udtGolfer['strFirstName']) . "</td>
                        <td>" . htmlspecialchars($udtGolfer['strLastName']) . "</td>
                        <td>$" . number_format($udtGolfer['decTotalPledged'], 2) . "</td>
                        <td>$" . number_format($udtGolfer['decTotalCollected'], 2) . "</td>
                        <td>
                            <a href='view_donors.php?intEventGolferID={$udtGolfer['intEventGolferID']}'>
                                <button type='button'>View Donors</button>
                            </a>
                        </td>
                    </tr>";
                }

                echo '</tbody>
                </table>';
            } else {
                echo '<p>No golfers found for the selected event year.</p>';
            }

            mysqli_close($conn);
        }
        ?>

        <div class="button-container">
            <a href="admin_dashboard.php">
                <button type="button">Back to Admin Dashboard</button>
            </a>
        </div>
    </div>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>

</html>
