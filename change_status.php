<!DOCTYPE html>
<html lang="en">
<head>
    <title>Change Payment Status</title>
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

        footer {
            margin-top: 20px;
            background-color: #333;
            color: white;
            padding: 10px;
        }

        select, button {
            padding: 10px;
            margin: 10px 0;
            font-size: 1rem;
        }

        button {
            background-color: #006400;
            color: white;
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

        .success-message {
            color: green;
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            margin-top: 10px;
            color: #006400;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Change Payment Status</h1>
    </header>

    <div class="content-section">
        <?php
        // Validate query string parameters
        if (isset($_GET['intSponsorID'], $_GET['intEventGolferID']) && !empty($_GET['intSponsorID']) && !empty($_GET['intEventGolferID'])) {
            $intSponsorID = intval($_GET['intSponsorID']);
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

            // Fetch current payment statuses
            $strStatusesQuery = "
                SELECT 
                    intPaymentStatusID, 
                    strPaymentStatus 
                FROM 
                    TPaymentStatuses;
            ";
            $rstStatuses = mysqli_query($conn, $strStatusesQuery);

            if (!$rstStatuses || mysqli_num_rows($rstStatuses) === 0) {
                echo "<p class='error-message'>Error fetching payment statuses.</p>";
                mysqli_close($conn);
                exit();
            }

            // Update payment status if submitted
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['intNewPaymentStatus'])) {
                $intNewPaymentStatusID = intval($_POST['intNewPaymentStatus']);

                $strUpdateQuery = "
                    UPDATE 
                        TEventGolferSponsors
                    SET 
                        intPaymentStatusID = $intNewPaymentStatusID
                    WHERE 
                        intSponsorID = $intSponsorID 
                        AND intEventGolferID = $intEventGolferID;
                ";

                if (mysqli_query($conn, $strUpdateQuery)) {
                    echo "<p class='success-message'>Payment status updated successfully!</p>";
                } else {
                    echo "<p class='error-message'>Error updating payment status: " . mysqli_error($conn) . "</p>";
                }
            }

            // Display dropdown for payment status change
            echo "<form method='POST' action='change_status.php?intSponsorID=$intSponsorID&intEventGolferID=$intEventGolferID'>";
            echo "<label for='cmbPaymentStatus'>Select New Payment Status:</label><br>";
            echo "<select name='intNewPaymentStatus' id='cmbPaymentStatus'>";

            while ($udtStatus = mysqli_fetch_assoc($rstStatuses)) {
                echo "<option value='" . intval($udtStatus['intPaymentStatusID']) . "'>" . htmlspecialchars($udtStatus['strPaymentStatus']) . "</option>";
            }

            echo "</select><br>";
            echo "<button type='submit'>Apply</button>";
            echo "</form>";

            mysqli_close($conn);
        } else {
            echo "<p class='error-message'>Invalid request. Missing parameters.</p>";
        }
        ?>

        <a href="admin_dashboard.php" class="back-link">Go Back to Admin Dashboard</a>
    </div>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>
</html>
