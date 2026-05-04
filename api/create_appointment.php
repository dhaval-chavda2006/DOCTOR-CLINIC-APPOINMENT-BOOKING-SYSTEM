<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$patient_id = $_SESSION['user_id'];
$doctor_id = $_POST['doctor_id'];
$date = $_POST['appointment_date'];
$slot = $_POST['time_slot'];
$symptoms = $_POST['symptoms'] ?? '';

$sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, time_slot, symptoms, status) 
        VALUES (?, ?, ?, ?, ?, 'Scheduled')";
$stmt = $pdo->prepare($sql);

if ($stmt->execute([$patient_id, $doctor_id, $date, $slot, $symptoms])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking failed']);
}
?>