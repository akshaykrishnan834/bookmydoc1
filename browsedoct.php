<?php 
session_start();
include('db_connection.php'); 
include('patientheader2.php');   

// Get logged-in patient ID
$patient_id = $_SESSION['id'] ?? null;

// Check if the patient is disabled
$patient_disabled = false;
if ($patient_id) {
    $patient_query = "SELECT action FROM patientreg WHERE id = $patient_id";
    $patient_result = mysqli_query($conn, $patient_query);
    $patient_data = mysqli_fetch_assoc($patient_result);
    $patient_disabled = ($patient_data['action'] === 'disabled');
}

// Get specializations and locations for filters
$specializations_query = "SELECT DISTINCT specialization FROM doctorreg WHERE status = 'approved'";
$specializations_result = mysqli_query($conn, $specializations_query);
$locations_query = "SELECT DISTINCT location FROM doctorreg WHERE status = 'approved'";
$locations_result = mysqli_query($conn, $locations_query);

// Initialize filters
$specialization_filter = $location_filter = $search_term = "";
if(isset($_GET['filter']) || isset($_GET['search'])) {
    $specialization_filter = isset($_GET['specialization']) ? mysqli_real_escape_string($conn, $_GET['specialization']) : '';
    $location_filter = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';
    $search_term = isset($_GET['search_term']) ? mysqli_real_escape_string($conn, $_GET['search_term']) : '';
}

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    if (!$patient_id) {
        echo "<script>alert('Please log in to submit feedback.'); window.location.href='login.php';</script>";
        exit();
    }
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $feedback_text = mysqli_real_escape_string($conn, $_POST['feedback_text']);
    
    $feedback_insert = "INSERT INTO feedback (doctor_id, patient_id, rating, feedback_text, created_at) 
                        VALUES ('$doctor_id', '$patient_id', '$rating', '$feedback_text', NOW())";
    
    if (mysqli_query($conn, $feedback_insert)) {
        echo "<script>alert('Feedback submitted successfully!'); window.location.href='browsedoct.php';</script>";
    } else {
        echo "<script>alert('Error submitting feedback.');</script>";
    }
}

