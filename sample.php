<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BookMyDoc - Patient Dashboard">
    <title>BookMyDoc - Patient Dashboard</title>
    <style>
        /* Root Variables */
        :root {
            --primary-color: #0077b6;
            --primary-dark: #005b8c;
            --secondary-color: #00b4d8;
            --text-color: #333333;
            --light-gray: #f8f9fa;
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--light-gray);
            color: var(--text-color);
            font-size: 16px;
        }

        /* Header Section */
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-image {
            width: 40px;
            height: 40px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .logo-subtitle {
            font-size: 0.9rem;
            font-weight: 500;
            color: #f8f8f8;
            text-align: center;
        }

        /* Logout Button (Round button with Image) */
        .logout-btn {
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            background-color: var(--primary-dark);
            cursor: pointer;
            transition: var(--transition);
        }

        .logout-btn img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        .logout-btn:hover {
            background-color: #003c6d;
        }

        /* Log Out Dropdown */
        .logout-dropdown {
            display: none;
            position: absolute;
            top: 60px;
            right: 10px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 1rem;
        }

        .logout-dropdown.active {
            display: block;
        }

        .logout-dropdown button {
            padding: 0.8rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
        }

        .logout-dropdown button:hover {
            background-color: var(--primary-dark);
        }

        /* Navigation Bar */
        .nav-bar {
            background-color: var(--primary-color);
            padding: 1rem;
            position: sticky;
            top: 50px;
            z-index: 100;
            display: flex;
            justify-content: center;
            gap: 2rem;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .nav-link:hover {
            background-color: var(--primary-dark);
        }

        .active {
            background-color: var(--primary-dark);
        }

        /* Main Section */
        .main-content {
            padding: 3rem 1.5rem;
            text-align: center;
        }

        .section {
            display: none;
            padding: 2rem;
            border-radius: var(--border-radius);
            background-color: white;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .section.active {
            display: block;
            opacity: 1;
        }

        .welcome-section h2 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .welcome-section p {
            color: #666;
            margin-bottom: 2rem;
        }

        .button {
            padding: 0.8rem 2rem;
            font-size: 1rem;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            background-color: var(--primary-color);
            color: white;
            transition: var(--transition);
        }

        .button:hover {
            background-color: var(--primary-dark);
        }

    </style>
</head>

<body>

    <!-- Header Section with Logo and Subtitle -->
    <header>
        <div class="logo-container">
            <img src="images/logo.png" alt="BookMyDoc Logo" class="logo-image">
            <div>
                <span class="logo-text">BookMyDoc</span>
                <div class="logo-subtitle">Online Doctor Appointment Booking</div>
            </div>
        </div>
        <div class="logout-btn" onclick="toggleLogoutDropdown()">
            <!-- Image inside the round button -->
            <img src="images/user.png" alt="User Avatar">
        </div>

        <!-- Log Out Dropdown -->
        <div class="logout-dropdown" id="logoutDropdown">
            <button onclick="logout()">Log Out</button>
        </div>
    </header>

    <!-- Navigation Bar -->
    <div class="nav-bar">
        <a href="#" class="nav-link active" id="homeLink" onclick="showSection('home')">Home</a>
        <a href="#" class="nav-link" id="doctorsLink" onclick="showSection('doctors')">Browse Doctors</a>
        <a href="#" class="nav-link" id="profileLink" onclick="showSection('profile')">Profile</a>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <!-- Home Section -->
        <div class="section home-section active" id="home">
            <div class="welcome-section">
                <h2>Welcome to BookMyDoc, Patient!</h2>
                <p>Here, you can find the best doctors and book appointments online.</p>
                <button class="button" onclick="showSection('doctors')">Browse Doctors</button>
            </div>
        </div>

        <!-- Browse Doctors Section -->
        <div class="section doctor-section" id="doctors">
            <h2>Find Verified Doctors</h2>
            <p>Browse through a list of healthcare professionals available for appointments.</p>
            <div class="doctor-list">
                <div class="doctor-card">
                    <h3>Dr. John Doe</h3>
                    <p>Cardiologist</p>
                    <a href="#">Book Appointment</a>
                </div>
                <div class="doctor-card">
                    <h3>Dr. Jane Smith</h3>
                    <p>Pediatrician</p>
                    <a href="#">Book Appointment</a>
                </div>
                <div class="doctor-card">
                    <h3>Dr. Sarah Brown</h3>
                    <p>Dermatologist</p>
                    <a href="#">Book Appointment</a>
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="section profile-section" id="profile">
            <h2>Update Your Profile</h2>
            <form>
                <input type="text" placeholder="Full Name">
                <input type="email" placeholder="Email">
                <input type="tel" placeholder="Phone Number">
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        // Toggle logout dropdown visibility
        function toggleLogoutDropdown() {
            const dropdown = document.getElementById('logoutDropdown');
            dropdown.classList.toggle('active');
        }

        // Logout functionality (for now just an alert)
        function logout() {
            alert("Logging out...");
            // You can add your logout functionality here
        }

        // Show and hide sections
        function showSection(section) {
            // Hide all sections
            const sections = document.querySelectorAll('.section');
            sections.forEach(function (sectionElement) {
                sectionElement.classList.remove('active');
            });

            // Show the selected section
            document.getElementById(section).classList.add('active');

            // Update active link style
            const links = document.querySelectorAll('.nav-link');
            links.forEach(function (link) {
                link.classList.remove('active');
            });
            document.getElementById(section + 'Link').classList.add('active');
        }
    </script>
</body>

</html>
