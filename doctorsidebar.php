<!-- sidebar.php -->
<div class="sidebar">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="appointments.php">Appointments</a></li>
        <li><a href="patients.php">Patients</a></li>
        <li><a href="settings.php">Settings</a></li>
    </ul>
</div>

<style>
    .sidebar {
        width: 250px;
        height: calc(100vh - 50px); /* Reduced height to accommodate the logo in the header */
        position: fixed;
        top: 70px; /* Increased to match the height of the header */
        left: 0;
        background: #343a40;
        color: white;
        padding-top: 15px;
        overflow-y: auto; /* Allows scrolling if content is too long */
    }
    .sidebar ul {
        list-style: none;
        padding: 0;
    }
    .sidebar ul li {
        padding: 12px;
    }
    .sidebar ul li a {
        color: white;
        text-decoration: none;
        display: block;
        transition: all 0.3s ease-in-out;
    }
    .sidebar ul li a:hover {
        background: #495057;
        padding-left: 12px;
    }
</style>
