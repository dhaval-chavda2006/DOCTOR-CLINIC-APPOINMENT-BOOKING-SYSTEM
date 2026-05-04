<?php
session_start();
require_once '../includes/db.php'; 
// Only allow doctors to access this data
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

try {
    // We SELECT from users (u) and JOIN with patient_details (pd)
    // u.id = pd.user_id is the "link" between them
    $sql = "SELECT u.id, u.name, pd.age, pd.gender, pd.contact_number AS phone 
            FROM users u 
            JOIN patient_details pd ON u.id = pd.user_id 
            WHERE u.role = 'patient'";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $patients = $stmt->fetchAll();

    echo json_encode($patients);

} catch(PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>