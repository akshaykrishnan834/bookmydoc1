<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: adminlog.php");
    exit();
}

// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "bookmydoc";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin details
$sql = "SELECT id, name, email, phone, password FROM patientreg";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #0077b6;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        :root {
            --primary-color: #0077b6;
            --primary-dark: #005b8c;
            --secondary-color: #00b4d8;
            --text-color: #333333;
            --light-gray: #f8f9fa;
            --border-radius: 8px;
            --transition: all 0.3s ease;
            --sidebar-width: 240px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: var(--light-gray);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .logo-image {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-title {
            color: var(--primary-color);
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo-subtitle {
            color: #666;
            font-size: 0.75rem;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .menu-item {
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .menu-item:hover {
            background: var(--light-gray);
            color: var(--primary-color);
        }

        .menu-item.active {
            background: var(--primary-color);
            color: white;
        }

        .menu-icon {
            width: 20px;
            text-align: center;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        /* Header */
        .header {
            background: white;
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 900;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .user-menu {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 50px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 150px;
            overflow: hidden;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            transition: 0.3s;
        }

        .dropdown-menu a:hover {
            background: #f1f1f1;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }
        /* Dashboard Content */
        .dashboard-content {
            margin-top: 80px;
            padding: 2rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .header {
                left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="#" class="logo-container">
                <img src="images/logo.png" alt="BookMyDoc Logo" class="logo-image">
                <div class="logo-text">
                    <span class="logo-title">BookMyDoc</span>
                    <span class="logo-subtitle">Admin Panel</span>
                </div>
            </a>
        </div>
        <nav class="sidebar-menu">
            <a href="admindashboard.php" class="menu-item active">
                <i class="fas fa-home menu-icon"></i>
                Dashboard
            </a>
            <a href="managedoctors.php" class="menu-item">
                <i class="fas fa-user-md menu-icon"></i>
                Manage Doctors
            </a>
            <a href="managepatients.php" class="menu-item">
                <i class="fas fa-users menu-icon"></i>
                Manage Patients
            </a>
            <a href="appointments.php" class="menu-item">
                <i class="fas fa-calendar-check menu-icon"></i>
                Appointments
            </a>
            <a href="specialities.php" class="menu-item">
                <i class="fas fa-stethoscope menu-icon"></i>
                Specialities
            </a>
            <a href="reports.php" class="menu-item">
                <i class="fas fa-chart-bar menu-icon"></i>
                Reports
    </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="header">
            <h1 class="header-title">Manage Patients</h1>
            <div class="header-actions">
                <div class="header-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="header-icon user-menu">
                    <i class="fas fa-user" id="user-icon"></i>
                    <div class="dropdown-menu" id="user-dropdown">
                        <a href="adminlogout.php">Logout</a>
                    </div>
                </div>
            </div>
        </header>

    <h2>Patient Login Details</h2>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Password</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['phone']}</td>
                        <td>{$row['password']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No records found</td></tr>";
        }
        $conn->close();
        ?>
    </table>

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
