<?php
ob_start(); // Start output buffering to catch accidental whitespace
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

if (!$user_id) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    if ($role === 'patient') {
        $sql = "SELECT a.id, u.name as doctor_name, a.appointment_date, a.time_slot, a.status, a.symptoms 
                FROM appointments a
                JOIN users u ON a.doctor_id = u.id 
                WHERE a.patient_id = ?
                ORDER BY a.appointment_date DESC";
    } else {
        $sql = "SELECT a.id, u.name as patient_name, a.appointment_date, a.time_slot, a.status, a.symptoms 
                FROM appointments a
                JOIN users u ON a.patient_id = u.id 
                WHERE a.doctor_id = ?
                ORDER BY a.appointment_date DESC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_clean(); // Clear any accidental notices before sending JSON
    echo json_encode([
        'success' => true,
        'appointments' => $appointments
    ]);

} catch (PDOException $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>



