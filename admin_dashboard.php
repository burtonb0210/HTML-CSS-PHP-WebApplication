<!DOCTYPE html>
<html lang="en">

<head>
    <title>Administrator Dashboard</title>
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

        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        a {
            text-decoration: none;
        }

        button {
            background-color: #006400;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
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
        <h1>Administrator Dashboard</h1>
    </header>

    <div class="content-section">
        <p>Welcome to the admin dashboard! Use the navigation links to manage the event.</p>
        <div class="button-container">
            <a href="add_event.php">
                <button>Add an Event</button>
            </a>
            <a href="manage_golfers.php">
                <button>Manage Event Golfers</button>
            </a>
            <a href="GolfathonHome.php">
                <button>Back to Home</button>
            </a>
        </div>
    </div>

    <footer>
        <p>Keep swinging! &copy; 2024 Golfathon Event</p>
    </footer>
</body>

</html>
