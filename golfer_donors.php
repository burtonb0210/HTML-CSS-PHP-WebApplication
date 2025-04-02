<!DOCTYPE html>
<html lang="en">

<head>
    <title>Golfer Donors</title>
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

        footer {
            margin-top: 20px;
            background-color: #333;
            color: white;
            padding: 10px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #006400;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <header>
        <h1>Donors for Golfer</h1>
    </header>

    <div class="content-section">
        <?php
        // Ensure eventGolferID is provided
        if (!isset($_GET['eventGolferID']) || empty($_GET['eventGolferID'])) {
            echo "<p>Invalid request. Golfer information is missing.</p>";
            echo "<a href='golfer_statistics.php' class='back-link'>Back to Golfer Statistics</a>";
            exit();
        }

        $eventGolferID = intval($_GET['eventGolferID']);

        // Database connection
        $conn = mysqli_connect("mc-itddb-12-e-1", "bwburton", "0733033", "01WAPP1400BurtonB");

        if (!$conn) {
            die("<p>Connection failed: " . mysqli_connect_error() . "</p>");
        }

        // Fetch golfer name
        $golferQuery = "
            SELECT g.strFirstName, g.strLastName
            FROM TEventGolfers eg
            JOIN TGolfers g ON eg.intGolferID = g.intGolferID
            WHERE eg.intEventGolferID = $eventGolferID
        ";
        $golferResult = mysqli_query($conn, $golferQuery);
        $golfer = mysqli_fetch_assoc($golferResult);

        if (!$golfer) {
            echo "<p>Golfer not found.</p>";
            echo "<a href='golfer_statistics.php' class='back-link'>Back to Golfer Statistics</a>";
            exit();
        }

        $golferName = htmlspecialchars($golfer['strFirstName'] . ' ' . $golfer['strLastName']);

        // Fetch donors
        $donorsQuery = "
            SELECT 
                TS.strFirstName, 
                TS.strLastName, 
                TEGS.decPledgePerHole * 9 AS TotalDonation
            FROM TEventGolferSponsors as TEGS
            JOIN TSponsors as TS ON TEGS.intSponsorID = TS.intSponsorID
            WHERE TEGS.intEventGolferID = $eventGolferID
        ";
        $donorsResult = mysqli_query($conn, $donorsQuery);
        ?>

        <h2>Donors for <?php echo $golferName; ?></h2>

        <?php if (mysqli_num_rows($donorsResult) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Total Donation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($donor = mysqli_fetch_assoc($donorsResult)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($donor['strFirstName']); ?></td>
                            <td><?php echo htmlspecialchars($donor['strLastName']); ?></td>
                            <td>$<?php echo number_format($donor['TotalDonation'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No donors found for this golfer.</p>
        <?php endif; ?>

        <?php mysqli_close($conn); ?>

        <a href="golfer_statistics.php" class="back-link">Back to Golfer Statistics</a>
    </div>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>

</html>
