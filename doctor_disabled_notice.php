<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: doctorlog.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Disabled - BookMyDoc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
            background-image: url('images/bg2.jpg');
            background-size: cover;
            background-position: center;
        }

        .modal-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
            width: 90%;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .icon-container {
            background: #ff6b6b;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 30px;
        }

        .icon-container i {
            font-size: 50px;
            color: white;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }

        p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .button-container {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-primary {
            background: #0077b6;
            color: white;
            border: none;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }

        .contact-info strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="modal-container">
        <div class="icon-container">
            <i class="fas fa-user-lock"></i>
        </div>
        <h1>Account Access Restricted</h1>
        <p>Your doctor account has been temporarily disabled. This may be due to pending verification or administrative action.</p>
        <p>Please contact the administrator for more information and to resolve this issue.</p>
        
        <div class="button-container">
            <a href="doctorlogout.php" class="btn btn-primary">Sign Out</a>
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=akshaykrishnan2027@mca.ajce.in&su=Doctor Account Disabled - Support Required&body=Hello,%0D%0A%0D%0AMy doctor account has been disabled. I would like to request assistance in resolving this issue.%0D%0A%0D%0ADoctor Email: <?php echo htmlspecialchars($_SESSION['email']); ?>%0D%0A%0D%0AThank you." 
               class="btn btn-secondary" 
               target="_blank">Contact Support</a>
        </div>

        <div class="contact-info">
            <p>For immediate assistance:</p>
            <p><strong>Email:</strong> admin@bookmydoc.com</p>
            <p><strong>Phone:</strong> 7025572282</p>
        </div>
    </div>
</body>
</html> 