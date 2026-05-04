<?php
// api/add_record.php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

// Prevent caching of the API response
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get and Sanitize data
    $patient_id = $_POST['patient_id'] ?? null;
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $treatment = trim($_POST['treatment'] ?? '');
    $visit_date = date('Y-m-d'); // Automatically set today's date

    // 2. Validate input
    if (empty($patient_id) || empty($diagnosis) || empty($treatment)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
        exit;
    }

    try {
        // 3. Insert into medical_records table
        $sql = "INSERT INTO medical_records (patient_id, diagnosis, treatment, visit_date) 
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$patient_id, $diagnosis, $treatment, $visit_date])) {
            echo json_encode(['status' => 'success', 'message' => 'Record saved successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save record to database.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>