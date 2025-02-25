<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form with Validation</title>
    <style>
        /* General Styles */
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0077b6, #1e90ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        /* Form Container */
        .form-box {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 450px;
            max-width: 90%;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo Section */
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .logo-container h2 {
            margin: 0;
            font-size: 1.8rem;
            color: #0077b6;
        }

        .logo-container h5 {
            font-size: 1rem;
            color: #666;
            margin-top: 5px;
        }

        h3 {
            color: #444;
            margin-bottom: 20px;
        }

        /* Floating Label Form Group */
        .form-group {
            position: relative;
            margin-bottom: 15px;
            text-align: left;
        }

        input {
            display: block;
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
            margin: 0 auto;
        }

        input:focus {
            border-color: #0077b6;
            box-shadow: 0 0 8px rgba(0, 119, 182, 0.5);
        }

        label {
            position: absolute;
            top: 10px;
            left: 10px;
            color: #777;
            font-size: 1rem;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        input:focus + label,
        input:not(:placeholder-shown) + label {
            top: -15px;
            left: 5px;
            font-size: 0.9rem;
            color: rgb(0, 0, 0);
        }

        .error {
            color: #ff4d4f;
            font-size: 0.9em;
        }

        /* Buttons */
        button {
            width: 100%;
            padding: 12px;
            background-color: #0077b6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background-color: #005f99;
        }

        .already-account {
            margin-top: 15px;
            font-size: 0.9em;
            text-align: center;
        }

        .already-account a {
            color: #0077b6;
            text-decoration: none;
        }

        .already-account a:hover {
            text-decoration: underline;
        }

        /* Google Sign Up Button */
        .google-btn {
            width: 100%;
            padding: 12px;
            background-color: #db4437;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
        }

        .google-btn img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .google-btn:hover {
            background-color: #c1351d;
        }
        .back-to-home a {
            display: inline-flex;
            align-items: center;
            padding: 15px 9px;
            font-size: 1rem;
            font-weight: bold;
            color: white;
            background: linear-gradient(135deg, #0077b6, #1e90ff);
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .back-to-home a:hover {
            background: linear-gradient(135deg, #005f8d, #1565c0);
            transform: scale(1.05);
        }

        .home-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <div class="logo-container">
            <img src="images/logo.png" alt="Logo" class="logo">
            <div>
                <h2>BookMyDoc</h2>
                <h5>Online Doctor Appointment Booking</h5>
            </div>
        </div>

        <h3>Create Your Account As Doctor</h3>

        <form method="post" action="adminconnect.php">
           

           

            <div class="form-group">
                <input type="email" id="email" name="email" placeholder=" " required>
                <label for="email"> &nbsp Email:</label>
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password" placeholder=" " required>
                <label for="password"> &nbsp Password:</label>
            </div>

            
            <button type="submit">Create Account</button>

            <div class="already-account">
                <p>Already have an account? <a href="doctorlog.php">Log in</a></p>
            </div>
            <div class="back-to-home">
        <a href="home.php">
            &nbsp<img src="images/home_738822.png" alt="Home" class="home-icon"></a>
        <a href="role.php">
            &nbsp<img src="images/staff.png" alt="Home" class="home-icon"></a>
    </div>
        </form>
    </div>
</body>
</html>