// Build the query with filters
$query = "SELECT * FROM doctorreg WHERE status = 'approved' AND action != 'disabled'";
if($specialization_filter) { $query .= " AND specialization = '$specialization_filter'"; }
if($location_filter) { $query .= " AND location = '$location_filter'"; }
if($search_term) { 
    $query .= " AND (name LIKE '%$search_term%' OR specialization LIKE '%$search_term%' OR location LIKE '%$search_term%')";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Doctor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .container12 {
            max-width: 1300px;
            margin: 60px auto;
            padding: 20px;
        }
        .page-title {
            color: #2c3e50;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .filter-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .filter-box .form-control, .filter-box .form-select {
            border-radius: 10px;
            border: 2px solid #0077b6;
            padding: 8px 15px;
        }
        .filter-box .btn-custom {
            background: linear-gradient(45deg, #0077b6, #00b4d8);
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .filter-box .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,119,182,0.3);
            color: white;
        }
        .doctor-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border-left: 6px solid #0077b6;
            position: relative;
            overflow: hidden;
        }
        .doctor-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .doctor-box::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(0,119,182,0.05);
            transform: rotate(45deg);
            z-index: 0;
        }
        .doctor-box > * {
            position: relative;
            z-index: 1;
        }
        .doctor-name {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.4rem;
        }
        .specialization {
            color: #0077b6;
            font-size: 1.1rem;
            margin-bottom: 10px;
            font-style: italic;
        }
        .location-badge {
            background: #e3f2fd;
            color: #0077b6;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            margin: 10px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .doctor-contact {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            border-left: 3px solid #0077b6;
        }
        .contact-item {
            margin: 8px 0;
            color: #555;
            font-size: 0.95rem;
        }
        .contact-item i {
            color: #0077b6;
            margin-right: 8px;
            width: 20px;
        }
        .rating-stars {
            color: #f1c40f;
            font-size: 1.2rem;
            margin: 10px 0;
        }
        .btn-custom {
            background: linear-gradient(45deg, #0077b6, #00b4d8);
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,119,182,0.3);
            color: white;
        }
        .btn-feedback {
            background: linear-gradient(45deg, #e67e22, #f1c40f);
        }
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .modal-header {
            background: #0077b6;
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .feedback-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #00b4d8;
            box-shadow: 0 0 5px rgba(0,180,216,0.3);
        }
    </style>
</head>
<body>

<div class="container12">
    <h2 class="page-title">Find Your Doctor</h2>

    <!-- Search Filter Form -->
    <div class="filter-box">
        <form method="GET" action="">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="specialization" class="form-label">Specialization</label>
                    <select name="specialization" id="specialization" class="form-select">
                        <option value="">All Specializations</option>
                        <?php while ($spec = mysqli_fetch_assoc($specializations_result)) { ?>
                            <option value="<?php echo htmlspecialchars($spec['specialization']); ?>" 
                                    <?php echo $specialization_filter === $spec['specialization'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($spec['specialization']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="location" class="form-label">Location</label>
                    <select name="location" id="location" class="form-select">
                        <option value="">All Locations</option>
                        <?php while ($loc = mysqli_fetch_assoc($locations_result)) { ?>
                            <option value="<?php echo htmlspecialchars($loc['location']); ?>" 
                                    <?php echo $location_filter === $loc['location'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($loc['location']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search_term" class="form-label">Search by Name</label>
                    <input type="text" name="search_term" id="search_term" class="form-control" 
                           value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Enter doctor name">
                </div>
                <div class="col-md-3">
                    <button type="submit" name="filter" class="btn btn-custom w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <?php 
        if(mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { 
                $doctor_id = $row['id'];
                $rating_query = "SELECT AVG(rating) AS avg_rating FROM feedback WHERE doctor_id = $doctor_id";
                $rating_result = mysqli_query($conn, $rating_query);
                $rating_row = mysqli_fetch_assoc($rating_result);
                $avg_rating = round($rating_row['avg_rating'], 1);

                $feedback_query = "SELECT f.rating, f.feedback_text, p.name AS patient_name 
                                 FROM feedback f 
                                 JOIN patientreg p ON f.patient_id = p.id 
                                 WHERE f.doctor_id = $doctor_id ORDER BY f.created_at DESC";
                $feedback_result = mysqli_query($conn, $feedback_query);

                $is_disabled = ($row['status'] == 'disabled' || $patient_disabled);
                $disable_class = $is_disabled ? 'disabled' : '';
        ?>
            <div class="col-md-4">
                <div class="doctor-box">
                    <h5 class="doctor-name"><?php echo htmlspecialchars($row['name']); ?></h5>
                    <p class="specialization"><?php echo htmlspecialchars($row['specialization']); ?></p>

                    <div class="location-badge">
                        <i class="fas fa-map-marker-alt me-2"></i> <?php echo htmlspecialchars($row['location']); ?>
                    </div>

                    <p class="rating-stars">
                        <?php 
                        $stars = floor($avg_rating);
                        for($i = 0; $i < 5; $i++) {
                            echo $i < $stars ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                        }
                        echo " " . ($avg_rating ? $avg_rating . "/5" : "No ratings");
                        ?>
                    </p>

                    <div class="doctor-contact">
                        <div class="contact-item"><i class="fas fa-envelope"></i><?php echo htmlspecialchars($row['email']); ?></div>
                        <div class="contact-item"><i class="fas fa-phone"></i><?php echo htmlspecialchars($row['phone']); ?></div>
                        <div class="contact-item"><i class="fas fa-home"></i><?php echo htmlspecialchars($row['address']); ?></div>
                    </div>

                    <div>
                        <a href="appointmentdate.php?doctor_id=<?php echo $row['id']; ?>" 
                           class="btn btn-custom <?php echo $disable_class; ?>">
                            <i class="fas fa-calendar-alt me-2"></i>Book Appointment
                        </a>
                        <button class="btn btn-custom btn-feedback" 
                                data-bs-toggle="modal" 
                                data-bs-target="#feedbackModal<?php echo $row['id']; ?>">
                            <i class="fas fa-comment me-2"></i>Feedback
                        </button>
                    </div>
                </div>
            </div>

            <!-- Feedback Modal -->
            <div class="modal fade" id="feedbackModal<?php echo $row['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Feedback for <?php echo htmlspecialchars($row['name']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <h6 class="mb-3">Previous Feedback:</h6>
                            <div style="max-height: 300px; overflow-y: auto;">
                                <?php 
                                if(mysqli_num_rows($feedback_result) > 0) {
                                    while ($feedback = mysqli_fetch_assoc($feedback_result)) {
                                        echo "<div class='feedback-item'>";
                                        echo "<strong>{$feedback['patient_name']}:</strong> ";
                                        for($i = 0; $i < 5; $i++) {
                                            echo $i < $feedback['rating'] ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-warning"></i>';
                                        }
                                        echo "<br>{$feedback['feedback_text']}</div>";
                                    }
                                } else {
                                    echo "<p class='text-muted'>No feedback yet.</p>";
                                }
                                ?>
                            </div>

                            <?php if ($patient_id): ?>
                                <form method="POST" class="mt-4">
                                    <input type="hidden" name="doctor_id" value="<?php echo $row['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Rating:</label>
                                        <select name="rating" class="form-select" required>
                                            <option value="5">5 - Excellent</option>
                                            <option value="4">4 - Good</option>
                                            <option value="3">3 - Average</option>
                                            <option value="2">2 - Below Average</option>
                                            <option value="1">1 - Poor</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Your Feedback:</label>
                                        <textarea name="feedback_text" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" name="submit_feedback" class="btn btn-custom w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                                    </button>
                                </form>
                            <?php else: ?>
                                <p class="text-danger mt-3">Please log in to submit feedback.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } } else { ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">No doctors found matching your criteria.</div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>