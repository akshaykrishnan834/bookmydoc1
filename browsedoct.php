<?php 
include('db_connection.php'); // Include database connection
include('patientheader.php');   

// Get all unique specializations for the filter dropdown
$specializations_query = "SELECT DISTINCT specialization FROM doctorreg WHERE status = 'approved'";
$specializations_result = mysqli_query($conn, $specializations_query);

// Get all unique locations from the location field for the filter dropdown
$locations_query = "SELECT DISTINCT location FROM doctorreg WHERE status = 'approved'";
$locations_result = mysqli_query($conn, $locations_query);

// Initialize filters
$specialization_filter = "";
$location_filter = "";
$search_term = "";

// Handle form submission
if(isset($_GET['filter']) || isset($_GET['search'])) {
    // Get filter values
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
$query = "SELECT * FROM doctorreg WHERE status = 'approved'";

if($specialization_filter != "") {
    $query .= " AND specialization = '$specialization_filter'";
}

if($location_filter != "") {
    $query .= " AND location = '$location_filter'"; // Changed from address to location
}

if($search_term != "") {
    $query .= " AND (name LIKE '%$search_term%' OR specialization LIKE '%$search_term%' OR location LIKE '%$search_term%')"; // Changed from address to location
}

// Execute the query
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
        .doctor-contact {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .no-results {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
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
    </style>
</head>
<body>

<div class="container12">
    <h2 class="text-center mb-4">Approved Doctors</h2>
    
    <!-- Search and Filter Section -->
    <div class="filter-section">
        <form method="GET" action="" class="row g-3">
            <!-- Search bar -->
            <div class="col-md-4">
                <label for="search_term" class="form-label">Search</label>
                <input type="text" class="form-control" id="search_term" name="search_term" 
                       placeholder="Search by name, specialization..." value="<?php echo htmlspecialchars($search_term); ?>">
            </div>
            
            <!-- Specialization filter -->
            <div class="col-md-3">
                <label for="specialization" class="form-label">Department</label>
                <select class="form-select" id="specialization" name="specialization">
                    <option value="">All Departments</option>
                    <?php while($spec_row = mysqli_fetch_assoc($specializations_result)) { ?>
                        <option value="<?php echo htmlspecialchars($spec_row['specialization']); ?>" 
                                <?php if($specialization_filter == $spec_row['specialization']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($spec_row['specialization']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            
            <!-- Location filter -->
            <div class="col-md-3">
                <label for="location" class="form-label">Location</label>
                <select class="form-select" id="location" name="location">
                    <option value="">All Locations</option>
                    <?php while($loc_row = mysqli_fetch_assoc($locations_result)) { ?>
                        <option value="<?php echo htmlspecialchars($loc_row['location']); ?>"
                                <?php if($location_filter == $loc_row['location']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($loc_row['location']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            
            <!-- Filter buttons -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" name="filter" class="btn btn-primary me-2">Search</button>
                <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
    
    <!-- Display filtered results -->
    <div class="row">
        <?php 
        if(mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { 
        ?>
            <div class="col-md-4 mb-3">
                <div class="doctor-box">
                    <h5><?php echo htmlspecialchars($row['name']); ?></h5>
                    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($row['specialization']); ?></p>
                    
                    <!-- Display location separately with a badge style -->
                    <div class="location-badge">
                        <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($row['location']); ?>
                    </div>
                    
                    <div class="doctor-contact">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                    </div>
                    
                    <p>
                        <a href="appointmentdate.php?doctor_id=<?php echo $row['id']; ?>" class="btn btn-primary">
                            View Available Slots
                        </a>
                    </p>
                </div>
            </div>
        <?php 
            }
        } else {
            // No results found
            echo '<div class="col-12"><div class="no-results">
                    <h4>No doctors found</h4>
                    <p>Try changing your search criteria or reset filters</p>
                  </div></div>';
        }
        ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- Font Awesome for the location icon -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>