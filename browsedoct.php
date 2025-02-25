<?php
include('db_connection.php'); // Include database connection
include('patientheader.php'); 

// Fetch only approved doctors from doctorreg
$query = "SELECT * FROM doctorreg WHERE status = 'approved'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Doctors</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container12 { max-width: 1200px; margin: 40px auto; }
        .doctor-box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            border-left: 5px solid #0077b6;
        }
        .doctor-box:hover { transform: translateY(-3px); }
        .btn-primary { background-color: #0077b6; border: none; }
        .doctor-contact {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div class="container12">
    <h2 class="text-center mb-4">Approved Doctors</h2>
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4 mb-3">
                <div class="doctor-box">
                    <h5><?php echo htmlspecialchars($row['name']); ?></h5>
                    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($row['specialization']); ?></p>
                    <p><strong>Experience:</strong> <?php echo htmlspecialchars($row['experience']); ?> years</p>
                    
                    <div class="doctor-contact">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                    </div>
                    
                    <p>
                        <a href="book_appointment_page.php?doctor_id=<?php echo $row['id']; ?>" class="btn btn-primary">
                            View Available Slots
                        </a>
                    </p>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>