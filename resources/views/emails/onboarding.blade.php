<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Zintoop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c5e1a;
            text-align: center;
        }
        p {
            color: #333333;
            line-height: 1.6;
        }
        .step {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background-color: #fafafa;
        }
        .step h3 {
            color: #1a4010;
            margin-top: 0;
        }
        .screenshot-placeholder {
            width: 100%;
            height: 200px;
            background-color: #e9ecef;
            border: 2px dashed #adb5bd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-weight: bold;
            border-radius: 4px;
            margin-top: 15px;
        }
        .btn {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 12px 20px;
            background-color: #2c5e1a;
            color: #ffffff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Zintoop! 🫒</h1>
        <p>Thank you for subscribing to our updates. Zintoop is the first Tunisian marketplace dedicated to olive oil professionals.</p>
        <p>To help you get started, we have created this quick visual guide.</p>

        <div class="step">
            <h3>1. How to Register</h3>
            <p>Creating an account is free and takes less than a minute. Click on the 'Login/Register' button at the top right of the screen.</p>
            <!-- REPLACE THIS DIV WITH AN ACTUAL <img src="..."> TAG ONCE YOU HAVE THE SCREENSHOT -->
            <div class="screenshot-placeholder">
                [Screenshot: Arrow pointing to the 'Login/Register' button]
            </div>
        </div>

        <div class="step">
            <h3>2. How to Submit a Deal</h3>
            <p>Once registered, go to the Marketplace. Click the 'Post a Listing' button, fill in your offer or request details, and publish it to the community.</p>
            <!-- REPLACE THIS DIV WITH AN ACTUAL <img src="..."> TAG ONCE YOU HAVE THE SCREENSHOT -->
            <div class="screenshot-placeholder">
                [Screenshot: Arrow pointing to 'Post a Listing' form]
            </div>
        </div>

        <div class="step">
            <h3>3. How to Track Transporters</h3>
            <p>If you are a mill or farmer, you can track your shipments in real-time. Go to your Dashboard -> 'Active Trips' to see the live map.</p>
            <!-- REPLACE THIS DIV WITH AN ACTUAL <img src="..."> TAG ONCE YOU HAVE THE SCREENSHOT -->
            <div class="screenshot-placeholder">
                [Screenshot: Arrow pointing to the Map/Tracking screen]
            </div>
        </div>

        <div class="step">
            <h3>4. How to Chat</h3>
            <p>Found a listing you like? Click the 'Chat' button on the listing page to instantly message the seller or buyer and negotiate prices securely.</p>
            <!-- REPLACE THIS DIV WITH AN ACTUAL <img src="..."> TAG ONCE YOU HAVE THE SCREENSHOT -->
            <div class="screenshot-placeholder">
                [Screenshot: Arrow pointing to the Chat interface]
            </div>
        </div>

        <a href="{{ url('/') }}" class="btn">Explore Zintoop Now</a>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Zintoop. All rights reserved.</p>
            <p>You received this email because you subscribed on our website.</p>
        </div>
    </div>
</body>
</html>
