<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register Golfer</title>
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
            width: 40%;
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

        input:required:invalid,
        input:focus:invalid {
            border: 2px solid red;
            background-color: #fdd;
        }

        input:valid {
            border: 2px solid green;
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

        .message {
            margin: 20px auto;
            padding: 10px;
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
            border-radius: 5px;
            max-width: 380px;
            text-align: center;
        }
    </style>
</head>

<body>
    <header>
        <h1>Register Golfer</h1>
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

        // Initialize message variable
        $strSuccessMessage = "";

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $txtFirstName = $_POST["txtFirstName"];
            $txtLastName = $_POST["txtLastName"];
            $txtAddress = $_POST["txtAddress"];
            $txtCity = $_POST["txtCity"];
            $intStateID = $_POST["cmbState"];
            $txtZipCode = $_POST["txtZipCode"];
            $txtPhoneNumber = $_POST["txtPhoneNumber"];
            $txtEmail = $_POST["txtEmail"];
            $intShirtSizeID = $_POST["cmbShirtSize"];
            $intGenderID = $_POST["cmbGender"];

            // Insert golfer into TGolfers
            $strQueryGolfer = "
                INSERT INTO TGolfers (
                    strFirstName, strLastName, strAddress, strCity, intStateID,
                    strZipCode, strPhoneNumber, strEmail, intShirtSizeID, intGenderID
                ) VALUES (
                    '$txtFirstName', '$txtLastName', '$txtAddress', '$txtCity', $intStateID,
                    '$txtZipCode', '$txtPhoneNumber', '$txtEmail', $intShirtSizeID, $intGenderID
                )";

            if (mysqli_query($conn, $strQueryGolfer)) {
                // Get the inserted golfer's ID
                $intNewGolferID = mysqli_insert_id($conn);

                // Insert record into TEventGolfers to link golfer to current event
                $intCurrentEventID = 3; // Assuming 2024 event
                $strQueryEventGolfer = "
                    INSERT INTO TEventGolfers (intEventID, intGolferID)
                    VALUES ($intCurrentEventID, $intNewGolferID)";

                if (mysqli_query($conn, $strQueryEventGolfer)) {
                    $strSuccessMessage = "Golfer registered successfully for the 2024 event!";
                } else {
                    echo "<p>Error linking golfer to event: " . mysqli_error($conn) . "</p>";
                }
            } else {
                echo "<p>Error adding golfer: " . mysqli_error($conn) . "</p>";
            }
        }

        // Fetch dropdown options
        $rstStates = mysqli_query($conn, "SELECT intStateID, strState FROM TStates");
        $rstShirtSizes = mysqli_query($conn, "SELECT intShirtSizeID, strShirtSize FROM TShirtSizes");
        $rstGenders = mysqli_query($conn, "SELECT intGenderID, strGender FROM TGenders");
        ?>

        <!-- Display success message -->
        <?php if ($strSuccessMessage): ?>
            <div class="message"><?php echo $strSuccessMessage; ?></div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="post" action="register_golfer.php">
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
                <option value="">Select State</option>
                <?php while ($udtState = mysqli_fetch_assoc($rstStates)): ?>
                    <option value="<?php echo $udtState['intStateID']; ?>"><?php echo $udtState['strState']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="txtZipCode">Zip Code:</label>
            <input id="txtZipCode" name="txtZipCode" type="text" required>

            <label for="txtPhoneNumber">Phone:</label>
            <input id="txtPhoneNumber" name="txtPhoneNumber" type="tel" pattern="[\(]\d{3}[\)]\s\d{3}[\-]\d{4}" required placeholder="(555) 555-5555">

            <label for="txtEmail">Email:</label>
            <input id="txtEmail" name="txtEmail" type="email" required>

            <label for="cmbShirtSize">Shirt Size:</label>
            <select id="cmbShirtSize" name="cmbShirtSize" required>
                <option value="">Select Shirt Size</option>
                <?php while ($udtShirtSize = mysqli_fetch_assoc($rstShirtSizes)): ?>
                    <option value="<?php echo $udtShirtSize['intShirtSizeID']; ?>"><?php echo $udtShirtSize['strShirtSize']; ?></option>
                <?php endwhile; ?>
            </select>

            <label for="cmbGender">Gender:</label>
            <select id="cmbGender" name="cmbGender" required>
                <option value="">Select Gender</option>
                <?php while ($udtGender = mysqli_fetch_assoc($rstGenders)): ?>
                    <option value="<?php echo $udtGender['intGenderID']; ?>"><?php echo $udtGender['strGender']; ?></option>
                <?php endwhile; ?>
            </select>

            <div class="button-container">
                <button type="submit">Register</button>
                <button type="reset">Clear</button>
                <a href="GolfathonHome.php" style="text-decoration: none;">
                    <button type="button">Back to Home</button>
                </a>
            </div>
        </form>
    </div>

    <?php mysqli_close($conn); ?>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>

</html>
