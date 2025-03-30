<?php
session_start();
include ('db_connection.php');

// Fetch total counts
$total_doctors_query = "SELECT COUNT(*) as total FROM doctorreg";
$total_patients_query = "SELECT COUNT(*) as total FROM patientreg";
$total_appointments_query = "SELECT COUNT(*) as total FROM appointment_requests";
$pending_appointments_query = "SELECT COUNT(*) as total FROM appointment_requests WHERE status = 'pending'";

$doctors_result = mysqli_query($conn, $total_doctors_query);
$patients_result = mysqli_query($conn, $total_patients_query);
$appointments_result = mysqli_query($conn, $total_appointments_query);
$pending_result = mysqli_query($conn, $pending_appointments_query);

$total_doctors = mysqli_fetch_assoc($doctors_result)['total'];
$total_patients = mysqli_fetch_assoc($patients_result)['total'];
$total_appointments = mysqli_fetch_assoc($appointments_result)['total'];
$pending_appointments = mysqli_fetch_assoc($pending_result)['total'];

// Fetch appointment status distribution
$appointment_status_query = "SELECT status, COUNT(*) as count FROM appointment_requests GROUP BY status";
$appointment_status_result = mysqli_query($conn, $appointment_status_query);

$status_data = [];
while($status = mysqli_fetch_assoc($appointment_status_result)) {
    $status_data[$status['status']] = $status['count'];
}

// Fetch total payments if payment data exists
$payment_query = "SELECT SUM(amount) as total_revenue FROM payments";
$payment_result = mysqli_query($conn, $payment_query);
$total_revenue = mysqli_fetch_assoc($payment_result)['total_revenue'] ?? 0;

// Fetch user details
$users_query = "SELECT id, name, email, phone FROM patientreg";
$users_result = mysqli_query($conn, $users_query);
$users = [];
while ($user = mysqli_fetch_assoc($users_result)) {
    $users[] = $user;
}
$payments_query = "SELECT * FROM payments";
$payments_result = mysqli_query($conn, $payments_query);
$payments = [];
while ($payment = mysqli_fetch_assoc($payments_result)) {
    $payments[] = $payment;
}
// Fetch doctor details
$doctors_query = "SELECT id, name, email, specialization FROM doctorreg";
$doctors_result = mysqli_query($conn, $doctors_query);
$doctors = [];
while ($doctor = mysqli_fetch_assoc($doctors_result)) {
    $doctors[] = $doctor;
}

// Fetch appointment details with patient and doctor names and time from doctor_availability
$appointments_query = "SELECT a.id, p.name as patient_name, d.name as doctor_name, a.appointment_date as date, da.start_time, da.end_time, a.status 
                      FROM appointment_requests a 
                      JOIN patientreg p ON a.user_id = p.id 
                      JOIN doctorreg d ON a.doctor_id = d.id 
                      JOIN doctor_availability da ON a.doctor_id = da.doctor_id";
$appointments_result = mysqli_query($conn, $appointments_query);
$appointments = [];
while ($appointment = mysqli_fetch_assoc($appointments_result)) {
    $appointments[] = $appointment;
}

// Generate printable HTML report
echo "<html><head><title>BookMyDoc Report</title>";
echo "<style>
    body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
    .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
    h1 { color: #333; text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background-color: #007bff; color: white; }
    .summary { text-align: center; margin-top: 20px; }
    .print-btn { display: block; width: 200px; margin: 20px auto; padding: 10px; background: #007bff; color: white; text-align: center; border-radius: 5px; cursor: pointer; }
</style></head><body>";

echo "<div class='container'>";
echo "<h1>BookMyDoc - Admin Report</h1>";
echo "<p class='summary'><strong>Total Doctors:</strong> $total_doctors | <strong>Total Patients:</strong> $total_patients | <strong>Total Appointments:</strong> $total_appointments | <strong>Pending Appointments:</strong> $pending_appointments | <strong>Total Revenue:</strong> $$total_revenue</p>";

echo "<h2>Appointment Status</h2>";
echo "<table><tr><th>Status</th><th>Count</th></tr>";
foreach ($status_data as $status => $count) {
    echo "<tr><td>$status</td><td>$count</td></tr>";
}
echo "</table>";

echo "<h2>User Details</h2>";
echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>";
foreach ($users as $user) {
    echo "<tr><td>{$user['id']}</td><td>{$user['name']}</td><td>{$user['email']}</td><td>{$user['phone']}</td></tr>";
}
echo "</table>";

echo "<h2>Doctor Details</h2>";
echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Specialization</th></tr>";
foreach ($doctors as $doctor) {
    echo "<tr><td>{$doctor['id']}</td><td>{$doctor['name']}</td><td>{$doctor['email']}</td><td>{$doctor['specialization']}</td></tr>";
}
echo "</table>";

echo "<h2>Appointment List</h2>";
echo "<table><tr><th>ID</th><th>Patient Name</th><th>Doctor Name</th><th>Date</th><th>Start Time</th><th>End Time</th><th>Status</th></tr>";
foreach ($appointments as $appointment) {
    echo "<tr><td>{$appointment['id']}</td><td>{$appointment['patient_name']}</td><td>{$appointment['doctor_name']}</td><td>{$appointment['date']}</td><td>{$appointment['start_time']}</td><td>{$appointment['end_time']}</td><td>{$appointment['status']}</td></tr>";
}
echo "</table>";
echo "<h2>Payment Details</h2>";
echo "<table><tr><th>ID</th><th>Appointment ID</th><th>Doctor ID</th><th>Amount</th><th>Payment Method</th><th>Transaction ID</th><th>Order ID</th><th>Payment Date</th><th>Status</th></tr>";
foreach ($payments as $payment) {
    echo "<tr><td>{$payment['id']}</td><td>{$payment['appointment_id']}</td><td>{$payment['doctor_id']}</td><td>{$payment['amount']}</td><td>{$payment['payment_method']}</td><td>{$payment['transaction_id']}</td><td>{$payment['order_id']}</td><td>{$payment['payment_date']}</td><td>{$payment['status']}</td></tr>";
}
echo "</table>";

echo "<button class='print-btn' onclick='window.print()'>Print Report</button>";
echo "</div></body></html>";
?>
