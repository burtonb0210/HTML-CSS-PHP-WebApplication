<!DOCTYPE html>
<html lang="en">

<head>
    <title>Golfer Statistics</title>
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
            width: 80%;
            padding: 20px;
            background-color: #ffffff;
            border: 2px solid #a2d5a2;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: left;
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

        .donor-link {
            color: #006400;
            text-decoration: none;
            font-weight: bold;
        }

        .donor-link:hover {
            text-decoration: underline;
        }

        footer {
            margin-top: 20px;
            background-color: #333;
            color: white;
            padding: 10px;
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
    </style>
</head>

<body>
    <header>
        <h1>Golfer Statistics</h1>
    </header>

    <div class="content-section">
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

        $intCurrentEventID = 3; // Current event ID for 2024

        // Total amount pledged for the event
        $strTotalPledgesQuery = "
            SELECT SUM(TEGS.decPledgePerHole * 9) AS TotalPledges
            FROM TEventGolferSponsors AS TEGS
            JOIN TEventGolfers AS TEG ON TEGS.intEventGolferID = TEG.intEventGolferID
            WHERE TEG.intEventID = $intCurrentEventID;
        ";
        $rstTotalPledges = mysqli_query($conn, $strTotalPledgesQuery);
        $dblTotalPledges = mysqli_fetch_assoc($rstTotalPledges)['TotalPledges'] ?? 0;

        // Total number of donations for the event
        $strTotalDonationsQuery = "
            SELECT COUNT(*) AS TotalDonations
            FROM TEventGolferSponsors AS TEGS
            JOIN TEventGolfers AS TEG ON TEGS.intEventGolferID = TEG.intEventGolferID
            WHERE TEG.intEventID = $intCurrentEventID;
        ";
        $rstTotalDonations = mysqli_query($conn, $strTotalDonationsQuery);
        $intTotalDonations = mysqli_fetch_assoc($rstTotalDonations)['TotalDonations'] ?? 0;

        // Average donation amount for the event
        $dblAverageDonation = $intTotalDonations > 0 ? $dblTotalPledges / $intTotalDonations : 0;

        // Grid of golfers with total pledges
        $strGolfersQuery = "
            SELECT 
                TG.strFirstName, 
                TG.strLastName, 
                COALESCE(SUM(TEGS.decPledgePerHole * 9), 0) AS TotalPledged,
                COUNT(DISTINCT TEGS.intSponsorID) AS DonorCount,
                TEG.intEventGolferID
            FROM TEventGolfers AS TEG
            JOIN TGolfers AS TG ON TEG.intGolferID = TG.intGolferID
            LEFT JOIN TEventGolferSponsors TEGS ON TEG.intEventGolferID = TEGS.intEventGolferID
            WHERE TEG.intEventID = $intCurrentEventID
            GROUP BY TG.intGolferID, TEG.intEventGolferID
            ORDER BY TotalPledged DESC;
        ";
        $rstGolfers = mysqli_query($conn, $strGolfersQuery);

        if (!$rstGolfers) {
            die("<p>Failed to fetch golfer data: " . mysqli_error($conn) . "</p>");
        }
        ?>

        <h2>Current Statistics</h2>
        <p><strong>Total Amount of Pledges:</strong> $<?php echo number_format($dblTotalPledges, 2); ?></p>
        <p><strong>Total Number of Donations:</strong> <?php echo $intTotalDonations; ?></p>
        <p><strong>Average Donation:</strong> $<?php echo number_format($dblAverageDonation, 2); ?></p>

        <h2>Golfer Pledges</h2>
        <table>
            <thead>
                <tr>
                    <th>Golfer Name</th>
                    <th>Total Pledged</th>
                    <th>Number of Donors</th>
                    <th>Donors</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($udtGolfer = mysqli_fetch_assoc($rstGolfers)): ?>
                    <tr>
                        <td><?php echo $udtGolfer['strFirstName'] . ' ' . $udtGolfer['strLastName']; ?></td>
                        <td>$<?php echo number_format($udtGolfer['TotalPledged'], 2); ?></td>
                        <td><?php echo $udtGolfer['DonorCount']; ?></td>
                        <td>
                            <a class="donor-link" href="golfer_donors.php?eventGolferID=<?php echo $udtGolfer['intEventGolferID']; ?>">
                                View Donors
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php mysqli_close($conn); ?>
    </div>

    <!-- Back to Home Button -->
    <div class="button-container">
        <a href="GolfathonHome.php">
            <button type="button">Back to Home</button>
        </a>
    </div>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>

</html>
