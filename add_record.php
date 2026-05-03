<?php
// api/add_record.php
require_once '../includes/db.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $patient_id = $_POST['patient_id'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $prescription = $_POST['prescription'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $record_date = date('Y-m-d');
    
    // Optional
    $appointment_id = $_POST['appointment_id'] ?? null;
    if (empty($appointment_id)) $appointment_id = null;

    if (empty($patient_id) || empty($diagnosis) || empty($prescription)) {
        echo json_encode(['success' => false, 'message' => 'Diagnosis and prescription are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, appointment_id, diagnosis, prescription, notes, record_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$patient_id, $appointment_id, $diagnosis, $prescription, $notes, $record_date]);

        // If an appointment ID was passed, mark it as completed
        if ($appointment_id) {
            $stmt_update = $pdo->prepare("UPDATE appointments SET status = 'Completed' WHERE id = ?");
            $stmt_update->execute([$appointment_id]);
        }

        echo json_encode(['success' => true, 'message' => 'Medical record added successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

