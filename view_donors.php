<!DOCTYPE html>
<html lang="en">

<head>
    <title>Donors for Golfer</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th,
        table td {
            border: 1px solid #a2d5a2;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #006400;
            color: white;
        }

        table td {
            background-color: #f9f9f9;
        }

        footer {
            margin-top: 20px;
            background-color: #333;
            color: white;
            padding: 10px;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        button {
            background-color: #006400;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        button:hover {
            background-color: #004d00;
        }

        .button-container {
            margin-top: 20px;
            text-align: center;
        }

        a.payment-status {
            color: #006400;
            text-decoration: underline;
            font-weight: bold;
            cursor: pointer;
        }

        a.payment-status:hover {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <header>
        <h1>Donors for Golfer</h1>
    </header>

    <div class="content-section">
        <?php
        // Ensure intEventGolferID is provided via the query string
        if (isset($_GET['intEventGolferID']) && !empty($_GET['intEventGolferID'])) {
            $intEventGolferID = intval($_GET['intEventGolferID']);

            // Database connection
            $strServerName = "mc-itddb-12-e-1";
            $strUsername = "bwburton";
            $strPassword = "0733033";
            $strDBName = "01WAPP1400BurtonB";

            $conn = mysqli_connect($strServerName, $strUsername, $strPassword, $strDBName);

            if (!$conn) {
                die("<p class='error-message'>Connection failed: " . mysqli_connect_error() . "</p>");
            }

            // Query to get donors for the selected golfer
            $strDonorsQuery = "
                SELECT 
                    TS.intSponsorID,
                    TS.strFirstName AS strSponsorFirstName,
                    TS.strLastName AS strSponsorLastName,
                    COALESCE(SUM(TEGS.decPledgePerHole * 9), 0) AS decTotalPledged,
                    CASE 
                        WHEN TEGS.intPaymentStatusID = 1 THEN 'Paid'
                        ELSE 'Unpaid'
                    END AS strPaymentStatus
                FROM 
                    TEventGolferSponsors as TEGS
                JOIN 
                    TSponsors as TS ON TEGS.intSponsorID = TS.intSponsorID
                WHERE 
                    TEGS.intEventGolferID = $intEventGolferID
                GROUP BY 
                    TS.intSponsorID, TS.strFirstName, TS.strLastName, TEGS.intPaymentStatusID;
            ";

            $rstDonors = mysqli_query($conn, $strDonorsQuery);

            if ($rstDonors && mysqli_num_rows($rstDonors) > 0) {
                echo "<table>
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Total Pledged</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>";

                while ($udtDonor = mysqli_fetch_assoc($rstDonors)) {
                    $strPaymentLink = "change_status.php?intSponsorID={$udtDonor['intSponsorID']}&intEventGolferID={$intEventGolferID}";
                    echo "<tr>
                            <td>" . htmlspecialchars($udtDonor['strSponsorFirstName'] . " " . $udtDonor['strSponsorLastName']) . "</td>
                            <td>$" . number_format($udtDonor['decTotalPledged'], 2) . "</td>
                            <td><a class='payment-status' href='$strPaymentLink'>" . htmlspecialchars($udtDonor['strPaymentStatus']) . "</a></td>
                          </tr>";
                }

                echo "</tbody>
                    </table>";
            } else {
                echo "<p>No donors found for this golfer.</p>";
            }

            mysqli_close($conn);
        } else {
            echo "<p class='error-message'>Invalid request. Golfer ID is missing.</p>";
        }
        ?>

        <div class="button-container">
            <a href="manage_golfers.php">
                <button type="button">Back to Manage Event</button>
            </a>
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
