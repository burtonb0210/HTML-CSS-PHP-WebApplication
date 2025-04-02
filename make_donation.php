<!DOCTYPE html>
<html lang="en">

<head>
    <title>Make a Donation</title>
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

        input,
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #a2d5a2;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .button-container {
            text-align: center;
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

        input:required:invalid,
        select:required:invalid {
            border-color: red;
        }

        input:valid,
        select:valid {
            border-color: green;
        }
    </style>
</head>

<body>
    <header>
        <h1>Make a Donation</h1>
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
            die("<p class='error-message'>Connection failed: " . mysqli_connect_error() . "</p>");
        }

        // Fetch golfers for the current event
        $intCurrentEventID = 3; // Hardcoded for the 2024 event
        $strGolfersQuery = "
            SELECT 
                TEG.intEventGolferID, 
                CONCAT(TG.strFirstName, ' ', TG.strLastName) AS strGolferName 
            FROM 
                TEventGolfers AS TEG
            JOIN 
                TGolfers AS TG ON TEG.intGolferID = TG.intGolferID
            WHERE 
                TEG.intEventID = $intCurrentEventID
            ORDER BY 
                TG.strLastName, TG.strFirstName;
        ";
        $rstGolfers = mysqli_query($conn, $strGolfersQuery);

        // Fetch states
        $strStatesQuery = "SELECT intStateID, strState FROM TStates ORDER BY strState;";
        $rstStates = mysqli_query($conn, $strStatesQuery);

        // Fetch payment types
        $strPaymentTypesQuery = "SELECT intPaymentTypeID, strPaymentType FROM TPaymentTypes;";
        $rstPaymentTypes = mysqli_query($conn, $strPaymentTypesQuery);

        if (!$rstGolfers || !$rstStates || !$rstPaymentTypes) {
            die("<p class='error-message'>Error fetching data from the database.</p>");
        }
        ?>

        <form method="post" action="process_donation.php">
            <h2>Donor Information</h2>
            <label for="txtFirstName">First Name:</label>
            <input id="txtFirstName" name="txtFirstName" type="text" required>

            <label for="txtLastName">Last Name:</label>
            <input id="txtLastName" name="txtLastName" type="text" required>

            <label for="txtAddress">Address:</label>
            <input id="txtAddress" name="txtAddress" type="text" required>

            <label for="txtCity">City:</label>
            <input id="txtCity" name="txtCity" type="text" required>

            <label for="cmbState">State:</label>
            <select id="cmbState" name="cmbState" required>
                <option value="" disabled selected>Select your state</option>
                <?php while ($udtState = mysqli_fetch_assoc($rstStates)): ?>
                    <option value="<?php echo $udtState['intStateID']; ?>"><?php echo $udtState['strState']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="txtZipCode">ZIP Code:</label>
            <input id="txtZipCode" name="txtZipCode" type="text" pattern="\d{5}(-\d{4})?" title="Enter a valid ZIP code" required>

            <label for="txtPhoneNumber">Phone Number:</label>
            <input 
                id="txtPhoneNumber" 
                name="txtPhoneNumber" 
                type="tel" 
                pattern="[\(]\d{3}[\)]\s\d{3}[\-]\d{4}" 
                title="Phone number must be in the format (555) 555-5555" 
                placeholder="(555) 555-5555" 
                required>

            <label for="txtEmail">Email:</label>
            <input id="txtEmail" name="txtEmail" type="email" placeholder="example@example.com" required>

            <h2>Donation Information</h2>
            <label for="cmbGolfer">Golfer:</label>
            <select id="cmbGolfer" name="cmbGolfer" required>
                <option value="" disabled selected>Select a golfer</option>
                <?php while ($udtGolfer = mysqli_fetch_assoc($rstGolfers)): ?>
                    <option value="<?php echo $udtGolfer['intEventGolferID']; ?>"><?php echo $udtGolfer['strGolferName']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="txtPledge">Pledge Amount:</label>
            <input id="txtPledge" name="txtPledge" type="number" step="0.01" min="0" placeholder="Enter amount per hole played (nine holes per event)" required>

            <label for="cmbPaymentType">Payment Type:</label>
            <select id="cmbPaymentType" name="cmbPaymentType" required>
                <option value="" disabled selected>Select payment type</option>
                <?php while ($udtPaymentType = mysqli_fetch_assoc($rstPaymentTypes)): ?>
                    <option value="<?php echo $udtPaymentType['intPaymentTypeID']; ?>"><?php echo $udtPaymentType['strPaymentType']; ?></option>
                <?php endwhile; ?>
            </select>

            <div class="button-container">
                <button type="submit">Submit Donation</button>
                <button type="reset">Clear Form</button>
                <a href="GolfathonHome.php">
                    <button type="button">Back to Home</button>
                </a>
            </div>
        </form>

        <?php mysqli_close($conn); ?>
    </div>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>

</html>
