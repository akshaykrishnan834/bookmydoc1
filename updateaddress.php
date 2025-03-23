<?php 
session_start(); 
include('db_connection.php'); // Include database connection  

if (!isset($_SESSION['id'])) {     
    die("Unauthorized access"); 
}  

$doctor_id = $_SESSION['id']; // Get logged-in doctor's ID  

// Fetch the current address and location
$sql = "SELECT address, location FROM doctorreg WHERE id = ?"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $doctor_id); 
$stmt->execute(); 
$result = $stmt->get_result(); 
$doctor = $result->fetch_assoc(); 
$current_address = $doctor['address'] ?? ''; // Fetch existing address or set empty if null
$current_location = $doctor['location'] ?? ''; // Fetch existing location  

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {     
    $new_address = trim($_POST['address']);     
    $new_location = trim($_POST['location']);      
    
    // Update the address and location in the database     
    $update_sql = "UPDATE doctorreg SET address = ?, location = ? WHERE id = ?";     
    $update_stmt = $conn->prepare($update_sql);     
    $update_stmt->bind_param("ssi", $new_address, $new_location, $doctor_id);      
    
    if ($update_stmt->execute()) {         
        $message = "<div class='alert alert-success' role='alert'>
                        <i class='fas fa-check-circle me-2'></i> Address & Location updated successfully!
                    </div>";     
    } else {         
        $message = "<div class='alert alert-danger' role='alert'>
                        <i class='fas fa-exclamation-circle me-2'></i> Error updating details. Please try again.
                    </div>";     
    }      
    
    $update_stmt->close(); 
} 
?>  

<!DOCTYPE html> 
<html lang="en"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <title>Update Address & Location</title>     
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>         
        body {             
            background-color: #f0f7ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }         
        .card {             
            max-width: 550px;             
            margin: 60px auto;             
            padding: 30px;             
            border-radius: 15px;             
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            background: linear-gradient(to bottom, #ffffff, #f8f9fa);
        }
        .form-heading {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 12px;
        }
        .form-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background: #3498db;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #dce4ec;
            transition: all 0.3s;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            border-color: #3498db;
        }
        .form-label {
            font-weight: 500;
            color: #34495e;
            margin-bottom: 8px;
        }
        .btn-primary {
            background-color: #3498db;
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(52, 152, 219, 0.2);
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(52, 152, 219, 0.3);
        }
        .btn-secondary {
            background-color: #95a5a6;
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(149, 165, 166, 0.2);
            transition: all 0.3s;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(149, 165, 166, 0.3);
        }
        .icon-wrapper {
            background-color: #ebf5fb;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 25px;
        }
        .icon-wrapper i {
            font-size: 40px;
            color: #3498db;
        }
        .alert {
            border-radius: 8px;
            font-weight: 500;
        }
        .input-group-text {
            background-color: #ebf5fb;
            border: 1px solid #dce4ec;
            color: #3498db;
        }
    </style> 
</head> 
<body> 
    <div class="container">
        <div class="card">
            <div class="icon-wrapper">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <h2 class="text-center form-heading">Update Clinic Location</h2>
            
            <?php if (!empty($message)) echo $message; ?>
            
            <form method="POST" action="" class="mt-4">
                <div class="mb-4">
                    <label for="address" class="form-label">
                        <i class="fas fa-home me-2"></i>Address
                    </label>
                    <textarea name="address" id="address" class="form-control" rows="3" 
                        placeholder="Enter your complete practice address" required><?php echo htmlspecialchars($current_address); ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="location" class="form-label">
                        <i class="fas fa-location-dot me-2"></i>Location
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                        <input type="text" name="location" id="location" class="form-control" 
                            value="<?php echo htmlspecialchars($current_location); ?>" placeholder="City, State, Country" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-save me-2"></i>Update Details
                </button>
            </form>
            
            <a href="updatedoctor.php" class="btn btn-secondary w-100">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body> 
</html>