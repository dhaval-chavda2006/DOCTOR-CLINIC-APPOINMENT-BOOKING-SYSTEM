<?php
session_start();
require_once '../includes/db.php';

if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];

    try {
        // Get patient name from users table and history from medical_records
        $sql = "SELECT u.name, m.diagnosis, m.treatment, m.visit_date 
                FROM users u 
                LEFT JOIN medical_records m ON u.id = m.patient_id 
                WHERE u.id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$patient_id]);
        $details = $stmt->fetchAll();

        echo json_encode($details);

    } catch(PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
}
?>