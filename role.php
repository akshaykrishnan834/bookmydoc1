<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Role - BookMyDoc</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0077b6, #1e90ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
            position: relative; /* Allow absolute positioning relative to body */
        }

        /* Form Container */
        .form-box {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 400px;
            max-width: 90%;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
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

        /* Admin Button (Positioned at top left) */
        .admin-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #0077b6;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 100;
        }

        .admin-btn img {
            width: 25px;
            height: 25px;
            margin-bottom: 5px;
        }

        .admin-btn:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }

        /* Role Buttons (Stacked) */
        .role-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .role-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 15px;
            width: 80%;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.3s ease;
            color: white;
            text-align: center;
        }

        .role-btn img {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }

        .doctor-btn { background-color: #28a745; }
        .patient-btn { background-color: #dc3545; }

        .role-btn:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }

        /* Back to Home Button */
        .back-to-home {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .back-to-home a {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
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

<!-- Admin button positioned at the top left of the page -->
<button class="admin-btn" onclick="location.href='adminlog.php?role=admin'">
    <img src="images/admin.png" alt="Admin Icon">
</button>

<div class="form-box">
    <div class="logo-container">
        <img src="images/logo.png" alt="Logo" class="logo">
        <div>
            <h2>BookMyDoc</h2>
            <h5>Online Doctor Appointment Booking</h5>
        </div>
    </div>

    <h3>Select Your Role</h3>

    <div class="role-container">
        <button class="role-btn doctor-btn" onclick="location.href='doctorlog.php?role=doctor'">
            <img src="images/doctor.png" alt="Doctor Icon">
            Doctor
        </button>
        <button class="role-btn patient-btn" onclick="location.href='patientlog.php?role=patient'">
            <img src="images/patient.png" alt="Patient Icon">
            Patient
        </button>
    </div>

    <div class="back-to-home">
        <a href="home.php">
            <img src="images/home_738822.png" alt="Home" class="home-icon"> Back to Home
        </a>
    </div>
</div>

</body>
</html>