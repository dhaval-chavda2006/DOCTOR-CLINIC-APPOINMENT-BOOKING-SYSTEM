<?php
// api/register.php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password, $role]);
        $user_id = $pdo->lastInsertId();

        // If patient, save patient details
        if ($role === 'patient') {
            $age = $_POST['age'] ?? null;
            $gender = $_POST['gender'] ?? null;
            $contact_number = $_POST['contact_number'] ?? '';
            $address = $_POST['address'] ?? '';

            $stmt_patient = $pdo->prepare("INSERT INTO patient_details (user_id, age, gender, contact_number, address) VALUES (?, ?, ?, ?, ?)");
            $stmt_patient->execute([$user_id, $age, $gender, $contact_number, $address]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Registration successful.']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
