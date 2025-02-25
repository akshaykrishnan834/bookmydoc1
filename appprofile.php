<?php 
session_start(); 
include('db_connection.php'); 

// Fetch pending doctor requests 
$query = "SELECT * FROM doctorreg WHERE status = 'Pending'"; 
$result = mysqli_query($conn, $query); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pending Doctor Approvals</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0077b6;
            --secondary-color: #00b4d8;
            --bg-color: #f4f7f6;
            --text-color: #333;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: var(--bg-color);
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 2rem auto;
            background: var(--white);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .header {
            background: var(--primary-color);
            color: var(--white);
            padding: 1rem;
            text-align: center;
        }

        .pending-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .pending-table th {
            background: var(--secondary-color);
            color: var(--white);
            padding: 1rem;
            text-align: left;
        }

        .pending-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .action-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: var(--white);
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .action-btn:hover {
            background: #005b8c;
        }

        .certificate-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
        }

        .certificate-link:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #666;
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
            <a href="admin-dashboard.php" class="menu-item active">
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
            <h1 class="header-title">Dashboard</h1>
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

    
 <br>
 <br>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="pending-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Experience</th>
                        <th>Specialization</th>
                        <th>Qualification</th>
                        <th>Degree Certificate</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['experience']); ?> years</td>
                            <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                            <td><?php echo htmlspecialchars($row['qualifications']); ?></td>
                            <td>
                                <a href="uploads/<?php echo htmlspecialchars($row['degree_certificate']); ?>" 
                                   class="certificate-link" 
                                   target="_blank">
                                    View Certificate
                                </a>
                            </td>
                            <td>
                                <a href="approvedoct.php?id=<?php echo $row['id']; ?>" 
                                   class="action-btn" 
                                   onclick="return confirm('Approve this doctor?')">
                                    Approve
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--primary-color);"></i>
                <p>No pending doctor approvals at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
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
</html>