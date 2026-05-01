<?php
// api/get_patient_details.php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$patient_id = $_GET['id'] ?? '';
$doctor_id = $_SESSION['user_id'];

if (empty($patient_id)) {
    echo json_encode(['success' => false, 'message' => 'Patient ID is required.']);
    exit;
}

try {
    // 1. Get basic info
    $stmt_info = $pdo->prepare("
        SELECT u.id, u.name, u.email, pd.age, pd.gender, pd.contact_number, pd.address 
        FROM users u 
        LEFT JOIN patient_details pd ON u.id = pd.user_id 
        WHERE u.id = ? AND u.role = 'patient'
    ");
    $stmt_info->execute([$patient_id]);
    $info = $stmt_info->fetch(PDO::FETCH_ASSOC);

    // 2. Get past appointments with this doctor
    $stmt_app = $pdo->prepare("
        SELECT id, appointment_date, appointment_time, symptoms, status 
        FROM appointments 
        WHERE patient_id = ? AND doctor_id = ? 
        ORDER BY appointment_date DESC
    ");
    $stmt_app->execute([$patient_id, $doctor_id]);
    $appointments = $stmt_app->fetchAll(PDO::FETCH_ASSOC);

    // 3. Get medical records added by this doctor for this patient
    $stmt_med = $pdo->prepare("
        SELECT id, diagnosis, prescription, notes, record_date 
        FROM medical_records 
        WHERE patient_id = ?
        ORDER BY record_date DESC
    ");
    $stmt_med->execute([$patient_id]); // Depending on requirements, we can restrict to records by this doctor, but usually doctors can see all records for a patient. We'll show all for the clinic.
    $records = $stmt_med->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'info' => $info, 'appointments' => $appointments, 'records' => $records]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
