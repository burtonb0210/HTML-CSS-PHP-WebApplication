<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thank You for Your Donation</title>
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
            text-align: center;
        }

        .success-message {
            color: green;
            font-weight: bold;
        }

        .button-container {
            margin-top: 20px;
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
        <h1>Thank You!</h1>
    </header>

    <div class="content-section">
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

            // Collect sponsor details
            $strFirstName = mysqli_real_escape_string($conn, $_POST['txtFirstName']);
            $strLastName = mysqli_real_escape_string($conn, $_POST['txtLastName']);
            $strAddress = mysqli_real_escape_string($conn, $_POST['txtAddress']);
            $strCity = mysqli_real_escape_string($conn, $_POST['txtCity']);
            $intStateID = intval($_POST['cmbState']);
            $strZipCode = mysqli_real_escape_string($conn, $_POST['txtZipCode']);
            $strPhoneNumber = mysqli_real_escape_string($conn, $_POST['txtPhoneNumber']);
            $strEmail = mysqli_real_escape_string($conn, $_POST['txtEmail']);

            // Collect donation details
            $intEventGolferID = intval($_POST['cmbGolfer']);
            $dblPledgeAmount = floatval($_POST['txtPledge']);
            $intPaymentTypeID = intval($_POST['cmbPaymentType']);
            $intPaymentStatusID = ($intPaymentTypeID === 3) ? 1 : 2;

            // Insert or update sponsor
            $strSponsorQuery = "
                INSERT INTO TSponsors (strFirstName, strLastName, strAddress, strCity, intStateID, strZipCode, strPhoneNumber, strEmail)
                VALUES ('$strFirstName', '$strLastName', '$strAddress', '$strCity', $intStateID, '$strZipCode', '$strPhoneNumber', '$strEmail')
                ON DUPLICATE KEY UPDATE
                strAddress = VALUES(strAddress),
                strCity = VALUES(strCity),
                intStateID = VALUES(intStateID),
                strZipCode = VALUES(strZipCode),
                strPhoneNumber = VALUES(strPhoneNumber),
                strEmail = VALUES(strEmail);
            ";

            if (!mysqli_query($conn, $strSponsorQuery)) {
                die("<p class='error-message'>Error inserting sponsor: " . mysqli_error($conn) . "</p>");
            }

            // Get the sponsor ID
            $intSponsorID = mysqli_insert_id($conn);
            if ($intSponsorID === 0) {
                $strSponsorIDQuery = "
                    SELECT intSponsorID FROM TSponsors
                    WHERE strFirstName = '$strFirstName' AND strLastName = '$strLastName' AND strEmail = '$strEmail'
                    LIMIT 1;
                ";
                $rstSponsor = mysqli_query($conn, $strSponsorIDQuery);
                $intSponsorID = mysqli_fetch_assoc($rstSponsor)['intSponsorID'];
            }

            // Insert donation
            $strDonationQuery = "
                INSERT INTO TEventGolferSponsors (intEventGolferID, intSponsorID, dtmDateOfPledge, decPledgePerHole, intPaymentTypeID, intPaymentStatusID)
                VALUES ($intEventGolferID, $intSponsorID, NOW(), $dblPledgeAmount, $intPaymentTypeID, $intPaymentStatusID);
            ";

            if (!mysqli_query($conn, $strDonationQuery)) {
                die("<p class='error-message'>Error inserting donation: " . mysqli_error($conn) . "</p>");
            }

            // Retrieve golfer's name and event year
            $strThankYouQuery = "
                SELECT 
                    CONCAT(TG.strFirstName, ' ', TG.strLastName) AS strGolferName,
                    TE.dtmEventYear AS dtmEventYear
                FROM 
                    TEventGolfers AS TEG
                JOIN TGolfers AS TG ON TEG.intGolferID = TG.intGolferID
                JOIN TEvents AS TE ON TEG.intEventID = TE.intEventID
                WHERE 
                    TEG.intEventGolferID = $intEventGolferID;
            ";
            $rstThankYou = mysqli_query($conn, $strThankYouQuery);
            $udtThankYou = mysqli_fetch_assoc($rstThankYou);

            $strGolferName = $udtThankYou['strGolferName'];
            $intEventYear = $udtThankYou['dtmEventYear'];

            echo "<p class='success-message'>Thank you <strong>$strFirstName $strLastName</strong> for supporting <strong>$strGolferName</strong> with your donation of <strong>$" . number_format($dblPledgeAmount, 2) . "</strong> per hole for Golfathon <strong>$intEventYear</strong>!</p>";

            mysqli_close($conn);
        } else {
            echo "<p class='error-message'>Invalid request. Please try again.</p>";
        }
        ?>

        <div class="button-container">
            <a href="GolfathonHome.php">
                <button type="button">Return to Home</button>
            </a>
        </div>
    </div>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>
</html>
