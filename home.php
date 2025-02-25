<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BookMyDoc - Professional online doctor appointment booking system. Schedule appointments with verified doctors quickly and easily.">
    <title>BookMyDoc - Professional Healthcare Appointment System</title>
    
    <style>
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            background: var(--light-gray);
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Header & Navigation */
        .header {
            background: white;
            padding: 1rem 4rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .logo-image {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-title {
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo-subtitle {
            color: #666;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .btn {
            padding: 0.8rem 1.8rem;
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            border: none;
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        
        .hero {
            padding: 8rem 4rem 4rem;
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            min-height: 90vh;
            display: flex;
            align-items: center;
        }

        .hero-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4rem;
        }

        .hero-content {
            flex: 1;
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }

        .hero-description {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
            max-width: 600px;
        }

        .hero-image {
            flex: 1;
        }

        .hero-image img {
            width: 100%;
            max-width: 600px;
            border-radius: var(--border-radius);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        /* Features Section */
        .features {
            padding: 6rem 4rem;
            background: white;
        }

        .features-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            color: #666;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
        }

        .feature-card {
            padding: 2.5rem;
            border-radius: var(--border-radius);
            background: var(--light-gray);
            text-align: center;
            transition: var(--transition);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .feature-title {
            font-size: 1.4rem;
            color: var(--text-color);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .feature-description {
            color: #666;
            line-height: 1.6;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .hero-title {
                font-size: 3rem;
            }

            .features-grid {
                gap: 2rem;
            }
        }

        @media (max-width: 992px) {
            .header {
                padding: 1rem 2rem;
            }

            .hero {
                padding: 6rem 2rem 4rem;
            }

            .hero-container {
                flex-direction: column;
                text-align: center;
            }

            .hero-description {
                margin: 0 auto 2.5rem;
            }

            .features {
                padding: 4rem 2rem;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .feature-card {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <a href="#" class="logo-container">
                <img src="images/logo.png" alt="BookMyDoc Logo" class="logo-image">
                <div class="logo-text">
                    <span class="logo-title">BookMyDoc</span>
                    <span class="logo-subtitle">Online Doctor Appointment Booking</span>
                </div>
            </a>
            <nav class="nav-menu">
                
                <a href="patientlog.php" class="nav-link">Find Doctors</a>
                <button class="btn btn-outline"><a href="role.php" style="text-decoration: none;color:#333333;">Log In</a></button>
                <button class="btn btn-primary"><a href="role.php" style="text-decoration: none;color:#FFFFFF;">Register</a></button>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1 class="hero-title">Expert Healthcare at Your Fingertips</h1>
                <p class="hero-description">Connect with verified healthcare professionals and book appointments instantly. Experience healthcare that revolves around your schedule and comfort.</p>
                <a href="patientlog.php" class="btn btn-primary">Book an Appointment</a>
            </div>
            <div class="hero-image">
                <img src="images/hero-bg.jpg" alt="Professional healthcare consultation">
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="features-container">
            <div class="section-header">
                <h2 class="section-title">Why Choose BookMyDoc?</h2>
                <p class="section-subtitle">Experience healthcare scheduling reimagined with features designed around your needs</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-calendar-check feature-icon"></i>
                    <h3 class="feature-title">Smart Scheduling</h3>
                    <p class="feature-description">Book appointments 24/7 with real-time availability updates and instant confirmations.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-user-md feature-icon"></i>
                    <h3 class="feature-title">Verified Specialists</h3>
                    <p class="feature-description">Access a network of thoroughly verified healthcare professionals across all specialties.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-bell feature-icon"></i>
                    <h3 class="feature-title">Smart Reminders</h3>
                    <p class="feature-description">Receive timely notifications and never miss an appointment with our intelligent reminder system.</p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>