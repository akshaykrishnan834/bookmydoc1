<?php
include 'db_connection.php'; // Your DB connection file

//$doctorId = $_SESSION['id']; // Assuming doctor_id is stored in the session
?>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <div class="logo-container me-3">
                    <img src="images/logo.png" alt="Logo" class="logo-image">
                </div>
                <div class="brand-text">
                    <strong>BookMyDoc</strong>
                    <span class="tagline">Admin Panel</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admindashboard.php">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="managedoctors.php">
                            <i class="fas fa-user-md me-1"></i> Approve Doctors
                        </a>
                    </li>
                    
                </ul>
                <div class="nav-right d-flex align-items-center">
                <div class="nav-right d-flex align-items-center">
            <div class="dropdown">
                <a class="nav-link user-profile-dropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar-container">
                        <div class="avatar">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="admindashboard.php"><i class="fas fa-id-card me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="adminlogout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>

</div>

</div>

            </div>
        </div>
    </nav>
</header>
<!-- Bootstrap Bundle with Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Add this to your CSS file or in a style tag in your head section -->
<style>
    /* Modern Header Styling */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    header {
    font-family: 'Roboto', sans-serif;
}

    
    .navbar {
        background: linear-gradient(135deg, #005bea, #00c6fb);
        padding: 1rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
       
        position: relative;
        z-index: 100;
    }
    
    .navbar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgcGF0dGVyblRyYW5zZm9ybT0icm90YXRlKDQ1KSI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0iI2ZmZiIgZmlsbC1vcGFjaXR5PSIwLjA1Ii8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIiAvPjwvc3ZnPg==');
        opacity: 0.8;
        z-index: -1;
        border-bottom-left-radius: 18px;
        border-bottom-right-radius: 18px;
    }
    
    .logo-container {
        background: rgba(255,255,255,0.25);
        border-radius: 16px;
        width: 54px;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .logo-container:hover {
        transform: rotate(10deg) scale(1.1);
        background: rgba(255,255,255,0.35);
        box-shadow: 0 12px 25px rgba(0,0,0,0.2);
    }
    
    .logo-image {
        max-width: 100%;
        height: auto;
        filter: drop-shadow(0 2px 5px rgba(0,0,0,0.2));
    }
    
    .navbar-brand {
        transition: all 0.4s ease;
        padding: 0.3rem 0.5rem;
        border-radius: 12px;
    }
    
    .navbar-brand:hover {
        transform: translateY(-4px);
        background: rgba(255,255,255,0.1);
    }
    
    .brand-text {
        display: flex;
        flex-direction: column;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .brand-text strong {
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        letter-spacing: 0.6px;
        position: relative;
    }
    
    .brand-text strong::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 30px;
        height: 3px;
        background: rgba(255,255,255,0.7);
        border-radius: 10px;
    }
    
    .tagline {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.85);
        letter-spacing: 0.3px;
        margin-top: 2px;
    }
    
    .navbar-nav .nav-item {
        position: relative;
        margin: 0 6px;
    }
    
    .navbar-nav .nav-link {
        color: rgba(255,255,255,0.9);
        font-weight: 500;
        padding: 0.8rem 1.2rem;
        border-radius: 12px;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .navbar-nav .nav-link::before {
        content: '';
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.15);
        transition: all 0.3s ease;
        z-index: -1;
        border-radius: 12px;
    }
    
    .navbar-nav .nav-link:hover {
        color: white;
        transform: translateY(-3px);
    }
    
    .navbar-nav .nav-link:hover::before {
        top: 0;
    }
    
    .navbar-nav .nav-link.active {
        color: white;
        background: rgba(255,255,255,0.2);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    
    .navbar-nav .nav-link i {
        transition: all 0.3s ease;
        font-size: 1.1rem;
        vertical-align: middle;
    }
    
    .navbar-nav .nav-link:hover i {
        transform: translateY(-2px) scale(1.1);
    }
    
    .user-dropdown {
        display: flex;
        align-items: center;
        background-color: rgba(255,255,255,0.15);
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .user-dropdown:hover {
        background-color: rgba(255,255,255,0.25);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .user-dropdown i {
        font-size: 1.2rem;
        margin-right: 8px;
    }
    .profile-pic {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}
.avatar-container {
    width: 50px;
    height: 50px;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.avatar {
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}

.avatar i {
    font-size: 24px;
    color: white;
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.avatar-container:hover .avatar {
    transform: scale(1.1);
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.8);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.avatar-container:hover .avatar i {
    transform: scale(1.1);
}

/* Add a subtle pulse animation on hover */
@keyframes avatarPulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(255, 255, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
}

.avatar-container:hover .avatar {
    animation: avatarPulse 1.5s infinite;
}

.profile-pic:hover {
    transform: scale(1.1);
}

/* Smooth dropdown appearance */
.dropdown-menu {
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    
    .dropdown-menu {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        padding: 1rem 0;
        margin-top: 12px;
        background: rgba(255,255,255,0.98);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        transform: translateY(10px);
        transition: all 0.3s ease;
        opacity: 0;
        visibility: hidden;
        display: block;
    }
    
    .dropdown.show .dropdown-menu {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }
    
    .dropdown-item {
        padding: 0.8rem 1.5rem;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        font-weight: 500;
        color: #444;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
    
    .dropdown-item::before {
        content: '';
        position: absolute;
        left: -100%;
        top: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, rgba(0,91,234,0.1), rgba(0,198,251,0.05));
        transition: all 0.3s ease;
        z-index: -1;
    }
    
    .dropdown-item:hover {
        background: transparent;
        border-left: 4px solid #005bea;
        transform: translateX(5px);
        color: #005bea;
    }
    
    .dropdown-item:hover::before {
        left: 0;
    }
    
    .dropdown-item i {
        transition: all 0.3s ease;
    }
    
    .dropdown-item:hover i {
        transform: scale(1.1);
        color: #005bea;
    }
    
    .dropdown-divider {
        margin: 0.7rem 1rem;
        background-color: rgba(0,0,0,0.1);
    }
    
    .btn-emergency {
        background: linear-gradient(135deg, #ff0844, #ffb199);
        color: white;
        border: none;
        font-weight: 600;
        padding: 0.7rem 1.5rem;
        border-radius: 50px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 6px 18px rgba(255,8,68,0.3);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .btn-emergency::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, rgba(255,255,255,0.2), transparent);
        transition: all 0.4s ease;
        z-index: -1;
    }
    
    .btn-emergency:hover {
        transform: translateY(-4px) scale(1.05);
        box-shadow: 0 10px 25px rgba(255,8,68,0.4);
        color: white;
    }
    
    .btn-emergency:hover::before {
        left: 100%;
    }
    
    .btn-emergency i {
        margin-right: 6px;
        transition: all 0.3s ease;
    }
    
    .btn-emergency:hover i {
        transform: scale(1.2);
    }
    
    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: rgba(0, 46, 118, 0.97);
            border-radius: 20px;
            padding: 1.5rem;
            margin-top: 1.2rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .navbar-nav {
            margin: 1.2rem 0;
        }
        
        .nav-right {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
            margin-top: 1rem;
        }
        
        .user-dropdown, .btn-emergency {
            width: 100%;
            margin: 0.6rem 0;
            justify-content: center;
            text-align: center;
        }
        
        .navbar-nav .nav-link {
            border-left: 4px solid transparent;
            border-radius: 8px;
            margin: 5px 0;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            border-left: 4px solid rgba(255,255,255,0.7);
            padding-left: 1.5rem;
        }
    }
    
    /* Highlight current page */
    .current-page {
        position: relative;
    }
    
    .current-page::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 8px;
        height: 8px;
        background-color: white;
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(255,255,255,0.7);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: translateX(-50%) scale(1);
            opacity: 1;
        }
        50% {
            transform: translateX(-50%) scale(1.5);
            opacity: 0.5;
        }
        100% {
            transform: translateX(-50%) scale(1);
            opacity: 1;
        }
    }
    
</style>
<script>
    /* Add this script to your page to highlight the current page */
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = window.location.pathname.split('/').pop();
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPage) {
                link.classList.add('active');
                link.parentElement.classList.add('current-page');
            }
        });
        
        // Add dropdown animation 
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('show.bs.dropdown', function() {
                const menu = this.querySelector('.dropdown-menu');
                menu.style.display = 'block';
                setTimeout(() => {
                    menu.style.opacity = '1';
                    menu.style.visibility = 'visible';
                    menu.style.transform = 'translateY(0)';
                }, 10);
            });
            
            
        });
    });
    </script>