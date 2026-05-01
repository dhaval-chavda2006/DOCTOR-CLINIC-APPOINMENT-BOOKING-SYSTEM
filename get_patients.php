<?php
// api/get_patients.php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$doctor_id = $_SESSION['user_id'];

try {
    // Get unique patients who have appointments with this doctor
    $stmt = $pdo->prepare("
        SELECT DISTINCT u.id, u.name, u.email, pd.age, pd.gender, pd.contact_number 
        FROM users u 
        JOIN appointments a ON u.id = a.patient_id 
        LEFT JOIN patient_details pd ON u.id = pd.user_id 
        WHERE a.doctor_id = ?
    ");
    $stmt->execute([$doctor_id]);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get today's appointments for the dashboard
    $today = date('Y-m-d');
    $stmt_app = $pdo->prepare("
        SELECT a.id, a.appointment_time, a.status, u.name as patient_name 
        FROM appointments a 
        JOIN users u ON a.patient_id = u.id 
        WHERE a.doctor_id = ? AND a.appointment_date = ?
        ORDER BY a.appointment_time ASC
    ");
    $stmt_app->execute([$doctor_id, $today]);
    $today_appointments = $stmt_app->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'patients' => $patients, 'today_appointments' => $today_appointments]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
