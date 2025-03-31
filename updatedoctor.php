<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        
        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            width: 100%;
            margin: 20px;
            text-align: center;
        }
        
        .page-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 12px;
            text-align: center;
            font-size: 24px;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #3498db, #2c3e50);
            border-radius: 2px;
        }
        
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn-option {
            border: none;
            padding: 12px 10px;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: white;
            width: 220px;
            height: 85px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1;
            margin: 0 auto;
        }
        
        .btn-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(100%);
            transition: transform 0.3s ease;
            z-index: -1;
        }
        
        .btn-option:hover {
            transform: translateY(-4px);
            color: white;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        
        .btn-option:hover::before {
            transform: translateY(0);
        }
        
        .btn-option .icon {
            font-size: 24px;
            margin-bottom: 6px;
        }
        
        .btn-option .text {
            font-size: 14px;
        }
        
        .password {
            background: linear-gradient(45deg, #3498db, #2980b9);
        }
        
        .contact {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
        }
        
        .profile {
            background: linear-gradient(45deg, #f1c40f, #f39c12);
        }
        
        .address {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
        }
        
        .back {
            background: linear-gradient(45deg, #34495e, #2c3e50);
        }
        
        .branding {
            margin-top: 20px;
            font-size: 12px;
            color: #7f8c8d;
            text-align: center;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(0,0,0,0.1), transparent);
            width: 100%;
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="card">
            <h2 class="page-title">Update Profile</h2>
            
            <div class="button-container">
                <a href="doctorprofileresetpassword.php" class="btn-option password">
                    <div class="icon"><i class="fas fa-key"></i></div>
                    <div class="text">Reset Password</div>
                </a>
                
                <a href="doctorprofilecontact.php" class="btn-option contact">
                    <div class="icon"><i class="fas fa-address-card"></i></div>
                    <div class="text">Update Contact Info</div>
                </a>
                
                <a href="doctorchangeprofile.php" class="btn-option profile">
                    <div class="icon"><i class="fas fa-user-circle"></i></div>
                    <div class="text">Change Profile Photo</div>
                </a>
                
                <a href="updateaddress.php" class="btn-option address">
                    <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="text">Update Address</div>
                </a>
                <a href="update_fees.php" class="btn-option profile">
    <div class="icon"><i class="fas fa-rupee-sign"></i></div>
    <div class="text">Update Fees</div>
</a>
                
                <div class="divider"></div>
                
                <a href="doctorac.php" class="btn-option back">
                    <div class="icon"><i class="fas fa-arrow-left"></i></div>
                    <div class="text">Back to Profile</div>
                </a>
            </div>
            
            
        </div>
    </div>
</body>
</html>