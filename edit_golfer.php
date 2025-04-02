<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Golfer</title>
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

        .error {
            margin: 20px auto;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            max-width: 380px;
            text-align: center;
        }
    </style>
</head>

<body>
    <header>
        <h1>Edit Golfer</h1>
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
            die("<p class='error'>Connection failed: " . mysqli_connect_error() . "</p>");
        }

        // Check if intGolferID is provided
        if (!isset($_GET['intGolferID']) || empty($_GET['intGolferID'])) {
            echo "<p class='error'>Invalid request. Golfer ID is required.</p>";
            echo "<div class='button-container'><a href='view_golfers.php'><button type='button'>Back to View Golfers</button></a></div>";
            exit();
        }

        $intGolferID = intval($_GET['intGolferID']);
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

            // Update query
            $strUpdateQuery = "
                UPDATE TGolfers 
                SET 
                    strFirstName = '$txtFirstName',
                    strLastName = '$txtLastName',
                    strAddress = '$txtAddress',
                    strCity = '$txtCity',
                    intStateID = $intStateID,
                    strZipCode = '$txtZipCode',
                    strPhoneNumber = '$txtPhoneNumber',
                    strEmail = '$txtEmail',
                    intShirtSizeID = $intShirtSizeID,
                    intGenderID = $intGenderID
                WHERE 
                    intGolferID = $intGolferID
            ";

            if (mysqli_query($conn, $strUpdateQuery)) {
                $strSuccessMessage = "Golfer information updated successfully!";
            } else {
                echo "<p class='error'>Error updating golfer: " . mysqli_error($conn) . "</p>";
            }
        }

        // Fetch golfer details
        $strGolferQuery = "
            SELECT 
                TG.*, 
                TS.strState, 
                TSS.strShirtSize, 
                TGD.strGender 
            FROM TGolfers TG
            JOIN TStates TS ON TG.intStateID = TS.intStateID
            JOIN TShirtSizes TSS ON TG.intShirtSizeID = TSS.intShirtSizeID
            JOIN TGenders TGD ON TG.intGenderID = TGD.intGenderID
            WHERE TG.intGolferID = $intGolferID
        ";
        $rstGolfer = mysqli_query($conn, $strGolferQuery);

        if ($rstGolfer && mysqli_num_rows($rstGolfer) > 0) {
            $udtGolfer = mysqli_fetch_assoc($rstGolfer);
        } else {
            echo "<p class='error'>Golfer not found.</p>";
            echo "<div class='button-container'><a href='view_golfers.php'><button type='button'>Back to View Golfers</button></a></div>";
            exit();
        }

        // Fetch dropdown options
        $rstStates = mysqli_query($conn, "SELECT intStateID, strState FROM TStates");
        $rstShirtSizes = mysqli_query($conn, "SELECT intShirtSizeID, strShirtSize FROM TShirtSizes");
        $rstGenders = mysqli_query($conn, "SELECT intGenderID, strGender FROM TGenders");
        ?>

        <!-- Success Message -->
        <?php if ($strSuccessMessage): ?>
            <div class="message"><?php echo $strSuccessMessage; ?></div>
        <?php endif; ?>

        <!-- Edit Form -->
        <form method="post" action="edit_golfer.php?intGolferID=<?php echo $intGolferID; ?>">
            <label for="txtFirstName">First Name:</label>
            <input id="txtFirstName" name="txtFirstName" type="text" value="<?php echo $udtGolfer['strFirstName']; ?>" required>

            <label for="txtLastName">Last Name:</label>
            <input id="txtLastName" name="txtLastName" type="text" value="<?php echo $udtGolfer['strLastName']; ?>" required>

            <label for="txtAddress">Address:</label>
            <input id="txtAddress" name="txtAddress" type="text" value="<?php echo $udtGolfer['strAddress']; ?>" required>

            <label for="txtCity">City:</label>
            <input id="txtCity" name="txtCity" type="text" value="<?php echo $udtGolfer['strCity']; ?>" required>

            <label for="cmbState">State:</label>
            <select id="cmbState" name="cmbState" required>
                <?php while ($udtState = mysqli_fetch_assoc($rstStates)): ?>
                    <option value="<?php echo $udtState['intStateID']; ?>" <?php echo $udtGolfer['intStateID'] == $udtState['intStateID'] ? 'selected' : ''; ?>>
                        <?php echo $udtState['strState']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="txtZipCode">Zip Code:</label>
            <input id="txtZipCode" name="txtZipCode" type="text" value="<?php echo $udtGolfer['strZipCode']; ?>" required>

            <label for="txtPhoneNumber">Phone:</label>
            <input id="txtPhoneNumber" name="txtPhoneNumber" type="tel" value="<?php echo $udtGolfer['strPhoneNumber']; ?>" required pattern="[\(]\d{3}[\)]\s\d{3}[\-]\d{4}" placeholder="(555) 555-5555">

            <label for="txtEmail">Email:</label>
            <input id="txtEmail" name="txtEmail" type="email" value="<?php echo $udtGolfer['strEmail']; ?>" required>

            <label for="cmbShirtSize">Shirt Size:</label>
            <select id="cmbShirtSize" name="cmbShirtSize" required>
                <?php while ($udtShirtSize = mysqli_fetch_assoc($rstShirtSizes)): ?>
                    <option value="<?php echo $udtShirtSize['intShirtSizeID']; ?>" <?php echo $udtGolfer['intShirtSizeID'] == $udtShirtSize['intShirtSizeID'] ? 'selected' : ''; ?>>
                        <?php echo $udtShirtSize['strShirtSize']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="cmbGender">Gender:</label>
            <select id="cmbGender" name="cmbGender" required>
                <?php while ($udtGender = mysqli_fetch_assoc($rstGenders)): ?>
                    <option value="<?php echo $udtGender['intGenderID']; ?>" <?php echo $udtGolfer['intGenderID'] == $udtGender['intGenderID'] ? 'selected' : ''; ?>>
                        <?php echo $udtGender['strGender']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <div class="button-container">
                <button type="submit">Update</button>
                <a href="view_golfers.php" style="text-decoration: none;">
                    <button type="button">Back To View Golfers</button>
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
