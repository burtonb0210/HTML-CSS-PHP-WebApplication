<!DOCTYPE html>
<html lang="en">

<head>
    <title>Golfathon Home</title>
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

        nav a {
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            margin: 5px;
            background-color: #333;
            display: inline-block;
            border-radius: 5px;
        }

        nav a:hover {
            background-color: #575757;
        }

        .content-section {
            margin: 20px auto;
            width: 80%;
            padding: 20px;
            background-color: #ffffff;
            border: 2px solid #a2d5a2;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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
        <h1>Welcome to the Golfathon Event</h1>
    </header>

    <nav>
        <a href="register_golfer.php">Register to Golf</a>
        <a href="view_golfers.php">The Golfers</a>
        <a href="make_donation.php">Make a Donation</a>
        <a href="golfer_statistics.php">Golfer Statistics</a>
        <a href="admin_login.php">Administration Login</a>
    </nav>

    <h2>Event Highlights</h2>

    <?php
    // Database connection
    $strServerName = "mc-itddb-12-e-1";
    $strUsername = "bwburton";
    $strPassword = "0733033";
    $strDBName = "01WAPP1400BurtonB";

    $conn = mysqli_connect($strServerName, $strUsername, $strPassword, $strDBName);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Retrieve the most recent event year
    $strEventYearQuery = "SELECT MAX(dtmEventYear) AS CurrentYear FROM TEvents";
    $rstEventYear = mysqli_query($conn, $strEventYearQuery);
    $dtmCurrentYear = mysqli_fetch_assoc($rstEventYear)['CurrentYear'] ?? "Unknown";

    // Retrieve the total amount raised (adjusted for 9 holes)
    $strTotalRaisedQuery = "
        SELECT 
            SUM(decPledgePerHole * 9) AS TotalRaised 
        FROM 
            TEventGolferSponsors 
        WHERE 
            intPaymentStatusID = 1";
    $rstTotalRaised = mysqli_query($conn, $strTotalRaisedQuery);
    $dblTotalRaised = mysqli_fetch_assoc($rstTotalRaised)['TotalRaised'] ?? 0;

    // Retrieve the top fundraiser (adjusted for 9 holes)
    $strTopFundraiserQuery = "
        SELECT 
            TG.strFirstName, 
            TG.strLastName, 
            SUM(TEGS.decPledgePerHole * 9) AS TotalPledged
        FROM 
            TGolfers AS TG
        JOIN 
            TEventGolfers AS TEG ON TG.intGolferID = TEG.intGolferID
        JOIN 
            TEventGolferSponsors AS TEGS ON TEG.intEventGolferID = TEGS.intEventGolferID
        WHERE 
            TEGS.intPaymentStatusID = 1
        GROUP BY 
            TG.intGolferID
        ORDER BY 
            TotalPledged DESC
        LIMIT 1";
    $rstTopFundraiser = mysqli_query($conn, $strTopFundraiserQuery);
    $udtTopFundraiser = mysqli_fetch_assoc($rstTopFundraiser);
    $strTopFundraiserName = $udtTopFundraiser 
        ? $udtTopFundraiser['strFirstName'] . ' ' . $udtTopFundraiser['strLastName'] 
        : "None";
    $dblTopFundraiserAmount = $udtTopFundraiser['TotalPledged'] ?? 0;

    // Retrieve the total number of golfers
    $strTotalGolfersQuery = "SELECT COUNT(DISTINCT intGolferID) AS TotalGolfers FROM TGolfers";
    $rstTotalGolfers = mysqli_query($conn, $strTotalGolfersQuery);
    $intTotalGolfers = mysqli_fetch_assoc($rstTotalGolfers)['TotalGolfers'] ?? 0;

    // Fetch latest donation
    $strLatestDonationQuery = "
        SELECT 
            TS.strFirstName AS DonorFirstName, 
            TS.strLastName AS DonorLastName, 
            TG.strFirstName AS GolferFirstName, 
            TG.strLastName AS GolferLastName, 
            TEGS.decPledgePerHole
        FROM 
            TEventGolferSponsors AS TEGS
        JOIN 
            TSponsors AS TS ON TEGS.intSponsorID = TS.intSponsorID
        JOIN 
            TEventGolfers AS TEG ON TEGS.intEventGolferID = TEG.intEventGolferID
        JOIN 
            TGolfers AS TG ON TEG.intGolferID = TG.intGolferID
        ORDER BY 
            TEGS.intEventGolferSponsorID DESC
        LIMIT 1";
    $rstLatestDonation = mysqli_query($conn, $strLatestDonationQuery);
    $udtLatestDonation = mysqli_fetch_assoc($rstLatestDonation);

    $strLatestDonorName = $udtLatestDonation 
        ? htmlspecialchars($udtLatestDonation['DonorFirstName'] . ' ' . $udtLatestDonation['DonorLastName']) 
        : "None";
    $strLatestGolferName = $udtLatestDonation 
        ? htmlspecialchars($udtLatestDonation['GolferFirstName'] . ' ' . $udtLatestDonation['GolferLastName']) 
        : "None";
    $dblLatestDonationAmount = $udtLatestDonation 
        ? number_format($udtLatestDonation['decPledgePerHole'], 2) 
        : "0.00";

    mysqli_close($conn);
    ?>

    <div class="content-section">
        <h3>⛳ Total Raised So Far</h3>
        <p><strong>$<?php echo number_format($dblTotalRaised, 2); ?></strong></p>
    </div>

    <div class="content-section">
        <h3>✨ Latest Donation</h3>
        <?php if ($udtLatestDonation): ?>
            <p>Donor: <strong><?php echo $strLatestDonorName; ?></strong></p>
            <p>Golfer: <strong><?php echo $strLatestGolferName; ?></strong></p>
            <p>Pledge Amount: <strong>$<?php echo $dblLatestDonationAmount; ?> per hole</strong></p>
        <?php else: ?>
            <p>No recent donations available.</p>
        <?php endif; ?>
    </div>

    <div class="content-section">
        <h3>🏆 Top Fundraiser</h3>
        <p>Name: <strong><?php echo $strTopFundraiserName; ?></strong></p>
        <p>Amount Raised: <strong>$<?php echo number_format($dblTopFundraiserAmount, 2); ?></strong></p>
    </div>

    <div class="content-section">
        <h3>🏌️ Total Number of Golfers</h3>
        <p><strong><?php echo $intTotalGolfers; ?></strong></p>
    </div>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>

</html>
