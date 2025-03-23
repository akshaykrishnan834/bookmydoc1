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

// Get specializations for filter dropdown
$specializations_query = "SELECT DISTINCT specialization FROM doctorreg WHERE status = 'approved'";
$specializations_result = mysqli_query($conn, $specializations_query);

// Get locations for filter dropdown
$locations_query = "SELECT DISTINCT location FROM doctorreg WHERE status = 'approved'";
$locations_result = mysqli_query($conn, $locations_query);

// Initialize filters
$specialization_filter = $location_filter = $search_term = "";

if(isset($_GET['filter']) || isset($_GET['search'])) {
    if(isset($_GET['specialization']) && $_GET['specialization'] != "") {
        $specialization_filter = mysqli_real_escape_string($conn, $_GET['specialization']);
    }
    if(isset($_GET['location']) && $_GET['location'] != "") {
        $location_filter = mysqli_real_escape_string($conn, $_GET['location']);
    }
    if(isset($_GET['search_term']) && $_GET['search_term'] != "") {
        $search_term = mysqli_real_escape_string($conn, $_GET['search_term']);
    }
}

// Build the query with filters
// Build the query with filters
$query = "SELECT * FROM doctorreg WHERE status = 'approved' AND action != 'disabled'";
if($specialization_filter != "") { $query .= " AND specialization = '$specialization_filter'"; }
if($location_filter != "") { $query .= " AND location = '$location_filter'"; }
if($search_term != "") { 
    $query .= " AND (name LIKE '%$search_term%' OR specialization LIKE '%$search_term%' OR location LIKE '%$search_term%')";
}


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
            transition: transform 0.2s;
        }
        .doctor-box:hover { transform: translateY(-3px); }
        .btn-primary { background-color: #0077b6; border: none; }
        .doctor-contact { background-color: #f8f9fa; padding: 8px; border-radius: 5px; margin: 10px 0; }
        .location-badge {
            display: inline-block;
            background-color: #e3f2fd;
            color: #0077b6;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.9rem;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #b3e0ff;
        }
        .rating-stars { color: #ffcc00; }
    </style>
</head>
<body>

<div class="container12">
    <h2 class="text-center mb-4">Approved Doctors</h2>

    <div class="row">
        <?php 
        if(mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { 
                // Fetch average rating for doctor
                $doctor_id = $row['id'];
                $rating_query = "SELECT AVG(rating) AS avg_rating FROM feedback WHERE doctor_id = $doctor_id";
                $rating_result = mysqli_query($conn, $rating_query);
                $rating_row = mysqli_fetch_assoc($rating_result);
                $avg_rating = round($rating_row['avg_rating'], 1);

                // Fetch existing feedback
                $feedback_query = "SELECT f.rating, f.feedback_text, p.name AS patient_name 
                                   FROM feedback f 
                                   JOIN patientreg p ON f.patient_id = p.id 
                                   WHERE f.doctor_id = $doctor_id ORDER BY f.created_at DESC";
                $feedback_result = mysqli_query($conn, $feedback_query);

                // Disable slot booking if the doctor is disabled or the patient is disabled
                $is_disabled = ($row['status'] == 'disabled' || $patient_disabled);
                $disable_class = $is_disabled ? 'disabled' : '';
        ?>
            <div class="col-md-4 mb-3">
                <div class="doctor-box">
                    <h5><?php echo htmlspecialchars($row['name']); ?></h5>
                    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($row['specialization']); ?></p>

                    <div class="location-badge">
                        <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($row['location']); ?>
                    </div>

                    <p class="rating-stars">⭐ <?php echo $avg_rating ? $avg_rating . "/5" : "No ratings yet"; ?></p>

                    <div class="doctor-contact">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                    </div>

                    <p>
                        <a href="appointmentdate.php?doctor_id=<?php echo $row['id']; ?>" 
                           class="btn btn-primary <?php echo $disable_class; ?>">
                            View Available Slots
                        </a>
                        <button class="btn btn-warning" data-bs-toggle="modal" 
                                data-bs-target="#feedbackModal<?php echo $row['id']; ?>">
                            View Feedback
                        </button>
                    </p>
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
                            <h6>Previous Feedback:</h6>
                            <div style="max-height: 200px; overflow-y: auto;">
                                <?php 
                                if(mysqli_num_rows($feedback_result) > 0) {
                                    while ($feedback = mysqli_fetch_assoc($feedback_result)) {
                                        echo "<p><strong>{$feedback['patient_name']}:</strong> ⭐ {$feedback['rating']}/5 <br> {$feedback['feedback_text']}</p><hr>";
                                    }
                                } else {
                                    echo "<p>No feedback yet.</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php } } ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?> 
