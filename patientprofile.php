<?php
session_start();
include('patientheader2.php');
// Add authentication check
if (!isset($_SESSION['id'])) {
    header("Location: patientlog.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookMyDoc - Admin Dashboard</title>
   
    <style>
         .dashboard-content {
            margin-top: 80px;
            padding: 2rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>
        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <h1><b>Welcome, you are logged in as Patient<b></h1>
            <br>
            <h3><b>Book Your Appointments Now </b></h3>
            <!-- Add your dashboard widgets and content here -->
        </div>
    

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const userIcon = document.getElementById("user-icon");
            const dropdown = document.getElementById("user-dropdown");

            userIcon.addEventListener("click", function (event) {
                dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
                event.stopPropagation();
            });

            document.addEventListener("click", function (event) {
                if (!userIcon.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>