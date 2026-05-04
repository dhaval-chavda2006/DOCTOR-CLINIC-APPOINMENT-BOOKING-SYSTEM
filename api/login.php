<?php
// api/login.php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitize inputs
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? ''); 
    $role = $_POST['role'] ?? '';

    // 2. Basic Validation
    if (empty($email) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    try {
        // 3. Fetch user by email and role
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 4. Verify User Existence
        if (!$user) {
            echo json_encode([
                'success' => false, 
                'message' => "No account found for $email as a $role."
            ]);
            exit;
        }

        // 5. Verify Password
        if (password_verify($password, $user['password'])) {
            // Set Session Data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Determine Redirect URL
            // Ensure these files exist in your root directory!
            $redirectUrl = ($user['role'] === 'doctor') ? 'doctor_dashboard.php' : 'patient_dashboard.html';

            echo json_encode([
                'success' => true, 
                'message' => 'Login successful.',
                'redirect' => $redirectUrl
            ]);
        } else {
            // If you get this, the password in the DB doesn't match 'password123'
            echo json_encode([
                'success' => false, 
                'message' => 'Incorrect password for ' . $user['name'] . '.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>