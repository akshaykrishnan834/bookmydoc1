<?php
session_start();
include('doctorheader.php');
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookmydoc";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctor_id = $_SESSION['id'];

// Updated query to include status and rejection_reason fields
$stmt = $conn->prepare("SELECT name, email, age, qualifications, experience, specialization, degree_certificate, profile_photo, status, rejection_reason FROM doctorreg WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

$stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_feedbacks FROM feedback WHERE doctor_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$rating_result = $stmt->get_result()->fetch_assoc();

$avg_rating = round($rating_result['avg_rating'], 1); // Round to 1 decimal place
$total_feedbacks = $rating_result['total_feedbacks'];

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. <?= htmlspecialchars($doctor['name']) ?> - Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0077b6;
            --secondary-color: #00b4d8;
            --text-color: #2d3436;
            --text-light: #636e72;
            --bg-color: #f5f6fa;
            --white: #ffffff;
            --border-radius: 12px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .edcontainer {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            display: grid;
            grid-template-columns: 300px 1fr;
        }

        .profile-sidebar {
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            text-align: center;
        }

        .profile-photo-container {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--white);
            box-shadow: var(--shadow);
        }

        .profile-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .verification-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #2ecc71;
            padding: 0.5rem;
            border-radius: 50%;
            color: white;
            box-shadow: var(--shadow);
        }

        .doctor-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .doctor-specialization {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .quick-info {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            text-align: center;
        }

        .info-label {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .info-value {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .profile-main {
            padding: 2rem;
        }

        .section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-content {
            color: var(--text-light);
            font-size: 1rem;
        }

        .qualification-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .qualification-icon {
            color: var(--primary-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: var(--white);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn:hover {
            background: #005f99;
            transform: translateY(-2px);
        }

        .certificate-link {
            color: var(--primary-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .certificate-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .profile-card {
                grid-template-columns: 1fr;
            }

            .profile-sidebar {
                padding: 1.5rem;
            }

            .profile-photo-container {
                width: 150px;
                height: 150px;
            }
        }
        .action-buttons {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            color: var(--white);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            text-align: center;
        }

        .btn-password {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
        }

        .btn-password:hover {
            background: linear-gradient(135deg, #ff5252, #ff7675);
            transform: translateY(-2px);
        }

        .btn-contact {
            background: linear-gradient(135deg, #4CAF50, #8BC34A);
        }

        .btn-contact:hover {
            background: linear-gradient(135deg, #43A047, #7CB342);
            transform: translateY(-2px);
        }

        .btn-photo {
            background: linear-gradient(135deg, #9C27B0, #E040FB);
        }

        .btn-photo:hover {
            background: linear-gradient(135deg, #8E24AA, #D500F9);
            transform: translateY(-2px);
        }

        /* Status badge styles */
        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
        }

        .status-active {
            background-color: #2ecc71;
            color: white;
        }

        .status-pending {
            background-color: #f39c12;
            color: white;
        }

        .status-inactive, .status-rejected {
            background-color: #e74c3c;
            color: white;
        }

        /* Rejection reason modal styles */
        .rejection-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: slideIn 0.4s;
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: black;
            text-decoration: none;
        }

        .modal-header {
            border-bottom: 1px solid #e6e6e6;
            padding-bottom: 10px;
            margin-bottom: 15px;
            color: #e74c3c;
        }

        .modal-body {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        @keyframes fadeIn {
            from {opacity: 0}
            to {opacity: 1}
        }

        @keyframes slideIn {
            from {transform: translateY(-50px); opacity: 0;}
            to {transform: translateY(0); opacity: 1;}
        }

        @media (max-width: 768px) {
            .action-buttons {
                grid-template-columns: 1fr;
            }
            .modal-content {
                width: 95%;
                margin: 20% auto;
            }
        }
    </style>
</head>
<body>
    <div class="edcontainer">
        <div class="profile-card">
            <div class="profile-sidebar">
                <div class="profile-photo-container">
                <img src="<?= htmlspecialchars($doctor['profile_photo']) ?>" onerror="this.onerror=null; this.src='images/profilepicdoct.jpg';" class="profile-photo">

                <div class="verification-badge">
                    </div>
                </div>
                <h1 class="doctor-name">Dr. <?= htmlspecialchars($doctor['name']) ?></h1>
                <p class="doctor-specialization"><?= htmlspecialchars($doctor['specialization']) ?></p>
                
                <!-- Display status badge -->
                <?php 
                $statusClass = '';
                $statusText = isset($doctor['status']) ? htmlspecialchars($doctor['status']) : 'Unknown';
                
                if (strtolower($statusText) == 'active') {
                    $statusClass = 'status-active';
                } elseif (strtolower($statusText) == 'pending') {
                    $statusClass = 'status-pending';
                } elseif (strtolower($statusText) == 'rejected') {
                    $statusClass = 'status-rejected';
                } else {
                    $statusClass = 'status-inactive';
                }

                // Check if rejection reason exists
                $hasRejectionReason = strtolower($statusText) == 'rejected' && !empty($doctor['rejection_reason']);
                ?>
                
                <?php if ($hasRejectionReason): ?>
                <div id="statusBadge" class="status-badge <?= $statusClass ?>" onclick="showRejectionModal()">
                    <?= ucfirst($statusText) ?> <i class="fas fa-info-circle"></i>
                </div>
                <?php else: ?>
                <div class="status-badge <?= $statusClass ?>">
                    <?= ucfirst($statusText) ?>
                </div>
                <?php endif; ?>
                
                <div class="quick-info">
                    <div class="info-item">
                        <div class="info-value"><?= htmlspecialchars($doctor['experience']) ?>+</div>
                        <div class="info-label">Years Exp.</div>
                    </div>
                    <p class="rating-value">⭐ <br><?= $avg_rating ? $avg_rating . "/5" : "No ratings yet" ?></p>
                    <p class="feedback-count"><?= $total_feedbacks ?> <br>reviews</p>
                </div>
<a href="adddoctors.php" class="btn">
                    <i class="fas fa-edit"></i>
                    View Profile
                </a>
            </div>

            <div class="profile-main">
                <div class="section">
                    <h2 class="section-title">
                        
                    </h2>
                    <?php if (!empty($doctor['specialization']) && !empty($doctor['qualifications'])) { ?>
    <div class="section">
        <h2 class="section-title">
            <i class="fas fa-user-md"></i>
            About
        </h2>
        <p class="section-content">
            Experienced <?= htmlspecialchars($doctor['specialization']) ?> specialist with 
            <?= htmlspecialchars($doctor['experience']) ?> years of practice.  
            Committed to providing high-quality medical care and ensuring patient well-being.
        </p>
    </div>
    <?php } else { ?>
    <div class="alert alert-danger">
        <h4 style="color: red;">Please complete your profile by filling in Specialization and Qualifications.<h4>
    </div>
<?php } ?>

<?php if (isset($doctor['approved']) && $doctor['approved'] == 0) { ?>
    <div class="alert alert-warning">
        Your profile is not yet approved by the admin. Please wait for approval.
    </div>
<?php } ?>

                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-graduation-cap"></i>
                        Qualifications
                    </h2>
                    <div class="section-content">
                        <?php
                        $qualifications = explode(',', $doctor['qualifications']);
                        foreach ($qualifications as $qualification) {
                            echo '<div class="qualification-item">';
                            echo '<i class="fas fa-check-circle qualification-icon"></i>';
                            echo '<span>' . htmlspecialchars(trim($qualification)) . '</span>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-envelope"></i>
                        Contact Information
                    </h2>
                    <div class="section-content">
                        <p><i class="fas fa-envelope" style="margin-right: 0.5rem;"></i> <?= htmlspecialchars($doctor['email']) ?></p>
                    </div>
                </div>

                <?php if (!empty($doctor['degree_certificate'])): ?>
                <br>
                <br>
                    <a href="updatedoctor.php?id=<?php echo $doctor_id; ?>" class="action-btn btn-photo">
                            <i class="fas fa-user-edit"></i> Update Profile
                        </a>
                        <a href="doctor_feedback.php?id=<?php echo $doctor_id; ?>" class="action-btn btn-photo">
                        <i class="fas fa-comment-dots"></i>  Feedbacks
                        </a>
                    </div>
                </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Rejection Reason Modal -->
    <?php if ($hasRejectionReason): ?>
    <div id="rejectionModal" class="rejection-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeRejectionModal()">&times;</span>
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Application Rejected</h3>
            </div>
            <div class="modal-body">
                <strong>Reason for rejection:</strong>
                <p><?= htmlspecialchars($doctor['rejection_reason']) ?></p>
                <p style="margin-top: 15px;">Please review the issues mentioned above and update your profile accordingly. You can reapply after making the necessary changes.</p>
                <form method="POST" action="updaterejected.php" >
                <button class="action-btn btn-contact">Update Profile</button>
            </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function showRejectionModal() {
            document.getElementById("rejectionModal").style.display = "block";
        }
        
        function closeRejectionModal() {
            document.getElementById("rejectionModal").style.display = "none";
        }
        
        // Close modal if user clicks outside of it
        window.onclick = function(event) {
            var modal = document.getElementById("rejectionModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    <?php endif; ?>
</body>
</html>