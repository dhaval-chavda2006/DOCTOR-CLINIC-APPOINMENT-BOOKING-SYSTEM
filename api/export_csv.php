<?php
session_start();
// 1. Point to the new shared connection file in the includes folder
require_once '../includes/db.php'; 

// 2. Security: Ensure only a logged-in doctor can export data
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// 3. Set headers to tell the browser this is a CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=patient_list.csv');

// 4. Open the "output" stream
$output = fopen('php://output', 'w');

// 5. Set the column headings (Matching the new table structure)
fputcsv($output, array('ID', 'Name', 'Age', 'Gender', 'Phone'));

try {
    // 6. Merged Logic: JOIN the users table and patient_details table
    // We use $pdo instead of $conn to match includes/db.php
    $query = "SELECT u.id, u.name, pd.age, pd.gender, pd.contact_number 
              FROM users u 
              JOIN patient_details pd ON u.id = pd.user_id 
              WHERE u.role = 'patient'";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
} catch (PDOException $e) {
    // If there is an error, we can't send JSON because headers are already set for CSV
    // But we can log it or stop the process
}

fclose($output);
exit;
?>