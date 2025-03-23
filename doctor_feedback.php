<?php session_start(); include('db_connection.php'); 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Invalid doctor ID.</p>";
    exit;
}

$doctor_id = intval($_GET['id']); // Secure the ID input

// Fetch doctor details
$sql = "SELECT name, specialization, profile_photo FROM doctorreg WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch feedback for the respective doctor
$sql_feedback = "SELECT f.rating, f.feedback_text, p.name, f.created_at
                FROM feedback f
                JOIN patientreg p ON f.patient_id = p.id
                WHERE f.doctor_id = ? ORDER BY f.created_at DESC";
$stmt_feedback = $conn->prepare($sql_feedback);
$stmt_feedback->bind_param("i", $doctor_id);
$stmt_feedback->execute();
$feedbacks = $stmt_feedback->get_result();
$stmt_feedback->close();

// Calculate average rating
$avg_rating = 0;
$total_ratings = 0;
if ($feedbacks->num_rows > 0) {
    $temp_feedbacks = [];
    $rating_sum = 0;
    while ($feedback = $feedbacks->fetch_assoc()) {
        $temp_feedbacks[] = $feedback;
        $rating_sum += $feedback['rating'];
        $total_ratings++;
    }
    $feedbacks = $temp_feedbacks;
    $avg_rating = $total_ratings > 0 ? round($rating_sum / $total_ratings, 1) : 0;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile - <?= htmlspecialchars($doctor['name']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --border-radius: 12px;
        }
        
        body {
            background-color: #f0f2f5;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container {
            max-width: 850px;
            margin: 40px auto;
            padding: 0 15px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
        }
        
        .doctor-profile {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .doctor-profile::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: var(--secondary-color);
            z-index: 0;
        }
        
        .doctor-photo-container {
            position: relative;
            z-index: 1;
            margin-top: 10px;
        }
        
        .doctor-photo {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            background-color: #f5f5f5;
        }
        
        .doctor-info {
            text-align: center;
            margin-top: 15px;
            width: 100%;
        }
        
        .doctor-name {
            margin: 10px 0 5px;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.8rem;
        }
        
        .doctor-specialization {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }
        
        .rating-summary {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 15px 0;
            padding: 12px;
            background-color: var(--light-bg);
            border-radius: var(--border-radius);
        }
        
        .average-rating {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-right: 10px;
        }
        
        .rating-stars {
            color: #ffcc00;
            font-size: 1.2rem;
        }
        
        .total-reviews {
            font-size: 0.9rem;
            color: #666;
            margin-left: 10px;
        }
        
        .section-title {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin: 30px 0 20px;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--secondary-color);
        }
        
        .feedback-list {
            margin-top: 20px;
        }
        
        .feedback-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            border-left: 4px solid var(--secondary-color);
        }
        
        .feedback-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .patient-name {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .feedback-rating {
            display: flex;
            align-items: center;
        }
        
        .feedback-text {
            margin: 10px 0;
            color: #444;
            line-height: 1.5;
            font-size: 0.95rem;
        }
        
        .feedback-date {
            text-align: right;
            font-size: 0.8rem;
            color: #888;
            margin-top: 5px;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.2s;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        
        .back-button:hover {
            background-color: #2980b9;
            color: white;
        }
        
        .back-button i {
            margin-right: 8px;
        }
        
        .empty-feedback {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: var(--border-radius);
            color: #666;
        }
        
        .empty-icon {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        @media (max-width: 576px) {
            .doctor-photo {
                width: 120px;
                height: 120px;
            }
            
            .feedback-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .feedback-rating {
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="doctor-profile">
        <div class="doctor-photo-container">
            <img src="<?= htmlspecialchars($doctor['profile_photo']) ?>" class="doctor-photo"
                 onerror="this.onerror=null; this.src='images/default-doctor.jpg';">
        </div>
        
        <div class="doctor-info">
            <h2 class="doctor-name"><?= htmlspecialchars($doctor['name']) ?></h2>
            <p class="doctor-specialization"><?= htmlspecialchars($doctor['specialization']) ?></p>
            
            <?php if($total_ratings > 0): ?>
            <div class="rating-summary">
                <div class="average-rating"><?= $avg_rating ?></div>
                <div class="rating-stars">
                    <?php
                    for($i = 1; $i <= 5; $i++) {
                        if($i <= floor($avg_rating)) {
                            echo '<i class="fas fa-star"></i>';
                        } elseif($i == ceil($avg_rating) && $avg_rating != floor($avg_rating)) {
                            echo '<i class="fas fa-star-half-alt"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    ?>
                </div>
                <div class="total-reviews">(<?= $total_ratings ?> reviews)</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <h3 class="section-title">Patient Feedback</h3>
    
    <div class="feedback-list">
        <?php if (!empty($feedbacks)): ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="feedback-card">
                    <div class="feedback-header">
                        <div class="patient-name"><?= htmlspecialchars($feedback['name']) ?></div>
                        <div class="feedback-rating">
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php if($i <= $feedback['rating']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <p class="feedback-text"><?= htmlspecialchars($feedback['feedback_text']) ?></p>
                    <p class="feedback-date"><?= date("M d, Y", strtotime($feedback['created_at'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-feedback">
                <div class="empty-icon"><i class="far fa-comment-dots"></i></div>
                <p>No feedback available for this doctor yet.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <a href="doctorac.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Doctors
    </a>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>