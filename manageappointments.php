<?php
session_start();
include('db_connection.php');
include('doctorheader.php');

$doctor_id = $_SESSION['id']; // Get logged-in doctor's ID

// Fetch pending appointment requests
$sql = "SELECT ar.id, u.name AS patient_name, ar.appointment_date, da.start_time, da.end_time 
        FROM appointment_requests ar
        JOIN patientreg u ON ar.user_id = u.id
        JOIN doctor_availability da ON ar.slot_id = da.id
        WHERE ar.doctor_id = ? AND ar.status = 'Pending'
        ORDER BY ar.appointment_date";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Requests</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <style>
        :root {
            --primary-color: #4a6cf7;
            --secondary-color: #6a7df3;
            --background-light: #f5f7fa;
            --text-dark: #2c3e50;
            --white: #ffffff;
        }

        body {
            background-color: var(--background-light);
            font-family: 'Inter', 'Poppins', sans-serif;
            color: var(--text-dark);
        }

        .container-appointments {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 15px;
        }

        .card-appointments {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(74, 108, 247, 0.1);
            border: none;
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header-custom h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header-custom i {
            font-size: 1.8rem;
        }

        .table-appointments {
            margin-bottom: 0;
        }

        .table-appointments thead {
            background-color: var(--background-light);
        }

        .table-appointments th {
            color: var(--text-dark);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .table-appointments td {
            vertical-align: middle;
            padding: 15px;
            background-color: var(--white);
            border-bottom: 1px solid #e9ecef;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
            padding: 8px 15px;
        }

        .btn-approve {
            background-color: #28a745;
            color: var(--white);
        }

        .btn-approve:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-reject {
            background-color: #dc3545;
            color: var(--white);
        }

        .btn-reject:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .no-appointments {
            text-align: center;
            padding: 2rem;
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .modal-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
        }

        @media (max-width: 768px) {
            .table-responsive-stack tr {
                display: flex;
                flex-direction: column;
                margin-bottom: 1rem;
                border: 1px solid #e9ecef;
            }
            
            .table-responsive-stack td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #e9ecef;
            }
        }
    </style>
</head>
<body>

<div class="container-appointments">
    <div class="card card-appointments">
        <div class="card-header-custom">
            <h2>
                <i class="bi bi-calendar-check"></i>
                Pending Appointment Requests
            </h2>
        </div>

        <?php if ($result->num_rows > 0) { ?>
            <div class="table-responsive">
                <table class="table table-appointments table-responsive-stack">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Appointment Date</th>
                            <th>Time Slot</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                <td><?php echo $row['appointment_date']; ?></td>
                                <td><?php echo $row['start_time'] . " - " . $row['end_time']; ?></td>
                                <td>
                                    <!-- Approve Button -->
                                    <form method="POST" action="approve_reject_appointment.php" style="display:inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-approve btn-action">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                    </form>

                                    <!-- Reject Button -->
                                    <button type="button" class="btn btn-reject btn-action" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $row['id']; ?>">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>

                                    <!-- Rejection Modal -->
                                    <div class="modal fade" id="rejectModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header modal-header-custom">
                                                    <h5 class="modal-title">Provide Rejection Reason</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="approve_reject_appointment.php">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="rejection_reason" class="form-label">Reason for rejection:</label>
                                                            <textarea class="form-control" name="rejection_reason" id="rejection_reason" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="action" value="reject" class="btn btn-reject">Confirm Rejection</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="no-appointments">
                <p class="mb-0">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    <br>
                    No pending appointments at the moment.
                </p>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>